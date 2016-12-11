<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredIf extends Required
{
    protected $implicit = true;

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
            $this->setAttributeAsRequired();
            return $required_validator->check($value, []); 
        }

        return true;
    }

}
