<?php

namespace Rakit\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var Validation
     */
    public $validation;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 422;

    /**
     * Create a new exception instance.
     *
     * @param Validation $validation
     * @return void
     */
    public function __construct($validation)
    {
        parent::__construct(static::summarize($validation));

        $this->validation = $validation;
    }

    /**
     * Create an error message summary from the validation errors.
     *
     * @param Validation $validation
     * @return string
     */
    protected static function summarize($validation)
    {
        $messages = $validation->errors()->all();

        if (! count($messages) || ! is_string($messages[0])) {
            return 'The given data was invalid.';
        }

        $message = array_shift($messages);

        if ($count = $validation->errors()->count()) {
            $pluralized = $count === 1 ? 'error' : 'errors';

            $message .= ' '."and $count more $pluralized";
        }

        return $message;
    }

    /**
     * Get all the validation error messages.
     *
     * @return array
     */
    public function getErrors():array
    {
        return $this->validation->errors()->toArray();
    }

    /**
     * Set the HTTP status code to be used for the response.
     *
     * @param  int  $status
     * @return $this
     */
    public function status($status)
    {
        $this->status = $status;

        return $this;
    }

}
