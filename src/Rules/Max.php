<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Max extends Rule
{

    protected $message = "The :attribute maximum is :params[0]";

    public function check($value, array $params)
    {
        $this->assertParamsCount($params, 1);
        $max = (int) $params[0];
        if (is_int($value)) {
            return $value <= $max;
        } elseif(is_string($value)) {
            return strlen($value) <= $max;
        } elseif(is_array($value)) {
            return count($value) <= $max;
        } else {
            return false;
        }
    }

}
