<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredWith extends Required
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
        $requiredValidator = $validator('required');

        foreach ($fields as $field) {
            if ($this->validation->hasValue($field)) {
                $this->setAttributeAsRequired();
                return $requiredValidator->check($value, []);
            }
        }

        return true;
    }
}
