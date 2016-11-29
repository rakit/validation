<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use Rakit\Validation\MimeTypeGuesser;

class UploadedFile extends Rule
{

    protected $message = "The :attribute is not valid";

    protected $max_size = null;
    protected $min_size = null;
    protected $allowed_types = [];

    public function maxSize($size)
    {
        $this->max_size = $size;
        return $this;
    }

    public function minSize($size)
    {
        $this->min_size = $size;
        return $this;
    }

    public function sizeBetween($min, $max)
    {
        $this->min_size = $min;
        $this->max_size = $max;
        return $this;
    }

    public function fileTypes($types)
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }

        $this->allowed_types = $types;

        return $this;
    }

    public function getParams()
    {
        return [$this->min_size, $this->max_size, $this->allowed_types];
    }

    public function check($value, array $params)
    {
        if (isset($params[0])) $this->minSize($params[0]);
        if (isset($params[1])) $this->minSize($params[1]);
        if (isset($params[2])) $this->fileTypes($params[2]);

        if (!static::isUploadedFile($value)) {
            return false;
        }

        if ($this->min_size) {
            $min_size = $this->getBytes($this->min_size);
            if ($value['size'] < $min_size) {
                return false;
            }
        }

        if ($this->max_size) {
            $max_size = $this->getBytes($this->max_size);
            if ($value['size'] > $max_size) {
                return false;
            }
        }

        if (!empty($this->allowed_types)) {
            $guesser = new MimeTypeGuesser;
            $ext = $guesser->getExtension($value['type']);
            unset($guesser);

            if (!in_array($ext, $this->allowed_types)) {
                return false;
            }
        }

        return true;
    }

    public static function isUploadedFile($value)
    {
        if(!is_array($value)) return false;
        if(!empty($value['error'])) return false;

        $required_keys = ['name', 'type', 'tmp_name', 'size'];
        foreach($required_keys as $key) {
            if(!isset($value[$key])) return false;
        }

        return true;
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
