<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Alpha extends Rule
{

    protected $message = "The :attribute only allows alphabet characters";

    public function check($value, array $params)
    {
        return preg_match("/^[a-z]*$/i", $value) > 0;
    }

}
