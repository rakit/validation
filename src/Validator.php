<?php

namespace Rakit\Validation;

use Rakit\Validation\Rules\After;
use Rakit\Validation\Rules\Before;

class Validator
{

    protected $messages = [];

    protected $validators = [];

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        $this->registerBaseValidators();
    }

    public function setMessage($key, $message)
    {
        return $this->messages[$key] = $message;
    }

    public function setMessages()
    {
        array_merge($this->messages, $messages);
    }

    public function setValidator($key, Rule $rule)
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }

    public function getValidator($key)
    {
        return isset($this->validators[$key])? $this->validators[$key] : null;
    }

    public function validate(array $inputs, array $rules, array $messages = array())
    {
        $validation = $this->make($inputs, $rules, $messages);
        $validation->validate();
        return $validation;
    }

    public function make(array $inputs, array $rules, array $messages)
    {
        $messages = array_merge($this->messages, $messages);
        return new Validation($this, $inputs, $rules, $messages); 
    }

    public function __invoke($rule)
    {
        $args = func_get_args();
        $rule = array_shift($args);
        $params = $args;
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new RuleNotFoundException("Validator '{$rule}' is not registered", 1);
        }

        $clonedValidator = clone $validator;
        $clonedValidator->setParameters($params);

        return $clonedValidator;
    }

    protected function registerBaseValidators()
    {
        $baseValidator = [
            'required'          => new Rules\Required,
            'required_if'       => new Rules\RequiredIf,
            'email'             => new Rules\Email,
            'alpha'             => new Rules\Alpha,
            'numeric'           => new Rules\Numeric,
            'alpha_num'         => new Rules\AlphaNum,
            'alpha_dash'        => new Rules\AlphaDash,
            'in'                => new Rules\In,
            'not_in'            => new Rules\NotIn,
            'min'               => new Rules\Min,
            'max'               => new Rules\Max,
            'between'           => new Rules\Between,
            'url'               => new Rules\Url,
            'ip'                => new Rules\Ip,
            'ipv4'              => new Rules\Ipv4,
            'ipv6'              => new Rules\Ipv6,
            'array'             => new Rules\TypeArray,
            'same'              => new Rules\Same,
            'regex'             => new Rules\Regex,
            'date'              => new Rules\Date,
            'accepted'          => new Rules\Accepted,
            'present'           => new Rules\Present,
            'different'         => new Rules\Different,
            'uploaded_file'     => new Rules\UploadedFile,
            'before'            => new Before,
            'after'             => new After
        ];

        foreach($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    public function addValidator($ruleName, Rule $rule)
    {
        if (array_key_exists($ruleName, $this->validators)) {
            throw new RuleQuashException(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->setValidator($ruleName, $rule);
    }
}
