<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use InvalidArgumentException;
use Closure;

class Callback extends Rule
{

    /** @var string */
    protected $message = "El campo :attribute no es válido.";

    /** @var array */
    protected $fillableParams = ['callback'];

    /**
     * Set the Callback closure
     *
     * @param Closure $callback
     * @return self
     */
    public function setCallback(Closure $callback): Rule
    {
        return $this->setParameter('callback', $callback);
    }

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $callback = $this->parameter('callback');
        if (false === $callback instanceof Closure) {
            $key = $this->attribute->getKey();
            throw new InvalidArgumentException("Callback rule para '{$key}' no se puede llamar.");
        }

        $callback = $callback->bindTo($this);
        $invalidMessage = $callback($value);

        if (is_string($invalidMessage)) {
            $this->setMessage($invalidMessage);
            return false;
        } elseif (false === $invalidMessage) {
            return false;
        }

        return true;
    }
}
