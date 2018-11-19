<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Defaults extends Rule
{

    /** @var string */
    protected $message = "The :attribute default is :default";

    /** @var array */
    protected $fillableParams = ['default'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return mixed
     */
    public function check($value)
    {
        $this->requireParameters($this->fillableParams);

        $default = $this->parameter('default');
        return $default;
    }
}
