<?php

namespace Rakit\Validation;

class Attribute
{

    protected $rules = [];

    protected $key;

    protected $alias;

    protected $validation;

    protected $required = false;

    protected $primaryAttribute = null;

    protected $otherAttributes = [];

    protected $keyIndexes = [];

    public function __construct(Validation $validation, $key, $alias = null, array $rules = array())
    {
        $this->validation = $validation;
        $this->alias = $alias;
        $this->key = $key;
        foreach($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function setPrimaryAttribute(Attribute $primaryAttribute)
    {
        $this->primaryAttribute = $primaryAttribute;
    }

    public function setKeyIndexes(array $keyIndexes)
    {
        $this->keyIndexes = $keyIndexes;
    }

    public function getPrimaryAttribute()
    {
        return $this->primaryAttribute;
    }

    public function setOtherAttributes(array $otherAttributes)
    {
        $this->otherAttributes = [];
        foreach($otherAttributes as $otherAttribute) {
            $this->addOtherAttribute($otherAttribute);
        }
    }

    public function addOtherAttribute(Attribute $otherAttribute)
    {
        $this->otherAttributes[] = $otherAttribute;
    }

    public function getOtherAttributes()
    {
        return $this->otherAttributes;
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

    public function getKeyIndexes()
    {
        return $this->keyIndexes;
    }

    public function getValue($key = null)
    {
        if ($key && $this->isArrayAttribute()) {
            $key = $this->resolveSiblingKey($key);
        }

        if (!$key) {
            $key = $this->getKey();
        }

        return $this->validation->getValue($key);
    }

    public function isArrayAttribute()
    {
        return count($this->getKeyIndexes()) > 0;
    }

    public function isUsingDotNotation()
    {
        return strpos($this->getKey(), '.') !== false;
    }

    public function resolveSiblingKey($key)
    {
        $indexes = $this->getKeyIndexes();
        $keys = explode("*", $key);
        $countAsterisks = count($keys) - 1;
        if (count($indexes) < $countAsterisks) {
            $indexes = array_merge($indexes, array_fill(0, $countAsterisks - count($indexes), "*"));
        }
        $args = array_merge([str_replace("*", "%s", $key)], $indexes);
        return call_user_func_array('sprintf', $args);
    }

    public function getHumanizedKey()
    {
        $primaryAttribute = $this->getPrimaryAttribute();
        $key = str_replace('_', ' ', $this->key);

        // Resolve key from array validation
        if ($primaryAttribute) {
            $split = explode('.', $key);
            $key = implode(' ', array_map(function($word) {
                if (is_numeric($word)) {
                    $word = $word + 1;
                }
                return Helper::snakeCase($word, ' ');
            }, $split));
        }

        return ucfirst($key);
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
