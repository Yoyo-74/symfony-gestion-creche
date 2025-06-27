<?php

namespace App\Controller;

use DateTime;
use Exception;
use DateTimeZone;
use App\Entity\CalendarChilds;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CalendarChildsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/update-presence/{id}', name: 'app_update_presence', methods: ['POST'])]
    public function updatePresence(
        int $id, 
        Request $request, 
        EntityManagerInterface $entityManager,
        CalendarChildsRepository $repository
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $isPresent = $data['isPresent'] ?? false;
            
            $calendarChild = $repository->find($id);
            if (!$calendarChild) {
                throw new \Exception('Présence non trouvée');
            }

            $calendarChild->setIspresent($isPresent);
            $entityManager->flush();
            
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()], 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


#[Route('/day', name: 'app_planning_day', methods: ['GET'])]
    public function planningDay(
        Request $request,
        CalendarRepository $calendarRepository,
        CalendarChildsRepository $planningRepository
    ): Response {
        $dateString = $request->query->get('date');
        dump($dateString);
        $date = $dateString ? new DateTime($dateString) : new \DateTime();
        $date->setTimezone(new DateTimeZone('Europe/Paris'));

        // Récupérer la semaine en cours
        $weekStart = clone $date;
        $weekStart->modify('monday this week');
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+4 days');
        dump($weekEnd);
        $weekDays = $calendarRepository->createQueryBuilder('c')
            ->where('c.date >= :start')
            ->andWhere('c.date <= :end')
            ->setParameter('start', $weekStart)
            ->setParameter('end', $weekEnd)
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();

        // Récupérer le nombre d'enfants pour chaque jour
        $childrenCounts = [];
        foreach ($weekDays as $day) {
            $dayDate = $day->getDate();
            $childrenCounts[$dayDate->format('Y-m-d')] = $planningRepository->countChildrenForDate($dayDate);
        }

        // Récupérer les présences du jour
        $presences = $planningRepository->findByDate($date);

        return $this->render('daily_planning/index.html.twig', [
            'date' => $date,
            'weekDays' => $weekDays,
            'presences' => $presences,
            'childrenCounts' => $childrenCounts,
        ]);
    }





    
}