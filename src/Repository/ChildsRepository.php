<?php

namespace App\Repository;

use App\Entity\Childs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Childs>
 */
class ChildsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Childs::class);
    }

    //    /**
    //     * @return Childs[] Returns an array of Childs objects
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

    //    public function findOneBySomeField($value): ?Childs
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    
    public function findWithResponsables(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.responsables', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    public function findWithFullDetails(int $id): ?Childs
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.responsables', 'r')
            ->leftJoin('c.calendarChilds', 'cc')
            ->addSelect('r')
            ->addSelect('cc')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
