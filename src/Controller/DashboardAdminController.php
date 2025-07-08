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

        // Obtenir les enfants prévus aujourd'hui
        $enfantsAujourdhui = $calendarChildsRepository->findByDate($today);

        // Trouver les anniversaires parmi eux
        $anniversaires = [];
        foreach ($enfantsAujourdhui as $calendarChild) {
            $child = $calendarChild->getChild();
            if ($child && $child->getDateNaissance() && $child->getDateNaissance()->format('m-d') === $today->format('m-d')) {
                $anniversaires[] = $child->getPrenom();
            }
        }
        $stats['nbrAnniversaires'] = count($anniversaires);
        $stats['prenomsAnniversaires'] = $anniversaires;
        $startOfYear = (clone $today)->setDate($today->format('Y'), 1, 1)->setTime(0,0,0);
        $stats['heuresAnnee'] = $calendarChildsRepository->sumPresenceHoursForPeriod($startOfYear, $today);
        $startOfMonth = (clone $today)->setDate($today->format('Y'), $today->format('m'), 1)->setTime(0,0,0);
        $stats['heuresMois'] = $calendarChildsRepository->sumPresenceHoursForPeriod($startOfMonth, $today);
        $startOfWeek = (clone $today)->modify('monday this week')->setTime(0,0,0);
        $endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23,59,59);
        $stats['heuresSemainePrevues'] = $calendarChildsRepository->sumPlannedHoursForPeriod($startOfWeek, $endOfWeek);

        return $this->render('dashboard_admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}