<?php

namespace Rakit\Validation\Rules;

use Exception;

trait DateUtils
{

    protected function isValidDate($date)
    {
        return (strtotime($date) !== false);
    }

    protected function throwException($value)
    {
        return new Exception(
            "Expected a valid date, got '{$value}' instead. 2016-12-08, 2016-12-02 14:58, tomorrow are considered valid dates"
        );
    }

    protected function getTimeStamp($date)
    {
        return strtotime($date);
    }
}
