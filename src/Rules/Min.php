<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Min extends Rule
{

    /** @var string */
    protected $message = "The :attribute minimum is :min";

    /** @var array */
    protected $fillableParams = ['min'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min = (int) $this->parameter('min');
        if (is_int($value)) {
            return $value >= $min;
        } elseif (is_string($value)) {
            return mb_strlen($value, 'UTF-8') >= $min;
        } elseif (is_array($value)) {
            return count($value) >= $min;
        } else {
            return false;
        }
    }
}
