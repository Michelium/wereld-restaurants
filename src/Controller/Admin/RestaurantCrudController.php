<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Asset\Packages;

class RestaurantCrudController extends AbstractCrudController {

    public function __construct(
        private readonly Packages $assets,
    ) {
    }

    public static function getEntityFqcn(): string {
        return Restaurant::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->addSelect('country')
            ->leftJoin('entity.country', 'country')
            ->orderBy('CASE WHEN country.name IS NULL THEN 1 ELSE 0 END') // Null values last
            ->addOrderBy('country.name', 'ASC')
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

    public function configureFields(string $pageName): iterable {
        yield FormField::addColumn(8);
        yield FormField::addFieldset('Algemene informatie');
        yield TextField::new('name')
            ->setLabel('Naam');
        yield AssociationField::new('country')
            ->setLabel('Land')
            ->formatValue(function ($value, $entity) {
                if (!$value) return 'Niets';

                $name = $value->getName();
                $flagUrl = $this->assets->getUrl("build/images/flags/{$value->getFlag()}");

                return sprintf('<img src="%s" alt="%s" style="width: 20px; height: 14px; border: 1px solid #ccc; margin-right: 5px;" />%s',$flagUrl, $name, $name);})
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

        yield FormField::addColumn(4);
        yield FormField::addFieldset('Locatie');
        yield NumberField::new('latitude')
            ->hideOnIndex()
            ->setNumDecimals(6)
            ->setLabel('Breedtegraad (Latitude)');
        yield NumberField::new('longitude')
            ->hideOnIndex()
            ->setNumDecimals(6)
            ->setLabel('Lengtegraad (Longitude)');


    }
}
