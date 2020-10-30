<?php

namespace App\Controller\Api;

use App\Service\CategoriesManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    private CategoriesManagerService $categoriesManagerService;

    public function __construct(CategoriesManagerService $categoriesManagerService)
    {
        $this->categoriesManagerService = $categoriesManagerService;
    }

    /**
     * @Route("/api/categories", methods={"GET","HEAD"})
     */
    public function getAll(Request $request)
    {
        return $this->json(['status' => 'ok', 'categories' => []]);
    }

    /**
     * @Route("/api/categories/{id}", methods={"GET","HEAD"})
     */
    public function getForSingle($id)
    {
        $category = $this->categoriesManagerService->loadCategory($id);

        return $this->json(['status' => 'ok', 'category' => $category->toArray()]);
    }

    /**
     * @Route("/api/categories/create", methods={"POST","HEAD"})
     */
    public function createCategory()
    {
        try {
            $category = $this->categoriesManagerService->createCategory();

            return $this->json([
                'status' => 'ok',
                'category' => $category->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/api/categories/{id}/edit", methods={"PUT","HEAD"})
     */
    public function editCategory($id)
    {
        try {
            $category = $this->categoriesManagerService->editCategory($id);

            return $this->json([
                'status' => 'ok',
                'category' => $category->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/api/categories/{id}/delete", methods={"DELETE","HEAD"})
     */
    public function deleteCategory($id)
    {
        try {
            $this->categoriesManagerService->deleteCategory($id);

            return $this->json([
                'status' => 'ok',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }
}
