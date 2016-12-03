<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Present extends Rule
{

    protected $message = "The :attribute must be present";

    public function check($value, array $params)
    {
        return $this->validation->hasValue($this->attribute->getKey());
    }

}
