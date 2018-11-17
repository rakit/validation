<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Defaults extends Rule
{

    protected $message = "The :attribute default is :default";

    protected $fillableParams = ['default'];

    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $default = $this->parameter('default');
        return $default;
    }
}
