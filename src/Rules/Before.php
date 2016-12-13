<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Before extends Rule
{
    use DateUtils;

    protected $message = "The :attribute must be a date before :time.";

    protected $fillable_params = ['time'];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $time = $this->parameter('time');

        if (!$this->isValidDate($value)){
            throw $this->throwException($value);
        }

        if (!$this->isValidDate($time)) {
            throw $this->throwException($time);
        }

        return $this->getTimeStamp($time) > $this->getTimeStamp($value);
    }
}
