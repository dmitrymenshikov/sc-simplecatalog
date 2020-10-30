<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function createCategory(array $data): Category
    {
        $parent = !empty($data['parent']) && is_numeric($data['parent']) ? $data['parent'] : null;

        $category = new Category();
        $category->title = $data['title'] ?? null;
        $category->minPrice = (float) $data['minPrice'] ?? null;
        $category->parent = $parent;

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $parent = !empty($data['parent']) && is_numeric($data['parent']) ? $data['parent'] : null;

        $category->title = $data['title'] ?? null;
        $category->minPrice = (float) $data['minPrice'] ?? null;
        $category->parent = $parent;

        return $category;
    }

    public function saveCategory(Category $category): Category
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();

        return $category;
    }

}
