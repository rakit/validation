<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use Rakit\Validation\MimeTypeGuesser;

class UploadedFile extends Rule
{

    protected $message = "The :attribute is not valid";

    protected $maxSize = null;
    protected $minSize = null;
    protected $allowedTypes = [];

    public function maxSize($size)
    {
        $this->maxSize = $size;
        return $this;
    }

    public function minSize($size)
    {
        $this->minSize = $size;
        return $this;
    }

    public function sizeBetween($min, $max)
    {
        $this->minSize = $min;
        $this->maxSize = $max;
        return $this;
    }

    public function fileTypes($types)
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }

        $this->allowedTypes = $types;

        return $this;
    }

    public function getParams()
    {
        return [$this->minSize, $this->maxSize, $this->allowedTypes];
    }

    public function check($value, array $params)
    {
        if (count($params) > 0) $this->minSize(array_shift($params));
        if (count($params) > 0) $this->maxSize(array_shift($params));
        if (count($params) > 0) $this->fileTypes($params);

        if (is_null($value)) {
            return true;
        }

        if (!static::isUploadedFile($value)) {
            return false;
        }

        // we validate this in Required rule
        if ($value['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if ($value['error']) return false;

        if ($this->minSize) {
            $minSize = $this->getBytes($this->minSize);
            if ($value['size'] < $minSize) {
                return false;
            }
        }

        if ($this->maxSize) {
            $maxSize = $this->getBytes($this->maxSize);
            if ($value['size'] > $maxSize) {
                return false;
            }
        }

        if (!empty($this->allowedTypes)) {
            $guesser = new MimeTypeGuesser;
            $ext = $guesser->getExtension($value['type']);
            unset($guesser);

            if (!in_array($ext, $this->allowedTypes)) {
                return false;
            }
        }

        return true;
    }

    public static function isUploadedFile($value)
    {
        if(!is_array($value)) return false;

        $required_keys = ['name', 'type', 'tmp_name', 'size', 'error'];
        foreach($required_keys as $key) {
            if(!isset($value[$key])) return false;
        }

        return $value;
    }

    protected function getBytes($size)
    {
        if (is_int($size)) return $size;
        if (!is_string($size)) {
            throw new \InvalidArgumentException("Size must be string or integer Bytes", 1);
        }

        if(!preg_match("/^(?<number>((\d+)?\.)?\d+)(?<format>(B|K|M|G|T|P)B?)?$/i", $size, $match)) {
            throw new \InvalidArgumentException("Size is not valid format", 1);
        }

        $number = (float) $match['number'];
        $format = isset($match['format'])? $match['format'] : '';

        switch (strtoupper($format)) {
            case "KB":
            case "K":
                return $number*1024;
            case "MB":
            case "M":
                return $number*pow(1024,2);
            case "GB":
            case "G":
                return $number*pow(1024,3);
            case "TB":
            case "T":
                return $number*pow(1024,4);
            case "PB":
            case "P":
                return $number*pow(1024,5);
            default:
                return $number;
        }
    }

}
