<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Url extends Rule
{

    protected $message = "The :attribute is not valid url";

    public function fillParameters(array $params)
    {
        if (count($params) == 1 AND is_array($params[0])) {
            $params = $params[0];
        }
        return $this->forScheme($params);
    }

    public function forScheme($schemes)
    {
        $this->params['schemes'] = (array) $schemes;
        return $this;
    }

    public function check($value)
    {
        $schemes = $this->parameter('schemes');

        if (!$schemes) {
            return $this->validateCommonScheme($value);
        } else {
            foreach ($schemes as $scheme) {
                $method = 'validate' . ucfirst($scheme) .'Scheme';
                if (method_exists($this, $method)) {
                    if ($this->{$method}($value)) {
                        return true;
                    }
                } elseif($this->validateCommonScheme($value, $scheme)) {
                    return true;
                }
            }

            return false;
        }
    }

    public function validateBasic($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function validateCommonScheme($value, $scheme = null)
    {
        if (!$scheme) {
            return $this->validateBasic($value) && (bool) preg_match("/^\w+:\/\//i", $value);
        } else {
            return $this->validateBasic($value) && (bool) preg_match("/^{$scheme}:\/\//", $value);
        }
    }

    public function validateMailtoScheme($value)
    {
        return $this->validateBasic($value) && preg_match("/^mailto:/", $value);
    }

    public function validateJdbcScheme($value)
    {
        return (bool) preg_match("/^jdbc:\w+:\/\//", $value);
    }

}
