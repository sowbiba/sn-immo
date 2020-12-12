<?php

namespace App\Manager;

use App\Entity\Attribute;
use App\Entity\PropertyAttribute;
use Doctrine\ORM\EntityManagerInterface;

class PropertyAttributesManager extends AbstractObjectManager
{
    const PAGE_LIMIT = 10;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(PropertyAttribute::class);
        parent::__construct($entityManager);
    }

    public function isAttributeLinkedToAProperty(Attribute $attribute): bool
    {
        return $this->repository->isAttributeLinkedToAProperty($attribute);
    }
}
