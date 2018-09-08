<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Max extends Rule
{

    protected $message = "The :attribute maximum is :max";

    protected $fillable_params = ['max'];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);

        $max = (int) $this->parameter('max');
        if (is_int($value)) {
            return $value <= $max;
        } elseif(is_string($value)) {
            return mb_strlen($value, 'UTF-8') <= $max;
        } elseif(is_array($value)) {
            return count($value) <= $max;
        } else {
            return false;
        }
    }

}
