<?php

namespace App\Service;

use App\Entity\Product;
use Twig\Environment;

class SendEmail
{
    public $swift_mailer;
    private Environment $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->swift_mailer = $mailer;
        $this->twig = $twig;
    }

    public function newProduct(Product $product)
    {
        $message = (new \Swift_Message('New product mail'))
            ->setFrom('noreply@test.com')
            ->setTo('test@test.com, test2@test.com')
            ->setBody(
                $this->twig->render(
                    'emails/new_product.html.twig',
                    ['product_title' => $product->getTitle()]
                ),
                'text/html'
            );
        $this->swift_mailer->send($message);
    }
}