<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Integer extends Rule
{

    protected $message = "The :attribute must be integer";

    public function check($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

}
