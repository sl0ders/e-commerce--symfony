<?php

namespace App\Repository;

use App\Entity\ReportMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportMessage[]    findAll()
 * @method ReportMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportMessage::class);
    }

    // /**
    //  * @return ReportMessage[] Returns an array of ReportMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReportMessage
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
