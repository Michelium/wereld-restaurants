<?php

namespace App\Controller\Admin;

use App\DTO\RestaurantPrefillDTO;
use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantSuggestionStatus;
use App\Enum\RestaurantSuggestionType;
use App\Service\RestaurantSuggestionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestaurantSuggestionCrudController extends AbstractCrudController {
    public function __construct(
        private readonly AdminUrlGenerator      $adminUrlGenerator,
        private readonly RestaurantSuggestionService $restaurantSuggestionService
    ) {
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->andWhere('entity.status = :status')
            ->setParameter('status', RestaurantSuggestionStatus::PENDING);

        return $queryBuilder;
    }

    public static function getEntityFqcn(): string {
        return RestaurantSuggestion::class;
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->overrideTemplates([
                'crud/detail' => 'admin/restaurant_suggestion/detail.html.twig',
            ])
            ->setEntityLabelInSingular('Suggestie')
            ->setEntityLabelInPlural('Suggesties')
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Restaurant Suggesties');
    }

    public function configureFields(string $pageName): iterable {
        if ($pageName === Crud::PAGE_DETAIL) {
            yield IdField::new('id')->onlyOnIndex();

            yield AssociationField::new('restaurant')
                ->formatValue(fn($v, $e) => $v?->getName() ?? 'Nieuw restaurant');
            yield DateTimeField::new('createdAt')
                ->setFormat('dd-MM-yyyy HH:mm')
                ->setLabel('Ingediend op');
        } else {
            yield IdField::new('id');
            yield AssociationField::new('restaurant');
            yield TextField::new('fields[name]')
                ->setLabel('(Voorgestelde) naam');
            yield ChoiceField::new('status')
                ->setChoices(RestaurantSuggestionStatus::cases())
                ->renderAsBadges([
                    RestaurantSuggestionStatus::PENDING->value => 'warning',
                    RestaurantSuggestionStatus::APPROVED->value => 'success',
                    RestaurantSuggestionStatus::REJECTED->value => 'danger',
                ]);
            yield ChoiceField::new('type')
                ->setLabel('Type suggestie')
                ->setChoices(RestaurantSuggestionType::cases())
                ->renderAsBadges([
                    RestaurantSuggestionType::FIELDS->value => 'info',
                    RestaurantSuggestionType::CLOSED->value => 'secondary',
                    RestaurantSuggestionType::NEW->value => 'success',
                ]);
            yield DateTimeField::new('createdAt')
                ->setFormat('dd-MM-yyyy HH:mm')
                ->setLabel('Ingediend op');
        }
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    #[Route('/admin/restaurant-suggestion/{id}/approve', name: 'admin_restaurant_suggestion_approve')]
    public function approve(RestaurantSuggestion $suggestion): Response {
        $this->restaurantSuggestionService->approveSuggestion($suggestion);

        if ($suggestion->getType() === RestaurantSuggestionType::NEW) {
            $prefillDto = RestaurantPrefillDTO::fromFields($suggestion->getFields());

            $url = $this->adminUrlGenerator
                ->setController(RestaurantCrudController::class)
                ->setAction(Action::NEW)
                ->setAll($prefillDto->toArray())
                ->generateUrl();

            return $this->redirect($url);
        }

        $this->addFlash('success', 'Suggestie goedgekeurd');
        return $this->redirect($this->adminUrlGenerator->setController(RestaurantSuggestionCrudController::class)->generateUrl());
    }

    #[Route('/admin/restaurant-suggestion/{id}/reject', name: 'admin_restaurant_suggestion_reject')]
    public function reject(RestaurantSuggestion $suggestion): Response {
        $this->restaurantSuggestionService->rejectSuggestion($suggestion);

        $this->addFlash('warning', 'Suggestie afgewezen');
        return $this->redirect($this->adminUrlGenerator->setController(RestaurantSuggestionCrudController::class)->generateUrl());
    }

}
