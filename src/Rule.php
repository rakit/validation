<?php

namespace Rakit\Validation;

use Rakit\Validation\MissingRequiredParameterException;

abstract class Rule
{
    /** @var mixed */
    protected $key;

    /** @var mixed */
    protected $attribute;

    /** @var mixed */
    protected $validation;

    /** @var bool */
    protected $implicit = false;

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $fillableParams = [];

    /** @var string */
    protected $message = "The :attribute is invalid";

    abstract public function check($value);

    /**
     * Set Validation class instance
     *
     * @param Validation $validation
     * @return void
     */
    public function setValidation(Validation $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Set key
     *
     * @param mixed $key
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key ?: get_class($this);
    }

    /**
     * Set attribute
     *
     * @param mixed $attribute
     * @return void
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Get attribute
     *
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute ?: get_class($this);
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * Set params
     *
     * @param array $params
     * @return Rule
     */
    public function setParameters(array $params): Rule
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Set parameters
     *
     * @param mixed $key
     * @param mixed $value
     * @return Rule
     */
    public function setParameter($key, $value): Rule
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Fill $params to $this->params
     *
     * @param array $params
     * @return Rule
     */
    public function fillParameters(array $params): Rule
    {
        foreach ($this->fillableParams as $key) {
            if (empty($params)) {
                break;
            }
            $this->params[$key] = array_shift($params);
        }
        return $this;
    }

    /**
     * Given $key and check is existed in $this->params
     *
     * @param mixed $key
     * @return void
     */
    public function parameter($key)
    {
        return isset($this->params[$key])? $this->params[$key] : null;
    }

    /**
     * Check $this->isImplicit is true
     *
     * @return boolean
     */
    public function isImplicit(): bool
    {
        return $this->implicit === true;
    }

    /**
     * Set message
     *
     * @param mixed $message
     * @return Rule
     */
    public function message($message): Rule
    {
        return $this->setMessage($message);
    }

    /**
     * Set message
     *
     * @param mixed $message
     * @return Rule
     */
    public function setMessage($message): Rule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return void
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Check $params are existed in $this->params
     *
     * @param array $params
     * @return void
     */
    protected function requireParameters(array $params)
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                $rule = $this->getKey();
                throw new MissingRequiredParameterException("Missing required parameter '{$param}' on rule '{$rule}'");
            }
        }
    }
}
