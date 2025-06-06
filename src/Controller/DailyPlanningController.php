<?php

namespace App\Controller;

use App\Repository\CalendarChildsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/planning')]
class DailyPlanningController extends AbstractController
{
    #[Route('/daily/{date}', name: 'app_daily_planning', defaults: ['date' => null])]
    public function daily(?string $date, CalendarChildsRepository $repository): Response
    {
        // Gérer la date (aujourd'hui par défaut ou date passée en paramètre)
        if ($date === null) {
            $currentDate = new \DateTime();
        } else {
            $currentDate = \DateTime::createFromFormat('Y-m-d', $date);
            if (!$currentDate) {
                throw $this->createNotFoundException('Date invalide');
            }
        }

        // Calculer les dates précédente et suivante
        $previousDate = (clone $currentDate)->modify('-1 day');
        $nextDate = (clone $currentDate)->modify('+1 day');

        // Récupérer les présences du jour
        $presences = $repository->findByDate($currentDate);

        return $this->render('daily_planning/index.html.twig', [
            'current_date' => $currentDate,
            'previous_date' => $previousDate,
            'next_date' => $nextDate,
            'presences' => $presences,
        ]);
    }
}