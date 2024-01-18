<?php

namespace App\Validator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AssertCanDeleteManufacturerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        /** Validate if Manufacturer has Products associated */
        if ($value->getProducts()->count()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', 'It has products associated.')
                ->setCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->addViolation();
        }
    }
}
