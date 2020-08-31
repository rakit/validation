<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class DigitsBetween extends Rule
{

    /** @var string */
    protected $message = ":attribute debe tener una longitud entre :min y :max.";

    /** @var array */
    protected $fillableParams = ['min', 'max'];

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
        $max = (int) $this->parameter('max');

        $length = strlen((string) $value);

        return ! preg_match('/[^0-9]/', $value)
                    && $length >= $min && $length <= $max;
    }
}
