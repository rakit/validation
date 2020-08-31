<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Regex extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute no es un formato válido.";

    /** @var array */
    protected $fillableParams = ['regex'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $regex = $this->parameter('regex');
        return preg_match($regex, $value) > 0;
    }
}
