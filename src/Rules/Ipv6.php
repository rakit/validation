<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Ipv6 extends Rule
{

    protected $message = "The :attribute is not valid IPv6 Address";

    public function check($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

}
