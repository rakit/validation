<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Same extends Rule
{

    protected $message = "The :attribute must be same with :field";

    protected $fillableParams = ['field'];

    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $field = $this->parameter('field');
        $anotherValue = $this->getAttribute()->getValue($field);

        return $value == $anotherValue;
    }
}
