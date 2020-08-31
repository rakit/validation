<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class TypeArray extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute debe ser una matriz.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return is_array($value);
    }
}
