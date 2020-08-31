<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Integer extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute debe ser un número entero.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
