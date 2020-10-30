<?php

namespace App\Service;

use App\Entity\Product;
use App\Event\NewProductEvent;
use App\EventSubscriber\NewProductSubscriber;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ProductsManagerService
{
    /** @var Request|null $request */
    private $request;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private $eventDispatcher;
    private SendEmail $sendEmail;

    public function __construct(RequestStack $requestStack,
                                ValidatorInterface $validator,
                                EntityManagerInterface $entityManager,
                                ProductRepository $productRepository,
                                EventDispatcherInterface $eventDispatcher,
                                SendEmail $sendEmail)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->sendEmail = $sendEmail;

        $npSubs = new NewProductSubscriber();
        $this->eventDispatcher->addListener('product.new', array($npSubs, 'onProductNew'));
    }

    public function loadProduct($id)
    {
        $array = $this->productRepository->findBy(['id' => $id]);
        $product = reset($array);

        return $product;
    }

    public function createProduct(): ?Product
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $payload = $this->getRequestContents();

            $product = $this->productRepository->createProduct($payload);

            $this->validate($product);

            $this->productRepository->saveProduct($product);
            $this->entityManager->commit();

            $event = new NewProductEvent($product, $this->sendEmail);
            $this->eventDispatcher->dispatch($event, NewProductEvent::NAME);

            return $product;
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            throw $e;
        }
    }

    public function editProduct(int $id): ?Product
    {
        $product = $this->loadProduct($id);

        if ($product instanceof Product) {
            $this->entityManager->getConnection()->beginTransaction();

            try {
                $payload = $this->getRequestContents();

                $product = $this->productRepository->updateCategory($product, $payload);

                $this->validate($product);
                $this->productRepository->saveProduct($product);

                $this->entityManager->commit();

                return $product;
            } catch (\Exception $e) {
                $this->entityManager->rollback();

                throw $e;
            }
        }

        throw new NotFoundHttpException("Product with {$id} not found.");
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->loadProduct($id);

        if ($product instanceof Product) {
            $this->entityManager->getConnection()->beginTransaction();

            try {
                $this->entityManager->remove($product);
                $this->entityManager->flush();

                $this->entityManager->commit();

                return TRUE;
            } catch (\Exception $e) {
                $this->entityManager->rollback();

                throw $e;
            }
        }

        throw new NotFoundHttpException("Product with {$id} not found.");
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