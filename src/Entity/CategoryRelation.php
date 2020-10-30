<?php

namespace App\Entity;

use App\Repository\CategoryRelationRepository;
use App\Validator\CategoryIsset;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\CategoryIsset as CategoryIssetAssert;

/**
 * @ORM\Entity(repositoryClass=CategoryRelationRepository::class)
 */
class CategoryRelation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy=Category::class)
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @CategoryIsset()
     */
    public $parent;

    /**
     * @ORM\OneToOne(targetEntity=Category::class, inversedBy="parentRelation", cascade={"persist", "remove"})
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        $newParent = null === $category ? null : $this;
        if ($category->getParent() !== $newParent) {
            $category->setParentRelation($newParent);
        }

        return $this;
    }
}
