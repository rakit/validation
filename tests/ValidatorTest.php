<?php

use Rakit\Validation\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    protected $validator;

    protected function setUp()
    {
        $this->validator = new Validator;
    }

    public function testPasses()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => 'required|email'
        ]);

        $this->assertTrue($validation->passes());

        $validation = $this->validator->validate([], [
            'email' => 'required|email'
        ]);

        $this->assertFalse($validation->passes());
    }

    public function testFails()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => 'required|email'
        ]);

        $this->assertFalse($validation->fails());

        $validation = $this->validator->validate([], [
            'email' => 'required|email'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testValidationMessages()
    {
        $this->validator->setMessage('required', ':attribute harus diisi');
        $validation = $this->validator->validate([
            'email' => ''
        ], [
            'email' => 'required|email'
        ]);

        $this->assertEquals($validation->errors()->count(), 2);

        $first_error = $validation->errors()->first('email');
        $this->assertEquals($first_error, 'Email harus diisi');
        $error_required = $validation->errors()->get('email', 'required');
        $this->assertEquals($error_required, 'Email harus diisi');
    }

}
