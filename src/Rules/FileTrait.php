<?php

namespace Rakit\Validation\Rules;

use InvalidArgumentException;

trait FileTrait
{

    /**
     * Check whether value is from $_FILES
     *
     * @param mixed $value
     */
    public function isValueFromUploadedFiles($value)
    {
        if (!is_array($value)) return false;

        $keys = ['name', 'type', 'tmp_name', 'size', 'error'];
        foreach($keys as $key) {
            if (!array_key_exists($key, $value)) return false;
        }

        return true;
    }

    public function isUploadedFile($value)
    {
        return $this->isValueFromUploadedFiles($value) AND is_uploaded_file($value['tmp_name']);
    }

    protected function getBytes($size)
    {
        if (is_int($size)) {
            return $size;
        }

        if (!is_string($size)) {
            throw new InvalidArgumentException("Size must be string or integer Bytes", 1);
        }

        if (!preg_match("/^(?<number>((\d+)?\.)?\d+)(?<format>(B|K|M|G|T|P)B?)?$/i", $size, $match)) {
            throw new InvalidArgumentException("Size is not valid format", 1);
        }

        $number = (float)$match['number'];
        $format = isset($match['format']) ? $match['format'] : '';

        switch (strtoupper($format)) {
            case "KB":
            case "K":
                return $number * 1024;

            case "MB":
            case "M":
                return $number * pow(1024, 2);

            case "GB":
            case "G":
                return $number * pow(1024, 3);

            case "TB":
            case "T":
                return $number * pow(1024, 4);

            case "PB":
            case "P":
                return $number * pow(1024, 5);

            default:
                return $number;
        }
    }
}
