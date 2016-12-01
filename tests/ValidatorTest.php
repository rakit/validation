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

    public function testSkipEmptyRule()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => [
                null,
                'email'
            ]
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testRequireUploadedFile()
    {
        $empty_file = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];
        $v1 = $this->validator->validate([
            'file' => $empty_file
        ], [
            'file' => 'required|uploaded_file'
        ]);
        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'file' => $empty_file
        ], [
            'file' => 'uploaded_file'
        ]);
        $this->assertTrue($v2->passes());
    }

    public function testRulePresent()
    {
        $v1 = $this->validator->validate([
        ], [
            'something' => 'present'
        ]);
        $this->assertFalse($v1->passes());


        $v2 = $this->validator->validate([
            'something' => 10
        ], [
            'something' => 'present'
        ]);
        $this->assertTrue($v2->passes());
    }

    public function testValidationMessages()
    {
        $validation = $this->validator->make([
            'email' => ''
        ], [
            'email' => 'required|email'
        ], [
            'email.required' => 'Kolom email tidak boleh kosong',
            'required' => ':attribute harus diisi'
        ]);

        $validation->setAlias('email', 'e-mail');
        $validation->validate();

        $this->assertEquals($validation->errors()->count(), 2);

        $first_error = $validation->errors()->first('email');
        $this->assertEquals($first_error, 'Kolom email tidak boleh kosong');
        $error_required = $validation->errors()->get('email', 'required');
        $this->assertEquals($error_required, 'Kolom email tidak boleh kosong');
    }

}
