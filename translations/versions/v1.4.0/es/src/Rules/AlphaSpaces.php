<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaSpaces extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute solo permite caracteres alfabéticos y espacios.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\s]+$/u', $value) > 0;
    }
}
