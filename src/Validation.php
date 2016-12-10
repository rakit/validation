<?php

namespace Rakit\Validation;

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
        foreach($rules as $key_attribute => $rules) {
            $this->addAttribute($key_attribute, $rules);
        }
    }

    public function addAttribute($key_attribute, $rules)
    {
        $resolved_rules = $this->resolveRules($rules);
        $attribute = new Attribute($this, $key_attribute, $this->getAlias($key_attribute), $resolved_rules);
        $this->attributes[$key_attribute] = $attribute;
    }

    public function getAttribute($key_attribute)
    {
        return isset($this->attributes[$key_attribute])? $this->attributes[$key_attribute] : null;
    }

    public function validate(array $inputs = array())
    {
        $this->errors = new ErrorBag; // reset error bag
        $this->inputs = array_merge($this->inputs, $this->resolveInputAttributes($inputs));
        foreach($this->attributes as $key_attribute => $attribute) {
            $this->validateAttribute($attribute);
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    protected function validateAttribute(Attribute $attribute)
    {
        $key_attribute = $attribute->getKey();
        $rules = $attribute->getRules(); 
        $value = $this->getValue($key_attribute);

        foreach($rules as $rule_validator) {
            $params = $rule_validator->getParams();
            $valid = $rule_validator->check($value, $params);
            if (!$valid) {
                $rulename = $rule_validator->getKey();
                $message = $this->resolveMessage($attribute, $value, $params, $rule_validator);
                $this->errors->add($key_attribute, $rulename, $message);

                if ($rule_validator->isImplicit()) {
                    break;
                }
            }
        }
    }

    protected function resolveAttributeName($key_attribute)
    {
        return isset($this->aliases[$key_attribute]) ? $this->aliases[$key_attribute] : ucfirst(str_replace('_', ' ', $key_attribute));
    }

    protected function resolveMessage(Attribute $attribute, $value, array $params, Rule $validator)
    {
        $key_attribute = $attribute->getKey();
        $rule_key = $validator->getKey();
        $alias = $attribute->getAlias() ?: $this->resolveAttributeName($key_attribute);
        $message = $validator->getMessage(); // default rule message
        $message_keys = [
            $key_attribute.'.'.$rule_key,
            $key_attribute.'.*',
            $rule_key
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
        $Validator = $this->getValidator();

        foreach($rules as $i => $rule) {
            if (empty($rule)) continue;
            $params = [];
            
            if (is_string($rule)) {
                list($rulename, $params) = $this->parseRule($rule);
                $validator = $Validator($rulename);
            } elseif($rule instanceof Rule) {
                $validator = $rule;
                $params = $rule->getParams();
            } else {
                $rule_def = is_object($rule) ? get_class($rule) : gettype($rule);
                throw new \Exception("Rule must be a string or Rakit\Validation\Rule instance. ".$rule_def." given", 1);
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

    public function setAlias($key_attribute, $alias)
    {
        $this->aliases[$key_attribute] = $alias;
    }

    public function getAlias($key_attribute)
    {
        return isset($this->aliases[$key_attribute])? $this->aliases[$key_attribute] : null;
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
        $resolved_inputs = [];
        foreach($inputs as $key => $rules) {
            $exp = explode(':', $key);
            
            if (count($exp) > 1) {
                // set attribute alias
                $this->aliases[$exp[0]] = $exp[1];
            }

            $resolved_inputs[$exp[0]] = $rules;
        }

        return $resolved_inputs;
    }

}