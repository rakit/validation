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

<<<<<<< HEAD
    public function testBeforeRule()
    {
        $data = ["date" => (new DateTime())->format('Y-m-d')];

        $validator = $this->validator->make($data, [
            'date' => 'required|before:tomorrow'
        ], []);

        $validator->validate();

        $this->assertTrue($validator->passes());

        $validator2 = $this->validator->make($data, [
            'date' => "required|before:last week"
        ],[]);

        $validator2->validate();

        $this->assertFalse($validator2->passes());
    }

    public function testAfterRule()
    {
        $data = ["date" => (new DateTime())->format('Y-m-d')];

        $validator = $this->validator->make($data, [
            'date' => 'required|after:yesterday'
        ], []);

        $validator->validate();

        $this->assertTrue($validator->passes());

        $validator2 = $this->validator->make($data, [
            'date' => "required|after:next year"
        ],[]);

        $validator2->validate();

        $this->assertFalse($validator2->passes());
=======
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

    public function testIgnoreOtherRulesWhenAttributeIsNotRequired()
    {
        $validation = $this->validator->validate([
            'an_empty_file' => [
                'name' => '',
                'type' => '',
                'size' => '',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE
            ],
            'required_if_field' => null,
        ], [
            'optional_field' => 'ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email',
            'an_empty_file' => 'uploaded_file'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testDontIgnoreOtherRulesWhenValueIsNotEmpty()
    {
        $validation = $this->validator->validate([
            'an_error_file' => [
                'name' => 'foo',
                'type' => 'text/plain',
                'size' => 10000,
                'tmp_name' => '/tmp/foo',
                'error' => UPLOAD_ERR_CANT_WRITE
            ],
            'optional_field' => 'invalid ip address',
            'required_if_field' => 'invalid email',
        ], [
            'an_error_file' => 'uploaded_file',
            'optional_field' => 'ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email'
        ]);

        $this->assertEquals($validation->errors()->count(), 4);
    }

    public function testDontIgnoreOtherRulesWhenAttributeIsRequired()
    {
        $validation = $this->validator->validate([
            'optional_field' => 'have a value',
            'required_if_field' => 'invalid email',
            'some_value' => 1
        ], [
            'optional_field' => 'required|ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email'
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->count(), 3);
        $this->assertNotNull($errors->get('optional_field', 'ipv4'));
        $this->assertNotNull($errors->get('optional_field', 'in'));
        $this->assertNotNull($errors->get('required_if_field', 'email'));
>>>>>>> master
    }
}
