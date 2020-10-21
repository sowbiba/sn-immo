<?php

namespace App\Manager;

use App\Entity\Attribute;
use Doctrine\ORM\EntityManagerInterface;

class AttributesManager extends AbstractObjectManager
{
    const PAGE_LIMIT = 10;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Attribute::class);
        parent::__construct($entityManager);
    }
}
