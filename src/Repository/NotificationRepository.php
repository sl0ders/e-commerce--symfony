<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * this function retrieve the notification of the users in an company
     * @param $user
     * @return mixed
     */
    public function findByUser($user): mixed
    {
        $qb = $this->createQueryBuilder("n")
            ->leftJoin("n.sender", "user")
            ->andWhere("user = :user")
            ->setParameter("user", $user);
        return $qb->getQuery()->getResult();
    }
}
