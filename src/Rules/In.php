<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class In extends Rule
{

    /** @var string */
    protected $message = "The :attribute is not allowing :value";

    /** @var bool */
    protected $strict = false;

    /**
     * Given $params and assign the $this->params
     *
     * @param array $params
     * @return self
     */
    public function fillParameters(array $params): Rule
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['allowed_values'] = $params;
        return $this;
    }

    /**
     * Set strict value
     *
     * @param bool $strict
     * @return void
     */
    public function strict(bool $strict = true)
    {
        $this->strict = $strict;
    }

    /**
     * Check $value is existed
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value)
    {
        $this->requireParameters(['allowed_values']);

        $allowed_values = $this->parameter('allowed_values');
        return in_array($value, $allowed_values, $this->strict);
    }
}
