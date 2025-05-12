<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Mattch;
use App\Entity\Ticket;
use App\Entity\Stade;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Rediriger directement vers la liste des utilisateurs
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator
            ->setController(UserCrudController::class)
            ->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ticket Match');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Gestion des utilisateurs
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        // Gestion des matchs
        yield MenuItem::linkToCrud('Matchs', 'fas fa-futbol', Mattch::class);

        // Gestion des tickets
        yield MenuItem::linkToCrud('Tickets', 'fas fa-ticket-alt', Ticket::class);

        // Gestion des stades
        yield MenuItem::linkToCrud('Stades', 'fas fa-building', Stade::class);

        // Lien vers la page d'accueil du site
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_home');
    }
}
