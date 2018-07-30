<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class MatchPhone extends Rule
{

    protected $message = "The :attribute is wrong format";

    public function check($value)
    {
        if (!is_numeric($value)) {
            return false;
        }

        return preg_match('/^([\+0-9]{2,5}|0)? ?[1-9][0-9]{2}[ \-]?[0-9]{3}[ \-]?[0-9]{4}$/', $value) > 0;
        
    }

}
