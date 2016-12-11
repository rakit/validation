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
        $anotherAttribute = array_shift($params);
        $definedValues = $params;
        $anotherValue = $this->validation->getValue($anotherAttribute);

        $validator = $this->validation->getValidator();
        $required_validator = $validator('required');

        if (in_array($anotherValue, $definedValues)) {
            $this->setAttributeAsRequired();
            return $required_validator->check($value, []); 
        }

        return true;
    }

}
