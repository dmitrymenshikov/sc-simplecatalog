<?php

namespace App\EventSubscriber;

use App\Event\NewProductEvent;
use App\Service\SendEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewProductSubscriber implements EventSubscriberInterface
{
    private SendEmail $sendEmail;

    public function onProductNew(NewProductEvent $event)
    {
        $sendEmail = $event->getSenderEmails();
        $sendEmail->newProduct($event->getProduct());
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.new' => 'onProductNew',
        ];
    }
}
