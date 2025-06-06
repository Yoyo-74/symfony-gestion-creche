<?php

namespace App\Controller;

use App\Repository\ChildsRepository;
use App\Repository\CalendarChildsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardAdminController extends AbstractController
{
    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    public function index(
        ChildsRepository $childsRepository,
        CalendarChildsRepository $calendarChildsRepository
    ): Response
    {
        // Obtenir la date du jour
        $today = new \DateTime();

        // Récupérer les statistiques
        $stats = [
            'totalEnfants' => $childsRepository->count([]),
            'enfantsAujourdhui' => $calendarChildsRepository->countChildrenForDate($today),
            'enfantsPresents' => $calendarChildsRepository->countPresentChildren($today),
            'repasAPrevoir' => $calendarChildsRepository->countMealsForDate($today),
        ];

        return $this->render('dashboard_admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}