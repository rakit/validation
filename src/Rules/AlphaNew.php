<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaNew extends Rule
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
/*
Rule to accept alphabet or  numeric or both with or without  (, . / - ' ') 

This is a new file in Rules/AlphaNew which accepts alphabets, numerics or both with or without some special characters used in the resident address like a comma, dot, space hyphen and slash (, . ' ' - /).

I was having trouble to accept home addess through the form, so I created it.

Do check it out.

*/