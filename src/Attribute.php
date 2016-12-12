<?php

namespace Rakit\Validation;

class Attribute
{

    protected $rules = [];

    protected $key;

    protected $alias;

    protected $validation;

    protected $required = false;

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

    public function getRule($ruleKey)
    {
        return $this->hasRule($ruleKey)? $this->rules[$ruleKey] : null;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function hasRule($ruleKey)
    {
        return isset($this->rules[$ruleKey]);
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function isRequired()
    {
        return $this->required === true;
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