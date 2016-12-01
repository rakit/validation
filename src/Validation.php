<?php

namespace Rakit\Validation;

class Validation
{

    protected $validator;

    protected $inputs = [];

    protected $rules = [];

    protected $messages = [];

    protected $attributes = [];

    public function __construct(Validator $validator, array $inputs, array $rules, array $messages = array())
    {
        $this->validator = $validator;
        $this->inputs = $this->resolveInputAttributes($inputs);
        $this->messages = $messages;
        $this->rules = $rules;
        $this->errors = new ErrorBag;
    }

    public function validate(array $inputs = array())
    {
        $this->errors = new ErrorBag; // reset error bag
        $this->inputs = array_merge($this->inputs, $this->resolveInputAttributes($inputs));
        foreach($this->rules as $attribute => $rules) {
            $this->validateAttribute($attribute, $rules);
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    protected function validateAttribute($attribute, $rules)
    {
        $rules = $this->resolveRules($rules);
        $value = $this->getValue($attribute);

        foreach($rules as $rule) {
            $validator = $rule['validator'];
            $params = $rule['params'];
            $validator->setAttribute($attribute);
            $validator->setValidation($this);
            
            $valid = $validator->check($value, $params);
            if (!$valid) {
                $rulename = $validator->getKey();
                $message = $this->resolveMessage($attribute, $value, $params, $validator);
                $this->errors->add($attribute, $rulename, $message);
            }
        }
    }

    protected function resolveAttributeName($attribute)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : ucfirst(str_replace('_', ' ', $attribute));
    }

    protected function resolveMessage($attribute, $value, array $params, Rule $validator)
    {
        $rule_key = $validator->getKey();
        $alias = $this->resolveAttributeName($attribute);
        $message = $validator->getMessage(); // default rule message
        $message_keys = [
            $attribute.'.'.$rule_key,
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

            $resolved_rules[] = [
                'validator' => $validator,
                'params' => $params
            ];
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

    public function setAlias($attribute, $alias)
    {
        $this->attributes[$attribute] = $alias;
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
                $this->attributes[$exp[0]] = $exp[1];
            }

            $resolved_inputs[$exp[0]] = $rules;
        }

        return $resolved_inputs;
    }

}