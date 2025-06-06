<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Entity\Childs;
use App\Entity\Calendar;
use App\Entity\CalendarChilds;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;  // Add this line
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony Gestion Creche')
            ->renderContentMaximized();

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Administration', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToCrud('Planning', 'fas fa-list', CalendarChilds::class);
        yield MenuItem::linkToCrud('Enfants', 'fas fa-list', Childs::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', Users::class);
        yield MenuItem::linkToCrud('Calendrier', 'fas fa-list', Calendar::class);
        // Ajout de la page personnalisée dans le menu
        yield MenuItem::linkToRoute('Page personnalisée', 'fa fa-file', 'custom_page');
        yield MenuItem::linkToRoute('Formulaire personnalisé', 'fa fa-file', 'custom_form');
    
    }

    #[Route('/admin/custom-page', name: 'custom_page')]
    public function customPage(): Response
    {
        return $this->render('admin/custom_page.html.twig', [
            'message' => 'Bienvenue sur la page personnalisée !'
        ]);
    }

}
