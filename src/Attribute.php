<?php

namespace Rakit\Validation;

class Attribute
{

    protected $rules = [];

    protected $key;

    protected $alias;

    protected $validation;

    public function __construct(Validation $validation, $key, $alias = null, array $rules = array())
    {
        $this->validation = $validation;
        $this->alias = $alias;
        $this->key = $key;
        foreach($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function addRule(Rule $rule)
    {
        $rule->setAttribute($this);
        $rule->setValidation($this->validation);
        $this->rules[$rule->getKey()] = $rule;
    }

    public function getRule($rule_key)
    {
        return $this->hasRule($rule_key)? $this->rules[$rule_key] : null;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function hasRule($rule_key)
    {
        return isset($this->rules[$rule_key]);
    }

    public function isRequired()
    {
        return $this->hasRule('required');
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

}