<?php

namespace App\Repository;

use App\Entity\PropertyAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertyAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyAttribute[]    findAll()
 * @method PropertyAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyAttributesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyAttribute::class);
    }

    // /**
    //  * @return PropertyAttribute[] Returns an array of PropertyAttribute objects
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
    public function findOneBySomeField($value): ?PropertyAttribute
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
