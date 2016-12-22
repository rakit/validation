<?php

namespace Rakit\Validation;

class ErrorBag
{

    protected $messages = [];

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    public function add($key, $rule, $message)
    {
        if (!isset($this->messages[$key])) {
            $this->messages[$key] = [];
        }

        $this->messages[$key][$rule] = $message;
    }

    public function count()
    {
        return count($this->all());
    }

    public function has($key)
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
                return !empty($messages) AND isset($messages[$ruleName]);
            }
        }
    }

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

    public function get($key, $format = ':message')
    {
        list($key, $ruleName) = $this->parsekey($key);
        $results = [];
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);
            foreach($messages as $explicitKey => $keyMessages) {
                foreach ($keyMessages as $rule => $message) {
                    $results[$explicitKey][$rule] = $this->formatMessage($message, $format);
                }
            }
        } else {
            $keyMessages = isset($this->messages[$key])? $this->messages[$key] : [];
            foreach($keyMessages as $rule => $message) {
                if ($ruleName AND $ruleName != $rule) continue;
                $results[$rule] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    public function all($format = ':message')
    {
        $messages = $this->messages;
        $results = [];
        foreach($messages as $key => $keyMessages) {
            foreach($keyMessages as $message) {
                $results[] = $this->formatMessage($message, $format);
            }
        }
        return $results;
    }

    public function firstOfAll($format = ':message')
    {
        $messages = $this->messages;
        $results = [];
        foreach($messages as $key => $keyMessages) {
            $results[] = $this->formatMessage(array_shift($messages[$key]), $format);
        }
        return $results;
    }

    public function toArray()
    {
        return $this->messages;
    }

    protected function parseKey($key)
    {
        $expl = explode(':', $key, 2);
        $key = $expl[0];
        $ruleName = isset($expl[1])? $expl[1] : null;
        return [$key, $ruleName];
    }

    protected function isWildcardKey($key)
    {
        return false !== strpos($key, '*');
    }

    protected function filterMessagesForWildcardKey($key, $ruleName = null)
    {
        $messages = $this->messages;
        $pattern = preg_quote($key, '#');
        $pattern = str_replace('\*', '.*', $pattern);

        $filteredMessages = [];

        foreach ($messages as $k => $keyMessages) {
            if ((bool) preg_match('#^'.$pattern.'\z#u', $k) === false) {
                continue;
            }

            foreach($keyMessages as $rule => $message) {
                if ($ruleName AND $rule != $ruleName) continue;
                $filteredMessages[$k][$rule] = $message;
            }
        }

        return $filteredMessages;
    }

    protected function formatMessage($message, $format)
    {
        return str_replace(':message', $message, $format);
    }

}
