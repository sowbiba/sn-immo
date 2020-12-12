<?php

namespace App\Manager;

use App\Entity\Advertising;
use Doctrine\ORM\EntityManagerInterface;

class AdvertisingManager extends AbstractObjectManager
{
    const PAGE_LIMIT = 10;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Advertising::class);
        parent::__construct($entityManager);
    }
}
