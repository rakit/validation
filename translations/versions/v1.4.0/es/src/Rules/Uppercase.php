<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Uppercase extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute debe estar en mayúsculas.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return mb_strtoupper($value, mb_detect_encoding($value)) === $value;
    }
}
