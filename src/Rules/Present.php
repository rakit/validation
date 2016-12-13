<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Present extends Rule
{
    protected $implicit = true;

    protected $message = "The :attribute must be present";

    public function check($value)
    {
        return $this->validation->hasValue($this->attribute->getKey());
    }

}
