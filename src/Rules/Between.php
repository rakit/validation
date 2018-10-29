<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Between extends Rule
{

    protected $message = "The :attribute must be between :min and :max";

    protected $fillable_params = ['min', 'max'];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);

        $min = (int) $this->parameter('min');
        $max = (int) $this->parameter('max');

        if (is_int($value) || is_float($value)) {
            return $value >= $min AND $value <= $max;
        } elseif(is_string($value)) {
            return mb_strlen($value, 'UTF-8') >= $min AND mb_strlen($value, 'UTF-8') <= $max;
        } elseif(is_array($value)) {
            return count($value) >= $min AND count($value) <= $max;
        } else {
            return false;
        }
    }

}
