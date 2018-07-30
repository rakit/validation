<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaAdd extends Rule
{

    protected $message = "The Place Of Birth only allows alphabet and numeric";

    public function check($value)
    {
        //if (! is_string($value) && ! is_numeric($value)) {
          //  return false;
        //}

        return preg_match('/^[\pL\pM 0-9 \,\.\-\/]+$/u', $value) > 0;
        
    }

}
