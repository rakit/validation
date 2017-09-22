<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use InvalidArgumentException;
use Closure;

class Callback extends Rule
{

    protected $message = "The :attribute is not valid";

    protected $fillable_params = ['callback'];

    public function setCallback(Closure $callback)
    {
        return $this->setParameter('callback', $callback);
    }

    public function check($value)
    {
        $this->requireParameters($this->fillable_params);

        $callback = $this->parameter('callback');
        if (false === $callback instanceof Closure) {
            $key = $this->attribute->getKey();
            throw new InvalidArgumentException("Callback rule for '{$key}' is not callable.");
        }

        $callback = $callback->bindTo($this);
        $invalidMessage = $callback($value);

        if (is_string($invalidMessage)) {
            $this->setMessage($invalidMessage);
            return false;
        } elseif(false === $invalidMessage) {
            return false;
        }

        return true;
    }

}
