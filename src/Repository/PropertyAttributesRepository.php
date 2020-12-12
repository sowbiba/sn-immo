<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\PropertyAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    /**
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function isAttributeLinkedToAProperty(Attribute $attribute): bool
    {
        $qb = $this->createQueryBuilder('property_attributes');
        $qb->select($qb->expr()->count('property_attributes'))
            ->where('property_attributes.attribute = ' . $attribute->getId())
            ;

        try {
            return $qb->getQuery()->getSingleScalarResult() > 0;
        } catch (NoResultException $e) {
            return false;
        } catch (NonUniqueResultException $e) {
            return true;
        }
    }
}
