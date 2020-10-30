<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryRelation[]    findAll()
 * @method CategoryRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryRelation::class);
    }

    public function createRelation(Category $category)
    {
        $relation = new CategoryRelation();
        $relation->setCategory($category);

        return $relation;
    }

    public function saveRelation(CategoryRelation $relation): CategoryRelation
    {
        $this->getEntityManager()->persist($relation);
        $this->getEntityManager()->flush();

        return $relation;
    }

    // /**
    //  * @return CategoryRelation[] Returns an array of CategoryRelation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryRelation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
