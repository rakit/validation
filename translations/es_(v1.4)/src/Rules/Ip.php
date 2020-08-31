<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Ip extends Rule
{

    /** @var string */
    protected $message = ":attribute no es una dirección IP válida.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
