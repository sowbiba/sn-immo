<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributesRepository")
 * @ORM\Table(name="attributes")
 */
class Attribute
{
    public const TYPE_STRING = 'string';
    public const TYPE_NUMERIC = 'numeric';
    public const TYPE_AMOUNT = 'amount';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_CHOICE = 'choice';

    public const ALLOWED_TYPES = [
        self::TYPE_STRING,
        self::TYPE_NUMERIC,
        self::TYPE_AMOUNT,
        self::TYPE_BOOLEAN,
        self::TYPE_CHOICE,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $values;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValues(): ?string
    {
        return $this->values;
    }

    public function setValues(string $values): self
    {
        $this->values = $values;

        return $this;
    }
}
