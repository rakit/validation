<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class String extends Rule
{

    /** @var string */
    protected $message = "The :attribute only allows a string";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return is_string($value);
    }
}
