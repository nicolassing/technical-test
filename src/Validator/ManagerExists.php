<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ManagerExists extends Constraint
{
    public string $message = 'Manager with ID "{{ id }}" does not exist.';
}
