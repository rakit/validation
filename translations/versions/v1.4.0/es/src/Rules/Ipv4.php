<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Ipv4 extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute no es una dirección IPv4 válida.";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }
}
