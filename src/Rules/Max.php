<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Max extends Rule
{

    /** @var string */
    protected $message = "The :attribute maximum is :max";

    /** @var array */
    protected $fillableParams = ['max'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $max = (int) $this->parameter('max');
        if (is_int($value)) {
            return $value <= $max;
        } elseif (is_string($value)) {
            return mb_strlen($value, 'UTF-8') <= $max;
        } elseif (is_array($value)) {
            return count($value) <= $max;
        } else {
            return false;
        }
    }
}
