<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Date extends Rule
{

    protected $message = "The :attribute is not valid date format";

    protected $fillable_params = ['format'];

    protected $params = [
        'format' => 'Y-m-d'
    ];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);

        $format = $this->parameter('format');
        return date_create_from_format($format, $value) !== false;
    }

}
