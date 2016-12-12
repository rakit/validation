<?php

namespace Rakit\Validation;

use Rakit\Validation\Rules\Required;

class Validation
{

    protected $validator;

    protected $inputs = [];

    protected $attributes = [];

    protected $messages = [];

    protected $aliases = [];

    public function __construct(Validator $validator, array $inputs, array $rules, array $messages = array())
    {
        $this->validator = $validator;
        $this->inputs = $this->resolveInputAttributes($inputs);
        $this->messages = $messages;
        $this->errors = new ErrorBag;
        foreach($rules as $attributeKey => $rules) {
            $this->addAttribute($attributeKey, $rules);
        }
    }

    public function addAttribute($attributeKey, $rules)
    {
        $resolved_rules = $this->resolveRules($rules);
        $attribute = new Attribute($this, $attributeKey, $this->getAlias($attributeKey), $resolved_rules);
        $this->attributes[$attributeKey] = $attribute;
    }

    public function getAttribute($attributeKey)
    {
        return isset($this->attributes[$attributeKey])? $this->attributes[$attributeKey] : null;
    }

    public function validate(array $inputs = array())
    {
        $this->errors = new ErrorBag; // reset error bag
        $this->inputs = array_merge($this->inputs, $this->resolveInputAttributes($inputs));
        foreach($this->attributes as $attributeKey => $attribute) {
            $this->validateAttribute($attribute);
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    protected function validateAttribute(Attribute $attribute)
    {
        $attributeKey = $attribute->getKey();
        $rules = $attribute->getRules(); 
        $value = $this->getValue($attributeKey);
        $isEmptyValue = $this->isEmptyValue($value);

        foreach($rules as $ruleValidator) {
            if ($isEmptyValue AND $this->ruleIsOptional($attribute, $ruleValidator)) {
                continue;
            }

            $params = $ruleValidator->getParams();
            $valid = $ruleValidator->check($value, $params);
            
            if (!$valid) {
                $rulename = $ruleValidator->getKey();
                $message = $this->resolveMessage($attribute, $value, $params, $ruleValidator);
                $this->errors->add($attributeKey, $rulename, $message);

                if ($ruleValidator->isImplicit()) {
                    break;
                }
            }
        }
    }

    protected function isEmptyValue($value)
    {
        $requiredValidator = new Required;
        return false === $requiredValidator->check($value, []);
    }

    protected function ruleIsOptional(Attribute $attribute, Rule $rule)
    {
        return false === $attribute->isRequired() AND 
            false === $rule->isImplicit() AND 
            false === $rule instanceof Required;
    }

    protected function resolveAttributeName($attributeKey)
    {
        return isset($this->aliases[$attributeKey]) ? $this->aliases[$attributeKey] : ucfirst(str_replace('_', ' ', $attributeKey));
    }

    protected function resolveMessage(Attribute $attribute, $value, array $params, Rule $validator)
    {
        $attributeKey = $attribute->getKey();
        $ruleKey = $validator->getKey();
        $alias = $attribute->getAlias() ?: $this->resolveAttributeName($attributeKey);
        $message = $validator->getMessage(); // default rule message
        $message_keys = [
            $attributeKey.'.'.$ruleKey,
            $attributeKey.'.*',
            $ruleKey
        ];

        foreach($message_keys as $key) {
            if (isset($this->messages[$key])) {
                $message = $this->messages[$key];
                break;
            }
        }

        $vars = [
            'attribute' => $alias,
            'value' => $value,
        ];

        foreach($params as $key => $value) {
            $vars['params['.$key.']'] = $value;
        }

        foreach($vars as $key => $value) {
            $value = $this->stringify($value);
            $message = str_replace(':'.$key, $value, $message);
        }

        return $message;
    }

    protected function stringify($value)
    {
        if (is_string($value) || is_numeric($value)) {
            return $value;
        } elseif(is_array($value) || is_object($value)) {
            return json_encode($value);
        } else {
            return '';
        }
    }

    protected function resolveRules($rules)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $resolved_rules = [];
        $validatorFactory = $this->getValidator();

        foreach($rules as $i => $rule) {
            if (empty($rule)) continue;
            $params = [];
            
            if (is_string($rule)) {
                list($rulename, $params) = $this->parseRule($rule);
                $validator = $validatorFactory($rulename);
            } elseif($rule instanceof Rule) {
                $validator = $rule;
                $params = $rule->getParams();
            } else {
                $ruleName = is_object($rule) ? get_class($rule) : gettype($rule);
                throw new \Exception("Rule must be a string or Rakit\Validation\Rule instance. ".$ruleName." given", 1);
            }

            $validator->setParams($params);

            $resolved_rules[] = $validator;
        }

        return $resolved_rules;
    }

    protected function parseRule($rule)
    {
        $exp = explode(':', $rule, 2);
        $rulename = $exp[0];
        $params = isset($exp[1])? explode(',', $exp[1]) : [];
        return [$rulename, $params];
    }

    public function setMessage($key, $message)
    {
        $this->messages[$key] = $message;
    }

    public function setMessages(array $messages)
    {
        array_merge($this->messages, $messages);
    }

    public function setAlias($attributeKey, $alias)
    {
        $this->aliases[$attributeKey] = $alias;
    }

    public function getAlias($attributeKey)
    {
        return isset($this->aliases[$attributeKey])? $this->aliases[$attributeKey] : null;
    }

    public function setAliases($aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function passes()
    {
        return $this->errors->count() == 0;
    }

    public function fails()
    {
        return !$this->passes();
    }

    public function getValue($key)
    {
        return isset($this->inputs[$key])? $this->inputs[$key] : null;
    }

    public function hasValue($key)
    {
        return isset($this->inputs[$key]);
    }

    public function getValidator()
    {
        return $this->validator;
    }

    protected function resolveInputAttributes(array $inputs)
    {
        $resolvedInputs = [];
        foreach($inputs as $key => $rules) {
            $exp = explode(':', $key);
            
            if (count($exp) > 1) {
                // set attribute alias
                $this->aliases[$exp[0]] = $exp[1];
            }

            $resolvedInputs[$exp[0]] = $rules;
        }

        return $resolvedInputs;
    }

}