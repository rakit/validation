<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Lowercase extends Rule
{

    protected $message = "The :attribute must be lowercase";

    public function check($value)
    {
    	return mb_strtolower($value, mb_detect_encoding($value)) === $value;
    }

}
