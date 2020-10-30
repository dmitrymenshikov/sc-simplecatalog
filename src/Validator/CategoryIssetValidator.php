<?php

namespace App\Validator;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryIssetValidator extends ConstraintValidator
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CategoryIsset */

        if (null === $value || '' === $value || [] === $value) {
            return;
        }

        if (is_numeric($value) && ($category = $this->loadCategory($value)) && $category instanceof Category) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }

    public function loadCategory($id)
    {
        $array = $this->categoryRepository->findBy(['id' => $id]);
        $category = reset($array);

        return $category;
    }
}
