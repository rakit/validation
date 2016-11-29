<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Min extends Rule
{

    protected $message = "The :attribute minimum is :params[0]";

    public function check($value, array $params)
    {
        $this->requireParamsCount($params, 1);
        $min = (int) $params[0];
        if (is_int($value)) {
            return $value >= $min;
        } elseif(is_string($value)) {
            return strlen($value) >= $min;
        } elseif(is_array($value)) {
            return count($value) >= $min;
        } else {
            return false;
        }
    }

}
