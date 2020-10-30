<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The product.new event is dispatched each time an order is created
 * in the system.
 */
class NewProductEvent extends Event
{
    public const NAME = 'product.new';

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }
}