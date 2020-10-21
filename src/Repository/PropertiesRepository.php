<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param array $criterias
     * @param array|string[] $sortOptions
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(array $criterias = [], array $sortOptions = ['id' => 'DESC']): QueryBuilder
    {
        $qb = $this->createQueryBuilder('properties')
            ->where('properties.id IS NOT NULL')
        ;
        foreach ($criterias as $criteria => $value) {
            $qb = $qb->andWhere("properties.$criteria LIKE '%$value%'");
        }

        $sortField = array_keys($sortOptions)[0];
        $sortDirection = array_values($sortOptions)[0];

        $qb = $qb->orderBy("properties.$sortField", $sortDirection);

        return $qb;
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
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
    public function findOneBySomeField($value): ?Property
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
