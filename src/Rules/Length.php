<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Length extends Rule
{
    use Traits\SizeTrait;

    /** @var string */
    protected $message = "The :attribute must must have an exact length of :length";

    /** @var array */
    protected $fillableParams = ['length'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $this->requireParameters($this->fillableParams);

        $length = (int) $this->parameter('length');

        return strlen((string) $value) == $length;
    }
}
