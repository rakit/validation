<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class DigitsBetween extends Rule
{

    protected $message = "The :attribute must have a length between the given :min and :max";

    protected $fillableParams = ['min', 'max'];

    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $min = (int) $this->parameter('min');
        $max = (int) $this->parameter('max');

        $length = strlen((string) $value);

        return ! preg_match('/[^0-9]/', $value)
                    && $length >= $min && $length <= $max;
    }
}
