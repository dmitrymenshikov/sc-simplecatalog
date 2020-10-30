<?php

namespace App\Validator;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinPriceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\MinPrice */

        /** @var Product $product */
        $product = $this->context->getObject();

        /** @var Category $category */
        $category = $product->getCategory();
        $minPrice = $category->getMinPrice();
        if ($minPrice <= $product->getPrice()) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $minPrice)
            ->addViolation();
    }
}
