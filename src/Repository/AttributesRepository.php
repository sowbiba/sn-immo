<?php

namespace App\Repository;

use App\Entity\Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attribute[]    findAll()
 * @method Attribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    /**
     * @param array $criterias
     * @param array|string[] $sortOptions
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(array $criterias = [], array $sortOptions = ['id' => 'DESC']): QueryBuilder
    {
        $qb = $this->createQueryBuilder('attributes')
            ->where('attributes.id IS NOT NULL')
        ;
        foreach ($criterias as $criteria => $value) {
            $qb = $qb->andWhere("attributes.$criteria LIKE '%$value%'");
        }

        $sortField = array_keys($sortOptions)[0];
        $sortDirection = array_values($sortOptions)[0];

        $qb = $qb->orderBy("attributes.$sortField", $sortDirection);

        return $qb;
    }
}
