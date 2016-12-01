<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Regex extends Rule
{

    protected $message = "The :attribute is not valid format";

    public function check($value, array $params)
    {
        $this->requireParamsCount($params, 1);
        $regex = $params[0];
        return preg_match($regex, $value) > 0;
    }

}
