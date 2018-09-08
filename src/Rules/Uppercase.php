<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Uppercase extends Rule
{

    protected $message = "The :attribute must be uppercase";

    public function check($value)
    {
    	return mb_strtoupper($value, mb_detect_encoding($value)) === $value;
    }

}
