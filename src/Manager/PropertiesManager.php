<?php

namespace App\Manager;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;

class PropertiesManager extends AbstractObjectManager
{
    const PAGE_LIMIT = 10;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Property::class);
        parent::__construct($entityManager);
    }
}
