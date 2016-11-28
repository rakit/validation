<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaDash extends Rule
{

    protected $message = "The :attribute only allows a-z, 0-8, _ and -";

    public function check($value, array $params)
    {
        return preg_match("/^[a-z0-9_-]*$/i", $value) > 0;
    }

}
