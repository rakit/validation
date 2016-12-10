<?php


class Required extends \Rakit\Validation\Rule
{

    public function check($value, array $params)
    {
        return true;
    }
}
