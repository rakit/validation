<?php


class Json extends \Rakit\Validation\Rule
{

    protected $message = "This :attribute is not a valid json object";

    public function check($value)
    {

        json_decode($value);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return true;
    }
}
