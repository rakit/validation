<?php

namespace Rakit\Validation;

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
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new Exception("Validator '{$rule}' is not registered", 1);
        }

        return clone $validator;
    }

    protected function registerBaseValidators()
    {
        $base_validators = [
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
            'uploaded_file'     => new Rules\UploadedFile,
        ];

        foreach($base_validators as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

}