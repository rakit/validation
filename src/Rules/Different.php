<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Different extends Rule
{

    protected $message = "The :attribute must be different with :field";

    protected $fillableParams = ['field'];

    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $field = $this->parameter('field');
        $anotherValue = $this->validation->getValue($field);

        return $value != $anotherValue;
    }
}
