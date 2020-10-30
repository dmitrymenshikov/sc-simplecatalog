<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRelationRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoriesManagerService
{
    /** @var Request|null $request */
    private $request;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;
    private CategoryRelationRepository $categoryRelationRepository;

    public function __construct(RequestStack $requestStack,
                                ValidatorInterface $validator,
                                EntityManagerInterface $entityManager,
                                CategoryRepository $categoryRepository,
                                CategoryRelationRepository $categoryRelationRepository)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryRelationRepository = $categoryRelationRepository;
    }

    public function createCategory(): Category
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $payload = $this->getRequestContents();

            $category = $this->categoryRepository->createCategory($payload);
            $categoryRelation = $this->createRelation($category);

            if ($category->parent) {
                $categoryRelation->parent = $this->loadCategory($category->parent);
            }

            $this->validate($category);
            $this->validate($categoryRelation);

            $this->categoryRepository->saveCategory($category);
            $this->categoryRelationRepository->saveRelation($categoryRelation);

            $this->entityManager->commit();

            return $category;
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            throw $e;
        }
    }

    public function editCategory($id)
    {
        $category = $this->loadCategory($id);

        if ($category instanceof Category) {
            $this->entityManager->getConnection()->beginTransaction();

            try {
                $payload = $this->getRequestContents();

                $category = $this->categoryRepository->updateCategory($category, $payload);
                $categoryRelation = $category->getParentRelation();

                if ($category->parent &&
                    ($categoryRelation->getParent() instanceof Category && $category->parent !== $categoryRelation->getParent()->getId()) &&
                    $categoryRelation->getParent()->getId() !== $category->parent &&
                    $category->parent !== $category->getId()) {
                    $categoryRelation->parent = $this->loadCategory($category->parent);
                }

                $this->validate($category);
                $this->validate($categoryRelation);
                $this->categoryRepository->saveCategory($category);
                $this->categoryRelationRepository->saveRelation($categoryRelation);

                $this->entityManager->commit();

                return $category;
            } catch (\Exception $e) {
                $this->entityManager->rollback();

                throw $e;
            }
        }

        throw new NotFoundHttpException("Category with {$id} not found.");
    }

    public function deleteCategory($id)
    {
        $category = $this->loadCategory($id);

        if ($category instanceof Category) {
            $this->entityManager->getConnection()->beginTransaction();

            try {

                $this->entityManager->remove($category);
                $this->entityManager->flush();

                $this->entityManager->commit();

                return TRUE;
            } catch (\Exception $e) {
                $this->entityManager->rollback();

                throw $e;
            }
        }

        throw new NotFoundHttpException("Category with {$id} not found.");
    }

    private function createRelation(Category $category) {
        return $this->categoryRelationRepository->createRelation($category);
    }

    public function loadCategory($id) {
        $array = $this->categoryRepository->findBy(['id' => $id]);
        $category = reset($array);

        return $category;
    }

    private function validate($entity) {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new InvalidArgumentException($errorsString);
        }

        return TRUE;
    }

    private function getRequestContents()
    {
        return json_decode($this->request->getContent(), TRUE);
    }
}