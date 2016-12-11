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

    public function first($key)
    {
        if (empty($this->messages[$key])) return null;
        return array_values($this->messages[$key])[0];
    }

    public function last($key)
    {
        if (empty($this->messages[$key])) return null;
        return array_values($this->messages[$key])[count($this->messages[$key])-1];
    }

    public function get($key, $rule)
    {
        if (empty($this->messages[$key])) return null;
        if (empty($this->messages[$key][$rule])) return null;
        return $this->messages[$key][$rule];
    }

    public function all($format = ':message')
    {
        $messages = [];
        foreach($this->messages as $key => $keyMessages) {
            foreach($keyMessages as $msg) {
                $messages[] = str_replace(':message', $format, $msg);
            }
        }

        return $messages;
    }

    public function implode($glue, $format = ':message')
    {
        return implode($glue, $this->all($format));
    }

    public function firstOfAll()
    {
        $messages = [];
        foreach($this->messages as $key => $keyMessages) {
            $messages[] = $this->first($key);
        }
        return $messages;
    }

    public function lastOfAll()
    {
        $messages = [];
        foreach($this->messages as $key => $keyMessages) {
            $messages[] = $this->last($key);
        }
        return $messages;
    }

}