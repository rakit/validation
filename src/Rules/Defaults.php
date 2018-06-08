<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Defaults extends Rule
{

    protected $message = "The :attribute default is :default";

    protected $fillable_params = ['default'];

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        
        $default = $this->parameter('default');
        return $default;
    }

}
