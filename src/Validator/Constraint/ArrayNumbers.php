<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ArrayNumbers extends Constraint
{
    public string $message = 'Niepoprawne liczby: {{ value }}';
}
