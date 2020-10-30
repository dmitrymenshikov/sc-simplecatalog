<?php

namespace App\Controller\Api;

use App\Service\ProductsManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private ProductsManagerService $productsManagerService;

    public function __construct(ProductsManagerService $productsManagerService)
    {
        $this->productsManagerService = $productsManagerService;
    }

    /**
     * @Route("/api/products", methods={"GET","HEAD"})
     */
    public function getAll(Request $request)
    {
        return $this->json(['status' => 'ok', 'products' => []]);
    }

    /**
     * @Route("/api/products/{id}", methods={"GET","HEAD"})
     */
    public function getForSingle($id)
    {
        $product = $this->productsManagerService->loadProduct($id);

        return $this->json(['status' => 'ok', 'product' => $product->toArray()]);
    }

    /**
     * @Route("/api/products/create", methods={"POST","HEAD"})
     */
    public function createCategory()
    {
        try {
            $product = $this->productsManagerService->createProduct();

            return $this->json([
                'status' => 'ok',
                'product' => $product->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/api/products/{id}/edit", methods={"PUT","HEAD"})
     */
    public function editCategory($id)
    {
        try {
            $category = $this->productsManagerService->editProduct($id);

            return $this->json([
                'status' => 'ok',
                'product' => $category->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/api/products/{id}/delete", methods={"DELETE","HEAD"})
     */
    public function deleteCategory($id)
    {
        try {
            $this->productsManagerService->deleteProduct($id);

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
