<?php

namespace App\Controller\Admin;

use App\DTO\RestaurantPrefillDTO;
use App\Entity\Restaurant;
use App\Enum\RestaurantStatus;
use App\GeocodingService;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class RestaurantCrudController extends AbstractCrudController {

    public function __construct(
        private readonly Packages          $assets,
        private readonly RequestStack      $requestStack,
        private readonly CountryRepository $countryRepository,
        private readonly GeocodingService  $geocodingService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string {
        return Restaurant::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->addSelect('c')
            ->leftJoin('entity.country', 'c')
            ->orderBy('CASE WHEN c.name IS NULL THEN 1 ELSE 0 END')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('entity.name', 'ASC');

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setEntityLabelInSingular('Restaurant')
            ->setEntityLabelInPlural('Restaurants')
            ->setPageTitle(Crud::PAGE_INDEX, 'Restaurants')
            ->setPageTitle(Crud::PAGE_EDIT, 'Restaurant bewerken')
            ->setPageTitle(Crud::PAGE_NEW, 'Nieuw restaurant')
            ->setSearchFields(['name', 'country.name', 'city']);
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->disable(Action::SAVE_AND_ADD_ANOTHER, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable {
        yield FormField::addColumn(8);
        yield FormField::addFieldset('Algemene informatie');
        yield TextField::new('name')
            ->setLabel('Naam');
        yield AssociationField::new('country')
            ->setLabel('Land')
            ->renderAsHtml()
            ->setFormTypeOption('choice_label', fn ($country) => $this->renderCountryFlag($country))
            ->formatValue(fn ($value, $entity) => $this->renderCountryFlag($value))
            ->setRequired(false);

        yield FormField::addFieldset('Adres');
        yield TextField::new('street')
            ->hideOnIndex()
            ->setColumns('col-md-8')
            ->setLabel('Straat');
        yield TextField::new('houseNumber')
            ->hideOnIndex()
            ->setColumns('col-md-4')
            ->setLabel('Huisnummer');
        yield TextField::new('postalCode')
            ->hideOnIndex()
            ->setColumns('col-md-4')
            ->setLabel('Postcode');
        yield TextField::new('city')
            ->setColumns('col-md-8')
            ->setLabel('Plaats');

        yield FormField::addFieldset('Contactinformatie');
        yield TextField::new('website')
            ->setColumns('col-md-8')
            ->setLabel('Website')
            ->setHelp('Vul de volledige URL in, inclusief https://. Bijvoorbeeld: https://www.example.com');

        yield FormField::addColumn(4);
        yield FormField::addFieldset('Instellingen');
        yield ChoiceField::new('status')
            ->setChoices(RestaurantStatus::choiceList())
            ->renderAsBadges([
                RestaurantStatus::OPEN->value => 'success',
                RestaurantStatus::CLOSED->value => 'secondary',
            ]);

        $locationFieldSet = FormField::addFieldset('Locatie');
        if ($pageName === Crud::PAGE_NEW) {
            $locationFieldSet->setHelp('Bij een nieuw restaurant worden de coördinaten automatisch bepaald op basis van het adres. Vul het adres in en klik op "Aanmaken" om de coördinaten te bepalen.');
        }
        yield $locationFieldSet;
        yield NumberField::new('latitude')
            ->hideOnIndex()
            ->setNumDecimals(6)
            ->setRequired(false) // Only false because we run geocoding automatically if not set
            ->setLabel('Breedtegraad (Latitude)');
        yield NumberField::new('longitude')
            ->hideOnIndex()
            ->setNumDecimals(6)
            ->setRequired(false) // Only false because we run geocoding automatically if not set
            ->setLabel('Lengtegraad (Longitude)');
    }

    // Prefill the entity with data from the request if available, the user is likely coming from a Restaurant suggestion if the data is present.
    // If no data is available, fall back to the default entity creation.
    public function createEntity(string $entityFqcn): Restaurant {
        $request = $this->requestStack->getCurrentRequest();
        $restaurantPrefillDTO = $request ? RestaurantPrefillDTO::fromRequest($request) : null;

        if ($restaurantPrefillDTO->hasAnyValue() === false) {
            return parent::createEntity($entityFqcn);
        }

        $restaurant = new Restaurant();
        $restaurantPrefillDTO->applyTo($restaurant, $this->countryRepository);

        return $restaurant;
    }

    /**
     * Persist the entity and automatically geocode it if latitude and longitude are not set.
     * This method is called when a new Restaurant entity is created or an existing one is updated.
     *
     * @param EntityManagerInterface $entityManager
     * @param Restaurant $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void {
        if (!$entityInstance instanceof Restaurant) {
            throw new \InvalidArgumentException('Expected instance of Restaurant');
        }

        if ($entityInstance->getLatitude() !== null && $entityInstance->getLongitude() !== null) {
            // If latitude and longitude are set, we assume they are correct and do not geocode again.
            // This is useful for cases where the user manually sets the coordinates.
            return;
        }

        if ($entityInstance->getStreet() === null || $entityInstance->getHouseNumber() === null || $entityInstance->getPostalCode() === null || $entityInstance->getCity() === null) {
            // If any of the address fields are missing, we can technically geocode but to save requests and avoid errors, we skip geocoding.
            $this->addFlash('warning', 'Vul het volledige adres in om de coördinaten automatisch te bepalen.');
            return;
        }

        // Automatically geocode the restaurant if latitude and longitude are not set
        $geocodedData = $this->geocodingService->geocodeFromRestaurant($entityInstance);

        if (!$geocodedData) {
            $this->addFlash('warning', 'De coördinaten konden niet automatisch worden bepaald. Vul deze handmatig in of controleer het adres.');
            return;
        }

        $entityInstance->setLatitude($geocodedData['lat']);
        $entityInstance->setLongitude($geocodedData['lon']);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse {
        // After creating, redirect to the edit page for the new restaurant
        $restaurant = $context->getEntity()->getInstance();
        if ($action === Crud::PAGE_NEW && $restaurant instanceof Restaurant) {
            $url = $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Crud::PAGE_EDIT)
                ->setEntityId($restaurant->getId())
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    private function renderCountryFlag(?object $country): string {
        if (!$country) {
            return 'Niets';
        }

        $name = $country->getName();
        $flagUrl = $this->assets->getUrl("build/images/flags/{$country->getFlag()}");

        return sprintf(
            '<img src="%s" alt="%s" style="width: 20px; height: 14px; border: 1px solid #ccc; margin-right: 5px;" />%s',
            $flagUrl,
            htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
    }
}
