<?php

namespace Rakit\Validation;

class ErrorBag
{

    /** @var array */
    protected $messages = [];

    /**
     * Constructor
     *
     * @param array $messages
     * @return void
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    /**
     * Add key,rule and message
     *
     * @param mixed $key
     * @param mixed $rule
     * @param mixed $message
     * @return void
     */
    public function add($key, $rule, $message)
    {
        if (!isset($this->messages[$key])) {
            $this->messages[$key] = [];
        }

        $this->messages[$key][$rule] = $message;
    }

    /**
     * Get results count
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Check given key is existed
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key): bool
    {
        list($key, $ruleName) = $this->parsekey($key);
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);
            return count(Helper::arrayDot($messages)) > 0;
        } else {
            $messages = isset($this->messages[$key])? $this->messages[$key] : null;

            if (!$ruleName) {
                return !empty($messages);
            } else {
                return !empty($messages) and isset($messages[$ruleName]);
            }
        }
    }

    /**
     * Get the first value of array
     *
     * @param mixed $key
     * @return mixed
     */
    public function first($key)
    {
        list($key, $ruleName) = $this->parsekey($key);
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);
            $flattenMessages = Helper::arrayDot($messages);
            return array_shift($flattenMessages);
        } else {
            $keyMessages = isset($this->messages[$key])? $this->messages[$key] : [];

            if (empty($keyMessages)) {
                return null;
            }

            if ($ruleName) {
                return isset($keyMessages[$ruleName])? $keyMessages[$ruleName] : null;
            } else {
                return array_shift($keyMessages);
            }
        }
    }

    /**
     * Given $key and $format then get the results
     *
     * @param mixed $key
     * @param string $format
     * @return array
     */
    public function get($key, string $format = ':message'): array
    {
        list($key, $ruleName) = $this->parsekey($key);
        $results = [];
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);
            foreach ($messages as $explicitKey => $keyMessages) {
                foreach ($keyMessages as $rule => $message) {
                    $results[$explicitKey][$rule] = $this->formatMessage($message, $format);
                }
            }
        } else {
            $keyMessages = isset($this->messages[$key])? $this->messages[$key] : [];
            foreach ($keyMessages as $rule => $message) {
                if ($ruleName and $ruleName != $rule) {
                    continue;
                }
                $results[$rule] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    /**
     * Get all results
     *
     * @param string $format
     * @return array
     */
    public function all(string $format = ':message'): array
    {
        $messages = $this->messages;
        $results = [];
        foreach ($messages as $key => $keyMessages) {
            foreach ($keyMessages as $message) {
                $results[] = $this->formatMessage($message, $format);
            }
        }
        return $results;
    }

    /**
     * Get the first result of results
     *
     * @param string $format
     * @param boolean $dotNotation
     * @return array
     */
    public function firstOfAll(string $format = ':message', bool $dotNotation = false): array
    {
        $messages = $this->messages;
        $results = [];
        foreach ($messages as $key => $keyMessages) {
            if ($dotNotation) {
                $results[$key] = $this->formatMessage(array_shift($messages[$key]), $format);
            } else {
                Helper::arraySet($results, $key, $this->formatMessage(array_shift($messages[$key]), $format));
            }
        }
        return $results;
    }

    /**
     * Get messagees
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->messages;
    }

    /**
     * Parse $key to get the array of $key and $ruleName
     *
     * @param mixed $key
     * @return array
     */
    protected function parseKey($key): array
    {
        $expl = explode(':', $key, 2);
        $key = $expl[0];
        $ruleName = isset($expl[1])? $expl[1] : null;
        return [$key, $ruleName];
    }

    /**
     * Check the $key is wildcard
     *
     * @param mixed $key
     * @return bool
     */
    protected function isWildcardKey($key): bool
    {
        return false !== strpos($key, '*');
    }

    /**
     * Filter messages with wildcard key
     *
     * @param mixed $key
     * @param mixed $ruleName
     * @return array
     */
    protected function filterMessagesForWildcardKey($key, $ruleName = null): array
    {
        $messages = $this->messages;
        $pattern = preg_quote($key, '#');
        $pattern = str_replace('\*', '.*', $pattern);

        $filteredMessages = [];

        foreach ($messages as $k => $keyMessages) {
            if ((bool) preg_match('#^'.$pattern.'\z#u', $k) === false) {
                continue;
            }

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName and $rule != $ruleName) {
                    continue;
                }
                $filteredMessages[$k][$rule] = $message;
            }
        }

        return $filteredMessages;
    }

    /**
     * Get formatted message
     *
     * @param mixed $message
     * @param mixed $format
     * @return string
     */
    protected function formatMessage($message, $format): string
    {
        return str_replace(':message', $message, $format);
    }
}
