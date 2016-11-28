<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Required extends Rule
{

    protected $message = "The :attribute is required";

    public function check($value, array $params)
    {
        if (is_string($value)) return strlen(trim($value)) > 0;
        if (is_array($value)) return count($value) > 0;
        return !is_null($value);
    }

}
