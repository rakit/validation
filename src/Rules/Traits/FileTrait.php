<?php

namespace Rakit\Validation\Rules\Traits;

use InvalidArgumentException;

trait FileTrait
{

    /**
     * Check whether value is from $_FILES
     *
     * @param mixed $value
     * @return bool
     */
    public function isValueFromUploadedFiles($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = ['name', 'type', 'tmp_name', 'size', 'error'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check the $value is uploaded file
     *
     * @param mixed $value
     * @return bool
     */
    public function isUploadedFile($value): bool
    {
        return $this->isValueFromUploadedFiles($value) && is_uploaded_file($value['tmp_name']);
    }
}
