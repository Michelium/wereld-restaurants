<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Entity\RestaurantSuggestion;
use App\Entity\User;
use App\Enum\RestaurantSuggestionStatus;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantSuggestionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController {

    public function __construct(
        private readonly RestaurantSuggestionRepository $restaurantSuggestionRepository,
        private readonly RestaurantRepository           $restaurantRepository,
    ) {
    }

    public function index(): Response {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(RestaurantSuggestionCrudController::class)->generateUrl());
    }

    public function configureCrud(): Crud {
        return Crud::new()
            ->renderContentMaximized()
            ->showEntityActionsInlined();
    }

    public function configureDashboard(): Dashboard {
        return Dashboard::new()
            ->setTitle('Wereld Restaurants')
            ->renderContentMaximized()
            ->disableDarkMode();
    }

    public function configureMenuItems(): iterable {
        yield MenuItem::linkToRoute('Terug naar de website', 'fa fa-home', 'app_index');

        yield MenuItem::section('Beheer');

        $numberOfPendingSuggestions = $this->restaurantSuggestionRepository->count(['status' => RestaurantSuggestionStatus::PENDING]);
        yield MenuItem::linkToCrud('Suggesties', 'fa fa-lightbulb', RestaurantSuggestion::class)
            ->setBadge($numberOfPendingSuggestions > 0 ? (string)$numberOfPendingSuggestions : '0', $numberOfPendingSuggestions > 0 ? 'danger' : 'success');

        yield MenuItem::section('Restaurants');
        $numberOfRestaurants = $this->restaurantRepository->count();
        yield MenuItem::linkToCrud('Restaurants', 'fa fa-utensils', Restaurant::class)
            ->setBadge($numberOfRestaurants > 0 ? (string)$numberOfRestaurants : '0', 'info');

        yield MenuItem::section('Instellingen');
        yield MenuItem::linkToCrud('Gebruikers', 'fa fa-users', User::class);

    }
}
