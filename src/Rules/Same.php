<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Same extends Rule
{

    protected $message = "The :attribute must be same with :params[0]";

    public function check($value, array $params)
    {
        $this->requireParamsCount($params, 1);
        $another_value = $this->validation->getValue($params[0]);

        return $value == $another_value;
    }

}
