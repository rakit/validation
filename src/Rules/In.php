<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class In extends Rule
{

    protected $message = "The :attribute is not allowing :value";

    public function check($value, array $params)
    {
        $this->requireParamsCount($params, 1);
        return in_array($value, $params);
    }

}
