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
        return $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.child)')
            ->join('c.idcalendar', 'cal')
            ->where('c.date = :date')
            ->andWhere('c.ispresent = true')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();
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
}
