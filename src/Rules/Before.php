<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Before extends Rule
{
    use DateUtils;

    public function check($value, array $param)
    {

        if (!$this->isValidDate($value)){
            throw $this->throwException($value);
        }

        if (!$this->isValidDate($param[0])) {
            throw $this->throwException($param[0]);
        }

        return $this->getTimeStamp($param[0]) > $this->getTimeStamp($value);
    }
}
