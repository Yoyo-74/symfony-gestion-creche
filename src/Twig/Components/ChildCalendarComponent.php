<?php

namespace App\Twig\Components;

use App\Entity\Childs;
use App\Entity\CalendarChilds;
use App\Repository\CalendarChildsRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;

#[AsLiveComponent('child_calendar_component')]
final class ChildCalendarComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?int $childId = null;

    private CalendarChildsRepository $calendarChildsRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CalendarChildsRepository $calendarChildsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->calendarChildsRepository = $calendarChildsRepository;
        $this->entityManager = $entityManager;
    }

    #[LiveProp(writable: true)]
    public function getEvents(): array
    {
        return $this->loadEvents();
    }

    private function loadEvents(): array
    {
        if (!$this->childId) {
            return [];
        }

        // Définir la plage de dates (1 mois avant et après la date actuelle)
        $start = new \DateTime('first day of last month');
        $end = new \DateTime('last day of next month');

        // Debug: Afficher la plage de dates
        error_log(sprintf(
            'Loading events for childId %d from %s to %s',
            $this->childId,
            $start->format('Y-m-d'),
            $end->format('Y-m-d')
        ));

        // Utiliser la méthode du repository avec plage de dates
        $calendarChilds = $this->calendarChildsRepository->findByChildAndDateRange(
            $this->childId,
            $start,
            $end
        );

        // Debug: Afficher le nombre d'événements trouvés
        error_log(sprintf('Found %d calendar entries', count($calendarChilds)));

        $events = [];
        foreach ($calendarChilds as $calendarChild) {
            try {
                $date = $calendarChild->getDate();
                if (!$date) {
                    error_log(sprintf('Skip - No date for entry %d', $calendarChild->getId()));
                    continue;
                }

                $heureArrivee = $calendarChild->getHeureArrivee();
                $heureDepart = $calendarChild->getHeureDepart();
                if (!$heureArrivee || !$heureDepart) {
                    error_log(sprintf('Skip - Missing times for entry %d', $calendarChild->getId()));
                    continue;
                }

                // Créer des DateTime pour le début et la fin
                $startDateTime = (clone $date)->setTime(
                    (int)$heureArrivee->format('H'),
                    (int)$heureArrivee->format('i')
                );

                $endDateTime = (clone $date)->setTime(
                    (int)$heureDepart->format('H'),
                    (int)$heureDepart->format('i')
                );

                $event = [
                    'id' => $calendarChild->getId(),
                    'title' => sprintf(
                        '%s - %s',
                        $heureArrivee->format('H:i'),
                        $heureDepart->format('H:i')
                    ),
                    'start' => $startDateTime->format('Y-m-d\TH:i:s'),
                    'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => $calendarChild->ispresent() ? '#28a745' : '#ffc107',
                    'borderColor' => $calendarChild->ispresent() ? '#28a745' : '#ffc107',
                    'allDay' => false,
                    'extendedProps' => [
                        'childId' => $this->childId,
                        'isPresent' => $calendarChild->ispresent()
                    ]
                ];

                $events[] = $event;
                error_log(sprintf('Created event for date %s', $date->format('Y-m-d')));
            } catch (\Exception $e) {
                error_log(sprintf('Error creating event: %s', $e->getMessage()));
            }
        }

        return $events;
    }

    public function updatePresence(int $eventId, bool $isPresent): void
    {
        try {
            $this->calendarChildsRepository->updatePresence($eventId, $isPresent);
        } catch (\Exception $e) {
            error_log(sprintf('Error updating presence: %s', $e->getMessage()));
        }
    }

    public function updateEvent(int $eventId, \DateTime $start, \DateTime $end): void
    {
        $calendarChild = $this->calendarChildsRepository->find($eventId);
        if ($calendarChild) {
            try {
                $date = new \DateTime($start->format('Y-m-d'));
                $calendarChild->setDate($date);
                
                $heureArrivee = new \DateTime();
                $heureArrivee->setTime(
                    (int)$start->format('H'),
                    (int)$start->format('i')
                );
                
                $heureDepart = new \DateTime();
                $heureDepart->setTime(
                    (int)$end->format('H'),
                    (int)$end->format('i')
                );
                
                $calendarChild->setHeureArrivee($heureArrivee);
                $calendarChild->setHeureDepart($heureDepart);
                
                $this->entityManager->flush();
            } catch (\Exception $e) {
                error_log(sprintf('Error updating event: %s', $e->getMessage()));
            }
        }
    }
} 