<?php

use Rakit\Validation\Validator;

require_once 'Fixtures/Json.php';
require_once 'Fixtures/Required.php';

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

    public function testRequiredIfRule()
    {
        $v1 = $this->validator->validate([
            'a' => '',
            'b' => '',
        ], [
            'b' => 'required_if:a,1'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_if:a,1'
        ]);

        $this->assertFalse($v2->passes());
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

        $this->assertEquals($validation->errors()->count(), 1);

        $first_error = $validation->errors()->first('email');
        $this->assertEquals($first_error, 'Kolom email tidak boleh kosong');
        $error_required = $validation->errors()->get('email', 'required');
        $this->assertEquals($error_required, 'Kolom email tidak boleh kosong');
    }

    /**
     * @expectedException \Rakit\Validation\RuleNotFoundException
     */
    public function testNonExistentValidationRule()
    {
        $validation = $this->validator->make([
            'name' => "some name"
        ], [
            'name' => 'required|xxx'
        ],[
            'name.required' => "Fill in your name",
            'xxx' => "Oops"
        ]);

        $validation->validate();
    }

    public function testNewValidationRuleCanBeAdded()
    {

        $this->validator->addValidator('json', new Json());

        $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

        $validation = $this->validator->make($data, ['s' => 'json'], []);

        $validation->validate();

        $this->assertTrue($validation->passes());
    }

    /**
     * @expectedException Rakit\Validation\RuleQuashException
     */
    public function testInternalValidationRuleCannotBeOverridden()
    {

        $this->validator->addValidator('required', new Required());

        $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

        $validation = $this->validator->make($data, ['s' => 'required'], []);

        $validation->validate();
    }

    public function testIgnoreNextRulesWhenImplicitRulesFails()
    {
        $validation = $this->validator->validate([
            'some_value' => 1
        ], [
            'required_field' => 'required|numeric|min:6',
            'required_if_field' => 'required_if:some_value,1|numeric|min:6',
            'must_present_field' => 'present|numeric|min:6',
            'must_accepted_field' => 'accepted|numeric|min:6'
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->count(), 4);

        $this->assertNotNull($errors->get('required_field', 'required'));
        $this->assertNull($errors->get('required_field', 'numeric'));
        $this->assertNull($errors->get('required_field', 'min'));

        $this->assertNotNull($errors->get('required_if_field', 'required_if'));
        $this->assertNull($errors->get('required_if_field', 'numeric'));
        $this->assertNull($errors->get('required_if_field', 'min'));

        $this->assertNotNull($errors->get('must_present_field', 'present'));
        $this->assertNull($errors->get('must_present_field', 'numeric'));
        $this->assertNull($errors->get('must_present_field', 'min'));

        $this->assertNotNull($errors->get('must_accepted_field', 'accepted'));
        $this->assertNull($errors->get('must_accepted_field', 'numeric'));
        $this->assertNull($errors->get('must_accepted_field', 'min'));
    }
}
