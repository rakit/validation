<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Time_24 extends Rule
{

    protected $message = "The :attribute is not in correct format";

    public function check($value)
         
    {
       
        return is_string($value) && preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value);
    }

}
