<?php

namespace App\Repository;

use App\Entity\CalendarChilds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarChilds>
 */
class CalendarChildsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarChilds::class);
    }

    //    /**
    //     * @return CalendarChilds[] Returns an array of CalendarChilds objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CalendarChilds
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function countChildrenForDate(\DateTime $date): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPresentChildren(\DateTime $date): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.date = :date')
            ->andWhere('c.ispresent = true')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countMealsForDate(\DateTime $date): int
    {
        // Plage horaire du repas sur la même date que $date
        $repasStart = clone $date;
        $repasStart->setTime(12, 0);

        $repasEnd = clone $date;
        $repasEnd->setTime(13, 30);

        // Requête Doctrine
        $qb = $this->createQueryBuilder('c')
            ->where('c.date = :date')
            ->andWhere('c.heure_arrivee < :repasEnd')
            ->andWhere('c.heure_depart > :repasStart')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('repasStart', '12:00:00') // format TIME
            ->setParameter('repasEnd', '13:30:00');  // format TIME

        $results = $qb->getQuery()->getResult();
        $count = 0;

        foreach ($results as $calendarChild) {
            $arrivee = $calendarChild->getHeureArrivee();
            $depart = $calendarChild->getHeureDepart();

            // Injecte la bonne date pour pouvoir faire un diff réaliste
            $arrivee->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $depart->setDate($date->format('Y'), $date->format('m'), $date->format('d'));

            // Calcul de la présence réelle pendant la plage de repas
            $startPresence = max($arrivee, $repasStart);
            $endPresence = min($depart, $repasEnd);
            $durationSeconds = max(0, $endPresence->getTimestamp() - $startPresence->getTimestamp());

            if ($durationSeconds >= 3600) {
                $count++;
            }
        }

        return $count;
    }

    
    public function findByChildAndDateRange(int $childId, \DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('cc')
            ->where('cc.child = :childId')
            ->andWhere('cc.date BETWEEN :start AND :end')
            ->setParameter('childId', $childId)
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('cc.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function deleteByChildAndDateRange(int $childId, \DateTime $start, \DateTime $end): void
    {
        $this->createQueryBuilder('cc')
            ->delete()
            ->where('cc.child = :childId')
            ->andWhere('cc.date BETWEEN :start AND :end')
            ->setParameter('childId', $childId)
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getQuery()
            ->execute();
    }

    public function updatePresence(int $id, bool $presence): void
    {
        $this->createQueryBuilder('cc')
            ->update()
            ->set('cc.ispresent', ':presence')
            ->where('cc.id = :id')
            ->setParameter('presence', $presence)
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function findByDate(\DateTime $date): array
    {
        return $this->createQueryBuilder('cc')
            ->select('cc', 'c')
            ->join('cc.child', 'c')
            ->where('cc.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('cc.heure_arrivee', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function sumPresenceHoursForPeriod(\DateTime $start, \DateTime $end): float
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.date BETWEEN :start AND :end')
            ->andWhere('c.ispresent = true')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'));

        $results = $qb->getQuery()->getResult();
        $totalSeconds = 0;
        foreach ($results as $calendarChild) {
            $arrivee = $calendarChild->getHeureArrivee();
            $depart = $calendarChild->getHeureDepart();
            if ($arrivee && $depart) {
                // On force la date pour éviter les erreurs de diff
                $arrivee->setDate(2000, 1, 1);
                $depart->setDate(2000, 1, 1);
                $totalSeconds += max(0, $depart->getTimestamp() - $arrivee->getTimestamp());
            }
        }
        return round($totalSeconds / 3600, 2); // en heures
    }
    public function sumPlannedHoursForPeriod(\DateTime $start, \DateTime $end): float
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.date BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'));

        $results = $qb->getQuery()->getResult();
        $totalSeconds = 0;
        foreach ($results as $calendarChild) {
            $arrivee = $calendarChild->getHeureArrivee();
            $depart = $calendarChild->getHeureDepart();
            if ($arrivee && $depart) {
                $arrivee->setDate(2000, 1, 1);
                $depart->setDate(2000, 1, 1);
                $totalSeconds += max(0, $depart->getTimestamp() - $arrivee->getTimestamp());
            }
        }
        return round($totalSeconds / 3600, 2); // en heures
    }
}
