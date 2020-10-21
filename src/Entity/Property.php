<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertiesRepository")
 * @ORM\Table(name="properties")
 */
class Property
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(
     *     targetEntity="PropertyAttribute",
     *     mappedBy="property",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $propertyAttributes;

    public function __construct()
    {
        $this->propertyAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|PropertyAttribute[]
     */
    public function getPropertyAttributes(): Collection
    {
        return $this->propertyAttributes;
    }

    public function addPropertyAttributes(PropertyAttribute $propertyAttribute): self
    {
        if (!$this->propertyAttributes->contains($propertyAttribute)) {
            $propertyAttribute->setProperty($this);
            dump($propertyAttribute);
            $this->propertyAttributes[] = $propertyAttribute;
        }

        return $this;
    }

    public function addPropertyAttribute(PropertyAttribute $propertyAttribute): self
    {
        dump($this->propertyAttributes, $propertyAttribute);
        if (!$this->propertyAttributes->contains($propertyAttribute)) {
            $propertyAttribute->setProperty($this);
            dump($propertyAttribute);
            $this->propertyAttributes[] = $propertyAttribute;
        }

        return $this;
    }

    public function removePropertyAttribute(PropertyAttribute $propertyAttribute): self
    {
        if ($this->propertyAttributes->contains($propertyAttribute)) {
            $this->propertyAttributes->removeElement($propertyAttribute);
            // set the owning side to null (unless already changed)
            if ($propertyAttribute->getProperty() === $this) {
                $propertyAttribute->setProperty(null);
            }
        }

        return $this;
    }
}
