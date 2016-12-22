<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use Rakit\Validation\MimeTypeGuesser;

class UploadedFile extends Rule
{
    use FileTrait;

    protected $message = "The :attribute is not valid";

    protected $maxSize = null;
    protected $minSize = null;
    protected $allowedTypes = [];

    public function fillParameters(array $params)
    {
        $this->minSize(array_shift($params));
        $this->maxSize(array_shift($params));
        $this->fileTypes($params);

        return $this;
    }

    public function maxSize($size)
    {
        $this->params['max_size'] = $size;
        return $this;
    }

    public function minSize($size)
    {
        $this->params['min_size'] = $size;
        return $this;
    }

    public function sizeBetween($min, $max)
    {
        $this->minSize($min);
        $this->maxSize($max);

        return $this;
    }

    public function fileTypes($types)
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }

        $this->params['allowed_types'] = $types;

        return $this;
    }

    public function check($value)
    {   
        $minSize = $this->parameter('min_size');
        $maxSize = $this->parameter('max_size');
        $allowedTypes = $this->parameter('allowed_types');

        // below is Required rule job
        if (!$this->isValueFromUploadedFiles($value) OR $value['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if (!$this->isUploadedFile($value)) {
            return false;
        }

        // just make sure there is no error
        if ($value['error']) return false;

        if ($minSize) {
            $bytesMinSize = $this->getBytes($minSize);
            if ($value['size'] < $bytesMinSize) {
                return false;
            }
        }

        if ($maxSize) {
            $bytesMaxSize = $this->getBytes($maxSize);
            if ($value['size'] > $bytesMaxSize) {
                return false;
            }
        }

        if (!empty($allowedTypes)) {
            $guesser = new MimeTypeGuesser;
            $ext = $guesser->getExtension($value['type']);
            unset($guesser);

            if (!in_array($ext, $allowedTypes)) {
                return false;
            }
        }

        return true;
    }
}
