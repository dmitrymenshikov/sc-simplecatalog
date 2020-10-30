<?php

namespace App\Event;

use App\Entity\Product;
use App\Service\SendEmail;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The product.new event is dispatched each time an order is created
 * in the system.
 */
class NewProductEvent extends Event
{
    public const NAME = 'product.new';

    protected $product;
    protected $sendEmail;

    public function __construct(Product $product, SendEmail $sendEmail)
    {
        $this->product = $product;
        $this->sendEmail = $sendEmail;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getSenderEmails()
    {
        return $this->sendEmail;
    }
}