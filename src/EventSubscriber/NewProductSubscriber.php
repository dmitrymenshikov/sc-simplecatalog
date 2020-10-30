<?php

namespace App\EventSubscriber;

use App\Event\NewProductEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewProductSubscriber implements EventSubscriberInterface
{
    public function onProductNew(NewProductEvent $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.new' => 'onProductNew',
        ];
    }
}
