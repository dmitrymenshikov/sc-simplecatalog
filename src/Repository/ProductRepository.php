<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private CategoryRepository $categoryRepository;

    public function __construct(ManagerRegistry $registry, CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;

        parent::__construct($registry, Product::class);
    }

    public function createProduct(array $data): Product
    {
        $product = new Product();

        $product->title = $data['title'] ?? null;
        $product->price = $data['price'] ?? null;
        $this->setCategory($product, $data['category'] ?? null);

        return $product;
    }

    public function updateCategory(Product $product, array $data): Product
    {
        $product->title = $data['title'] ?? null;
        $product->price = $data['price'] ?? null;
        $this->setCategory($product, $data['category']);

        return $product;
    }

    public function saveProduct(Product $product): Product
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return $product;
    }

    private function setCategory(Product &$product, $category_id): void {
        $array = $this->categoryRepository->findBy(['id' => $category_id]);
        $category = reset($array);

        if ($category instanceof Category) {
            $product->setCategory($category);
        }
    }
}
