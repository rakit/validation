<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Same extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute debe ser lo mismo con :field.";

    /** @var array */
    protected $fillableParams = ['field'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $field = $this->parameter('field');
        $anotherValue = $this->getAttribute()->getValue($field);

        return $value == $anotherValue;
    }
}
