<?php

namespace App\Repository;

use App\Entity\PropertyAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertyAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyAttachment[]    findAll()
 * @method PropertyAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyAttachmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyAttachment::class);
    }

    // /**
    //  * @return PropertyAttachment[] Returns an array of PropertyAttachment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PropertyAttachment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
