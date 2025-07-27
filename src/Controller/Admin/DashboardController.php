<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController {

    public function index(): Response {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(RestaurantCrudController::class)->generateUrl());
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
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Restaurants');
        yield MenuItem::linkToCrud('Restaurants', 'fa fa-utensils', Restaurant::class);

        yield MenuItem::section('Instellingen');
        yield MenuItem::linkToCrud('Gebruikers', 'fa fa-users', User::class);

    }
}
