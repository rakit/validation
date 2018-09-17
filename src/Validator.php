<?php

namespace Rakit\Validation;

class Validator
{

    protected $messages = [];

    protected $validators = [];

    protected $allowRuleOverride = false;

    protected $useHumanizedKeys = true;

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        $this->registerBaseValidators();
    }

    public function setMessage($key, $message)
    {
        return $this->messages[$key] = $message;
    }

    public function setMessages($messages)
    {
        $this->messages = array_merge($this->messages, $messages);
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

    public function make(array $inputs, array $rules, array $messages = array())
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
        $clonedValidator->fillParameters($params);

        return $clonedValidator;
    }

    protected function registerBaseValidators()
    {
        $baseValidator = [
            'required'                  => new Rules\Required,
            'required_if'               => new Rules\RequiredIf,
            'required_unless'           => new Rules\RequiredUnless,
            'required_with'             => new Rules\RequiredWith,
            'required_without'          => new Rules\RequiredWithout,
            'required_with_all'         => new Rules\RequiredWithAll,
            'required_without_all'      => new Rules\RequiredWithoutAll,
            'email'                     => new Rules\Email,
            'alpha'                     => new Rules\Alpha,
            'numeric'                   => new Rules\Numeric,
            'alpha_num'                 => new Rules\AlphaNum,
            'alpha_dash'                => new Rules\AlphaDash,
            'in'                        => new Rules\In,
            'not_in'                    => new Rules\NotIn,
            'min'                       => new Rules\Min,
            'max'                       => new Rules\Max,
            'between'                   => new Rules\Between,
            'url'                       => new Rules\Url,
            'integer'                   => new Rules\Integer,
            'ip'                        => new Rules\Ip,
            'ipv4'                      => new Rules\Ipv4,
            'ipv6'                      => new Rules\Ipv6,
            'array'                     => new Rules\TypeArray,
            'same'                      => new Rules\Same,
            'regex'                     => new Rules\Regex,
            'date'                      => new Rules\Date,
            'accepted'                  => new Rules\Accepted,
            'present'                   => new Rules\Present,
            'different'                 => new Rules\Different,
            'uploaded_file'             => new Rules\UploadedFile,
            'callback'                  => new Rules\Callback,
            'before'                    => new Rules\Before,
            'after'                     => new Rules\After,
            'lowercase'                 => new Rules\Lowercase,
            'uppercase'                 => new Rules\Uppercase,
            'json'                      => new Rules\Json,
            'digits'                    => new Rules\Digits,
            'digits_between'            => new Rules\DigitsBetween,
            'defaults'                  => new Rules\Defaults,
            'default'                   => new Rules\Defaults, // alias of defaults
        ];

        foreach($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    public function addValidator($ruleName, Rule $rule)
    {
        if (!$this->allowRuleOverride && array_key_exists($ruleName, $this->validators)) {
            throw new RuleQuashException(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->setValidator($ruleName, $rule);
    }

    public function allowRuleOverride($status = false)
    {
        $this->allowRuleOverride = $status;
    }

    public function setUseHumanizedKeys($useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    public function getUseHumanizedKeys()
    {
        return $this->useHumanizedKeys;
    }
}
