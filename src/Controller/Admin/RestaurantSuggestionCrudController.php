<?php

namespace App\Controller\Admin;

use App\Entity\RestaurantSuggestion;
use App\Enum\RestaurantSuggestionStatus;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestaurantSuggestionCrudController extends AbstractCrudController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator      $adminUrlGenerator
    ) {
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
            yield ChoiceField::new('status')
                ->setChoices(RestaurantSuggestionStatus::cases())
                ->renderAsBadges([
                    RestaurantSuggestionStatus::PENDING->value => 'warning',
                    RestaurantSuggestionStatus::APPROVED->value => 'success',
                    RestaurantSuggestionStatus::REJECTED->value => 'danger',
                ]);
        }
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    #[Route('/admin/restaurant-suggestion/{id}/approve', name: 'admin_restaurant_suggestion_approve')]
    public function approve(Request $request, RestaurantSuggestion $suggestion): Response {
        $suggestion->setStatus(RestaurantSuggestionStatus::APPROVED);

        // todo : Create or update the restaurant entity based on the suggestion fields

        $this->entityManager->persist($suggestion);
        $this->entityManager->flush();

        $this->addFlash('success', 'Suggestie goedgekeurd');
        return $this->redirect($this->adminUrlGenerator->setController(RestaurantSuggestionCrudController::class)->generateUrl());
    }

    #[Route('/admin/restaurant-suggestion/{id}/reject', name: 'admin_restaurant_suggestion_reject')]
    public function reject(Request $request, RestaurantSuggestion $suggestion): Response {
        $suggestion->setStatus(RestaurantSuggestionStatus::REJECTED);

        $this->entityManager->persist($suggestion);
        $this->entityManager->flush();

        $this->addFlash('warning', 'Suggestie afgewezen');
        return $this->redirect($this->adminUrlGenerator->setController(RestaurantSuggestionCrudController::class)->generateUrl());
    }

}
