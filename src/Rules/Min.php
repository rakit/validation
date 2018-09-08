<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Min extends Rule
{

    protected $message = "The :attribute minimum is :min";

    protected $fillable_params = ['min'];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        
        $min = (int) $this->parameter('min');
        if (is_int($value)) {
            return $value >= $min;
        } elseif(is_string($value)) {
            return mb_strlen($value, 'UTF-8') >= $min;
        } elseif(is_array($value)) {
            return count($value) >= $min;
        } else {
            return false;
        }
    }

}
