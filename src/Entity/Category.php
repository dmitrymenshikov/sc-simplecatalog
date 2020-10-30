<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\LessThanOrEqual(255)
     */
    public $title;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float")
     */
    public $minPrice;

    /**
     * @ORM\OneToOne(targetEntity=CategoryRelation::class, mappedBy="category", cascade={"persist", "remove"})
     */
    public $parentRelation;
    public ?int $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function getParentRelation(): ?CategoryRelation
    {
        return $this->parentRelation;
    }

    public function setParentRelation(?CategoryRelation $parent): self
    {
        $this->parentRelation = $parent;

        return $this;
    }

    public function getParent(): ?Category
    {
        $relation = $this->getParentRelation();

        if ($relation instanceof CategoryRelation) {
            $parent = $relation->getParent();

            return $parent;
        }

        return null;
    }

    public function getParentId(): ?int
    {
        $parent = $this->getParent();

        if ($parent instanceof Category) {
            return $parent->getId();
        }

        return null;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'minPrice' => $this->getMinPrice(),
            'parent' => $this->getParentId()
        ];
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }
}
