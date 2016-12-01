<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Date extends Rule
{

    protected $message = "The :attribute is not valid date format";

    public function check($value, array $params)
    {
        $format = isset($params[0])? $params[0] : 'Y-m-d';
        return date_create_from_format($format, $value) !== false;
    }

}
