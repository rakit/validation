<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use InvalidArgumentException;

class Callable extends Rule
{

    public function setParameters(array $params)
    {
        $this->params['callable'] = array_shift($params);
        $this->params['parameters'] = $params;
        return $this;
    }

    public function parameters(array $params)
    {
        $this->params['parameters'] = $params;
    }

    public function check($value)
    {
        $this->requireParameters(['callable']);

        $callable = $this->parameter('callable');
        if (! is_callable($callable)) {
            throw new InvalidArgumentException("Parameter 1 in callable rule must be callable. ", 1);
        }

        $params

        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\pN_-]+$/u', $value) > 0;
    }

}
