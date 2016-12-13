<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Ip extends Rule
{

    protected $message = "The :attribute is not valid IP Address";

    public function check($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

}
