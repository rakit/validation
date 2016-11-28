<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredIf extends Rule
{

    protected $message = "The :attribute is required";

    public function check($value, array $params)
    {
        $this->assertParamsCount($params, 1);
        $another_value = $this->validation->getValue($params[0]);
        $validator = $this->validation->getValidator();
        $required_validator = $validator('required');
        $a = $required_validator->check($another_value);

        if ($a) {
            return $required_validator->check($value); 
        }

        return true;
    }

}
