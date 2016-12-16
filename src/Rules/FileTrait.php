<?php

namespace Rakit\Validation\Rules;

use InvalidArgumentException;

trait FileTrait
{

    public function isUploadedFile($value)
    {
        return is_uploaded_file($value);
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
