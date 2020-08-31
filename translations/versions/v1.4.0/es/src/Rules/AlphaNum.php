<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaNum extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute solo permite caracteres alfanuméricos.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\pN]+$/u', $value) > 0;
    }
}
