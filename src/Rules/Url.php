<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Url extends Rule
{

    protected $message = "The :attribute is not valid url";

    public function check($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

}
