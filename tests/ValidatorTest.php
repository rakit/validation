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

    public function testRequiredUploadedFile()
    {
        $empty_file = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $validation = $this->validator->validate([
            'file' => $empty_file
        ], [
            'file' => 'required|uploaded_file' 
        ]);

        $errors = $validation->errors();
        $this->assertFalse($validation->passes());
        $this->assertNotNull($errors->get('file', 'required'));
    }

    public function testOptionalUploadedFile()
    {
       $empty_file = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $validation = $this->validator->validate([
            'file' => $empty_file
        ], [
            'file' => 'uploaded_file' 
        ]);
        $this->assertTrue($validation->passes());
    }

    /**
     * @dataProvider getSamplesMissingKeyFromUploadedFileValue
     */    
    public function testMissingKeyUploadedFile($uploaded_file)
    {
        $validation = $this->validator->validate([
            'file' => $uploaded_file
        ], [
            'file' => 'required|uploaded_file' 
        ]);

        $errors = $validation->errors();
        $this->assertFalse($validation->passes());
        $this->assertNotNull($errors->get('file', 'required'));
    }

    public function getSamplesMissingKeyFromUploadedFileValue()
    {
        $valid_uploaded_file = [
            'name' => 'foo',
            'type' => 'text/plain',
            'size' => 1000,
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK
        ];

        $samples = [];
        foreach($valid_uploaded_file as $key => $value) {
            $uploaded_file = $valid_uploaded_file;
            unset($uploaded_file[$key]);
            $samples[] = $uploaded_file;
        }
        return $samples;
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

    public function testIgnoreOtherRulesWhenAttributeIsNotRequired()
    {
        $validation = $this->validator->validate([
            'an_empty_file' => [
                'name' => '',
                'type' => '',
                'size' => '',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE,
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
    }

    public function testRegisterRulesUsingInvokes()
    {
        $validator = $this->validator;
        $validation = $this->validator->validate([
            'a_field' => null,
            'a_number' => 1000,
            'a_same_number' => 1000,
            'a_date' => '2016-12-06',
            'a_file' => [
                'name' => 'foo',
                'type' => 'text/plain',
                'size' => 10000,
                'tmp_name' => '/tmp/foo',
                'error' => UPLOAD_ERR_OK
            ]
        ], [
            'a_field' => [
                $validator('required')->message('1'),
            ],
            'a_number' => [
                $validator('min', 2000)->message('2'),
                $validator('max', 5)->message('3'),
                $validator('between', 1, 5)->message('4'),
                $validator('in', [1, 2, 3, 4, 5])->message('5'),
                $validator('not_in', [1000, 2, 3, 4, 5])->message('6'),
                $validator('same', 'a_date')->message('7'),
                $validator('different', 'a_same_number')->message('8'),
            ],
            'a_date' => [
                $validator('date', 'd-m-Y')->message('9')
            ],
            'a_file' => [
                $validator('uploaded_file', 20000)->message('10')
            ]
        ]);

        $errors = $validation->errors();
        $this->assertEquals($errors->implode(','), '1,2,3,4,5,6,7,8,9,10');
    }

    public function testArrayAssocValidation()
    {
        $validation = $this->validator->validate([
            'user' => [
                'email' => 'invalid email',
                'name' => 'John Doe',
                'age' => 16
            ]
        ], [
            'user.email' => 'required|email',
            'user.name' => 'required',
            'user.age' => 'required|min:18'
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->count(), 2);

        $this->assertNotNull($errors->get('user.email', 'email'));
        $this->assertNotNull($errors->get('user.age', 'min'));
        $this->assertNull($errors->get('user.name', 'required'));
    }

    public function testArrayValidation()
    {
        $validation = $this->validator->validate([
            'cart_items' => [
                ['id_product' => 1, 'qty' => 10],
                ['id_product' => null, 'qty' => 10],
                ['id_product' => 3, 'qty' => null],
                ['id_product' => 4, 'qty' => 'foo'],
                ['id_product' => 'foo', 'qty' => 10],
            ]
        ], [
            'cart_items.*.id_product' => 'required|numeric',
            'cart_items.*.qty' => 'required|numeric'
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->count(), 4);

        $this->assertNotNull($errors->get('cart_items.1.id_product', 'required'));
        $this->assertNotNull($errors->get('cart_items.2.qty', 'required'));
        $this->assertNotNull($errors->get('cart_items.3.qty', 'numeric'));
        $this->assertNotNull($errors->get('cart_items.4.id_product', 'numeric'));
    }

    public function testSetCustomMessagesInValidator()
    {
        $this->validator->setMessages([
            'required' => 'foo',
            'email' => 'bar',
            'comments.*.text' => 'baz'
        ]);

        $this->validator->setMessage('numeric', 'baz');

        $validation = $this->validator->validate([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $errors = $validation->errors();
        $this->assertEquals($errors->get('foo', 'required'), 'foo');
        $this->assertEquals($errors->get('email', 'email'), 'bar');
        $this->assertEquals($errors->get('something', 'numeric'), 'baz');
        $this->assertEquals($errors->get('comments.0.text', 'required'), 'baz');
    }

    public function testSetCustomMessagesInValidation()
    {
        $validation = $this->validator->make([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $validation->setMessages([
            'required' => 'foo',
            'email' => 'bar',
            'comments.*.text' => 'baz'
        ]);

        $validation->setMessage('numeric', 'baz');

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals($errors->get('foo', 'required'), 'foo');
        $this->assertEquals($errors->get('email', 'email'), 'bar');
        $this->assertEquals($errors->get('something', 'numeric'), 'baz');
        $this->assertEquals($errors->get('comments.0.text', 'required'), 'baz');
    }

    public function testSetAttributeAliases()
    {
        $validation = $this->validator->make([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $validation->setMessages([
            'required' => ':attribute foo',
            'email' => ':attribute bar',
            'numeric' => ':attribute baz',
            'comments.*.text' => ':attribute qux'
        ]);

        $validation->setAliases([
            'foo' => 'Foo',
            'email' => 'Bar'
        ]);

        $validation->setAlias('something', 'Baz');
        $validation->setAlias('comments.*.text', 'Qux');

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals($errors->get('foo', 'required'), 'Foo foo');
        $this->assertEquals($errors->get('email', 'email'), 'Bar bar');
        $this->assertEquals($errors->get('something', 'numeric'), 'Baz baz');
        $this->assertEquals($errors->get('comments.0.text', 'required'), 'Qux qux');
    }
}
