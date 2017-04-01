<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredWithout extends Required
{
    protected $implicit = true;

    protected $message = "The :attribute is required";

    public function fillParameters(array $params)
    {
        $this->params['fields'] = $params;
        return $this;
    }

    public function check($value)
    {
        $this->requireParameters(['fields']);
        $fields = $this->parameter('fields');
        $validator = $this->validation->getValidator();
        $required_validator = $validator('required');

        foreach($fields as $field) {
            if (!$this->validation->hasValue($field)) {
                $this->setAttributeAsRequired();
                return $required_validator->check($value, []); 
            }
        }

        return true;
    }

}
