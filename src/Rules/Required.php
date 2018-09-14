<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Required extends Rule
{
    use FileTrait;

    protected $implicit = true;

    protected $message = "The :attribute is required";

    public function check($value)
    {
        $this->setAttributeAsRequired();

        if ($this->attribute AND $this->attribute->hasRule('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) AND $value['error'] != UPLOAD_ERR_NO_FILE;
        }

        if (is_string($value)) return mb_strlen(trim($value), 'UTF-8') > 0;
        if (is_array($value)) return count($value) > 0;
        return !is_null($value);
    }

    protected function setAttributeAsRequired()
    {
        if ($this->attribute) {
            $this->attribute->setRequired(true);
        }
    }

}
