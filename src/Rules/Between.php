<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Between extends Rule
{

    protected $message = "The :attribute must be between :params[0] and :params[1]";

    public function check($value, array $params)
    {
        $this->assertParamsCount($params, 2);
        $min = (int) $params[0];
        $max = (int) $params[1];
        if (is_int($value)) {
            return $value >= $min AND $value <= $max;
        } elseif(is_string($value)) {
            return strlen($value) >= $min AND strlen($value) <= $max;
        } elseif(is_array($value)) {
            return count($value) >= $min AND count($value) <= $max;
        } else {
            return false;
        }
    }

}
