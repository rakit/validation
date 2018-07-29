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

        return preg_match('/^(091|\+91|91|\(091\)|\(\+91\)|\(91\)|0)? ?[7-9][0-9]{9}$/', $value) > 0;
        
    }

}
