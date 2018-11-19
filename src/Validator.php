<?php

namespace Rakit\Validation;

class Validator
{

    /** @var array */
    protected $messages = [];

    /** @var array */
    protected $validators = [];

    /** @var bool */
    protected $allowRuleOverride = false;

    /** @var bool */
    protected $useHumanizedKeys = true;

    /**
     * Constructor
     *
     * @param array $messages
     * @return void
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        $this->registerBaseValidators();
    }

    /**
     * Given $key and $message to set message
     *
     * @param mixed $key
     * @param mixed $message
     * @return void
     */
    public function setMessage($key, $message)
    {
        return $this->messages[$key] = $message;
    }

    /**
     * Given $messages and set multiple messages
     *
     * @param array $messages
     * @return void
     */
    public function setMessages($messages)
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * Given $key and $rule to ser validator
     *
     * @param mixed $key
     * @param Rule $rule
     * @return void
     */
    public function setValidator($key, Rule $rule)
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }

    /**
     * Given $key to get validator
     *
     * @param mixed $key
     * @return mixed
     */
    public function getValidator($key)
    {
        return isset($this->validators[$key])? $this->validators[$key] : null;
    }

    /**
     * Validate $inputs
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function validate(array $inputs, array $rules, array $messages = [])
    {
        $validation = $this->make($inputs, $rules, $messages);
        $validation->validate();
        return $validation;
    }

    /**
     * Given $inputs, $rules and $messages to make the Validation class instance
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function make(array $inputs, array $rules, array $messages = [])
    {
        $messages = array_merge($this->messages, $messages);
        return new Validation($this, $inputs, $rules, $messages);
    }

    /**
     * magic invoke method
     *
     * @param mixed $rule
     * @return mixed
     * @throws RuleNotFoundException
     */
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

    /**
     * Initialize base validators array
     *
     * @return void
     */
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

        foreach ($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    /**
     * Given $ruleName and $rule to sdd new validator
     *
     * @param mixed $ruleName
     * @param Rule $rule
     * @return void
     */
    public function addValidator($ruleName, Rule $rule)
    {
        if (!$this->allowRuleOverride && array_key_exists($ruleName, $this->validators)) {
            throw new RuleQuashException(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->setValidator($ruleName, $rule);
    }

    /**
     * Set rule can allow to be overrided
     *
     * @param boolean $status
     * @return void
     */
    public function allowRuleOverride($status = false)
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Set this can use humanize keys
     *
     * @param boolean $useHumanizedKeys
     * @return void
     */
    public function setUseHumanizedKeys($useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    /**
     * Get $this->useHumanizedKeys value
     *
     * @return void
     */
    public function getUseHumanizedKeys()
    {
        return $this->useHumanizedKeys;
    }
}
