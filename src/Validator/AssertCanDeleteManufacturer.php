<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertCanDeleteManufacturer extends Constraint
{
    public string $message = 'Cannot delete manufacturer. {{ string }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
