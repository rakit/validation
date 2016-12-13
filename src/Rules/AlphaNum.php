<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaNum extends Rule
{

    protected $message = "The :attribute only allows alphabet and numeric";

    public function check($value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\pN]+$/u', $value) > 0;
    }

}
