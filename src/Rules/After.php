<?php

namespace Rakit\Validation\Rules;

use Exception;
use Rakit\Validation\Rule;

class After extends Rule
{

    public function check($value, array $param)
    {

        if ((strtotime($value) === false) || (strtotime($param[0]) === false)) {

            throw new Exception(
                "Expected a valid date, got {$value} instead. 2016-12-08, 2016-12-02 14:58, tomorrow are considered valid dates"
            );
        }

        return $this->getTimeStamp($param[0]) < $this->getTimeStamp($value);
    }

    protected function getTimeStamp($date)
    {
        return strtotime($date);
    }
}
