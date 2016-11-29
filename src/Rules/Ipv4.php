<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Ipv4 extends Rule
{

    protected $message = "The :attribute is not valid IPv4 Address";

    public function check($value, array $params)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

}
