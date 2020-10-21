<?php

namespace App\Manager;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class AbstractObjectManager
{
    const PAGE_LIMIT = 10;

    /** @var ServiceEntityRepository */
    protected $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save($object): void
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    public function delete($object)
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }

    public function search(array $criterias = [], $sortOptions = ['id' => 'DESC'], $page = 1): Pagerfanta
    {
        $qb = $this->repository->getSearchQueryBuilder($criterias, $sortOptions);

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(self::PAGE_LIMIT);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }
}
