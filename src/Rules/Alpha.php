<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Alpha extends Rule
{

    protected $message = "The :attribute only allows alphabet characters";

    public function check($value)
    {
        return is_string($value) && preg_match('/^[\pL\pM]+$/u', $value);
    }

}
