<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Numeric extends Rule
{

    protected $message = "The :attribute must be numeric";

    public function check($value)
    {
        return is_numeric($value);
    }

}
