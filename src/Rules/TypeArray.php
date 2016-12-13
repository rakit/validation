<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class TypeArray extends Rule
{

    protected $message = "The :attribute must be array";

    public function check($value)
    {
        return is_array($value);
    }

}
