<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Date extends Rule
{

    protected $message = "The :attribute is not valid date format";

    protected $fillableParams = ['format'];

    protected $params = [
        'format' => 'Y-m-d'
    ];

    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $format = $this->parameter('format');
        return date_create_from_format($format, $value) !== false;
    }
}
