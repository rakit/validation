<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredIf extends Rule
{

    protected $message = "The :attribute is required";

    public function check($value, array $params)
    {
        $this->requireParamsCount($params, 2);
        $another_attr = array_shift($params);
        $another_values = $params;
        $another_value = $this->validation->getValue($another_attr);

        $validator = $this->validation->getValidator();
        $required_validator = $validator('required');

        if (in_array($another_value, $another_values)) {
            return $required_validator->check($value, []); 
        }

        return true;
    }

}
