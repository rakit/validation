<?php

namespace Rakit\Validation\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Rakit\Validation\Rule;
use Rakit\Validation\Rules\UploadedFile;
use Rakit\Validation\Validator;

class ValidatorTest extends TestCase
{
    /** @var Validator */
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
        $this->assertNotNull($errors->first('file:required'));
    }

    public function testOptionalUploadedFile()
    {
        $emptyFile = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $validation = $this->validator->validate([
            'file' => $emptyFile
        ], [
            'file' => 'uploaded_file'
        ]);
        $this->assertTrue($validation->passes());
    }

    /**
     * @dataProvider getSamplesMissingKeyFromUploadedFileValue
     */
    public function testMissingKeyUploadedFile($uploadedFile)
    {
        $validation = $this->validator->validate([
            'file' => $uploadedFile
        ], [
            'file' => 'required|uploaded_file'
        ]);

        $errors = $validation->errors();
        $this->assertFalse($validation->passes());
        $this->assertNotNull($errors->first('file:required'));
    }

    public function getSamplesMissingKeyFromUploadedFileValue()
    {
        $validUploadedFile = [
            'name' => 'foo',
            'type' => 'text/plain',
            'size' => 1000,
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK
        ];

        $samples = [];
        foreach ($validUploadedFile as $key => $value) {
            $uploadedFile = $validUploadedFile;
            unset($uploadedFile[$key]);
            $samples[] = $uploadedFile;
        }
        return $samples;
    }

    public function testValidationShouldCorrectlyResolveMultipleFileUploads()
    {
        // Test from input files:
        // <input type="file" name="photos[]"/>
        // <input type="file" name="photos[]"/>
        $sampleInputFiles = [
            'photos' => [
                'name' => [
                    'a.png',
                    'b.jpeg',
                ],
                'type' => [
                    'image/png',
                    'image/jpeg',
                ],
                'size' => [
                    1000,
                    2000,
                ],
                'tmp_name' => [
                    __DIR__.'/a.png',
                    __DIR__.'/b.jpeg',
                ],
                'error' => [
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_OK,
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->fileTypes('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'photos.*' => ['required', $uploadedFileRule]
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'photos' => [
                1 => [
                    'name' => 'b.jpeg',
                    'type' => 'image/jpeg',
                    'size' => 2000,
                    'tmp_name' => __DIR__.'/b.jpeg',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'photos' => [
                0 => [
                    'name' => 'a.png',
                    'type' => 'image/png',
                    'size' => 1000,
                    'tmp_name' => __DIR__.'/a.png',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
    }

    public function testValidationShouldCorrectlyResolveAssocFileUploads()
    {
        // Test from input files:
        // <input type="file" name="photos[foo]"/>
        // <input type="file" name="photos[bar]"/>
        $sampleInputFiles = [
            'photos' => [
                'name' => [
                   'foo' => 'a.png',
                   'bar' => 'b.jpeg',
                ],
                'type' => [
                   'foo' => 'image/png',
                   'bar' => 'image/jpeg',
                ],
                'size' => [
                   'foo' => 1000,
                   'bar' => 2000,
                ],
                'tmp_name' => [
                   'foo' => __DIR__.'/a.png',
                   'bar' => __DIR__.'/b.jpeg',
                ],
                'error' => [
                   'foo' => UPLOAD_ERR_OK,
                   'bar' => UPLOAD_ERR_OK,
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->fileTypes('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'photos.foo' => ['required', clone $uploadedFileRule],
            'photos.bar' => ['required', clone $uploadedFileRule],
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'photos' => [
                'bar' => [
                    'name' => 'b.jpeg',
                    'type' => 'image/jpeg',
                    'size' => 2000,
                    'tmp_name' => __DIR__.'/b.jpeg',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'photos' => [
                'foo' => [
                    'name' => 'a.png',
                    'type' => 'image/png',
                    'size' => 1000,
                    'tmp_name' => __DIR__.'/a.png',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
    }

    public function testValidationShouldCorrectlyResolveComplexFileUploads()
    {
        // Test from input files:
        // <input type="file" name="files[foo][bar][baz]"/>
        // <input type="file" name="files[foo][bar][qux]"/>
        // <input type="file" name="files[photos][]"/>
        // <input type="file" name="files[photos][]"/>
        $sampleInputFiles = [
            'files' => [
                'name' => [
                   'foo' => [
                        'bar' => [
                            'baz' => 'foo-bar-baz.jpeg',
                            'qux' => 'foo-bar-qux.png',
                        ]
                   ],
                   'photos' => [
                        'photos-0.png',
                        'photos-1.jpeg',
                   ]
                ],
                'type' => [
                    'foo' => [
                        'bar' => [
                            'baz' => 'image/jpeg',
                            'qux' => 'image/png',
                        ]
                    ],
                    'photos' => [
                        'image/png',
                        'image/jpeg',
                    ]
                ],
                'size' => [
                   'foo' => [
                        'bar' => [
                            'baz' => 500,
                            'qux' => 750,
                        ]
                    ],
                    'photos' => [
                        1000,
                        2000,
                    ]
                ],
                'tmp_name' => [
                    'foo' => [
                        'bar' => [
                            'baz' => __DIR__.'/foo-bar-baz.jpeg',
                            'qux' => __DIR__.'/foo-bar-qux.png',
                        ]
                    ],
                    'photos' => [
                        __DIR__.'/photos-0.png',
                        __DIR__.'/photos-1.jpeg',
                    ]
                ],
                'error' => [
                   'foo' => [
                        'bar' => [
                            'baz' => UPLOAD_ERR_OK,
                            'qux' => UPLOAD_ERR_OK,
                        ]
                    ],
                    'photos' => [
                        UPLOAD_ERR_OK,
                        UPLOAD_ERR_OK,
                    ]
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->fileTypes('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'files.foo.bar.baz' => ['required', clone $uploadedFileRule],
            'files.foo.bar.qux' => ['required', clone $uploadedFileRule],
            'files.photos.*' => ['required', clone $uploadedFileRule],
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'files' => [
                'foo' => [
                    'bar' => [
                        'baz' => [
                            'name' => 'foo-bar-baz.jpeg',
                            'type' => 'image/jpeg',
                            'size' => 500,
                            'tmp_name' => __DIR__.'/foo-bar-baz.jpeg',
                            'error' => UPLOAD_ERR_OK,
                        ]
                    ]
                ],
                'photos' => [
                    1 => [
                        'name' => 'photos-1.jpeg',
                        'type' => 'image/jpeg',
                        'size' => 2000,
                        'tmp_name' => __DIR__.'/photos-1.jpeg',
                        'error' => UPLOAD_ERR_OK,
                    ]
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'files' => [
                'foo' => [
                    'bar' => [
                        'qux' => [
                            'name' => 'foo-bar-qux.png',
                            'type' => 'image/png',
                            'size' => 750,
                            'tmp_name' => __DIR__.'/foo-bar-qux.png',
                            'error' => UPLOAD_ERR_OK,
                        ]
                    ]
                ],
                'photos' => [
                    0 => [
                        'name' => 'photos-0.png',
                        'type' => 'image/png',
                        'size' => 1000,
                        'tmp_name' => __DIR__.'/photos-0.png',
                        'error' => UPLOAD_ERR_OK,
                    ],
                ]
            ]
        ]);
    }

    public function getMockedUploadedFileRule()
    {
        $rule = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['isUploadedFile'])
            ->getMock();

        $rule->method('isUploadedFile')->willReturn(true);

        return $rule;
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

    public function testRequiredUnlessRule()
    {
        $v1 = $this->validator->validate([
            'a' => '',
            'b' => '',
        ], [
            'b' => 'required_unless:a,1'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_unless:a,1'
        ]);

        $this->assertTrue($v2->passes());
    }

    public function testRequiredWithRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_with:a'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_with:a'
        ]);

        $this->assertFalse($v2->passes());
    }

    public function testRequiredWithoutRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_without:a'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_without:a'
        ]);

        $this->assertTrue($v2->passes());
    }

    public function testRequiredWithAllRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
            'a' => '1'
        ], [
            'b' => 'required_with_all:a,c'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
            'c' => '2'
        ], [
            'b' => 'required_with_all:a,c'
        ]);

        $this->assertFalse($v2->passes());
    }

    public function testRequiredWithoutAllRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
            'a' => '1'
        ], [
            'b' => 'required_without_all:a,c'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_without_all:a,c'
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
        ], [
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
        ], []);

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
        ], []);

        $validator2->validate();

        $this->assertFalse($validator2->passes());
    }

    public function testNewValidationRuleCanBeAdded()
    {

        $this->validator->addValidator('even', new Even());

        $data = [4, 6, 8, 10 ];

        $validation = $this->validator->make($data, ['s' => 'even'], []);

        $validation->validate();

        $this->assertTrue($validation->passes());
    }

    /**
     * @expectedException \Rakit\Validation\RuleQuashException
     */
    public function testInternalValidationRuleCannotBeOverridden()
    {

        $this->validator->addValidator('required', new Required());

        $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

        $validation = $this->validator->make($data, ['s' => 'required'], []);

        $validation->validate();
    }

    public function testInternalValidationRuleCanBeOverridden()
    {
        $this->validator->allowRuleOverride(true);

        //This is a custom rule defined in the fixtures directory
        $this->validator->addValidator('required', new Required());

        $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

        $validation = $this->validator->make($data, ['s' => 'required'], []);

        $validation->validate();

        $this->assertTrue($validation->passes());
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

        $this->assertNotNull($errors->first('required_field:required'));
        $this->assertNull($errors->first('required_field:numeric'));
        $this->assertNull($errors->first('required_field:min'));

        $this->assertNotNull($errors->first('required_if_field:required_if'));
        $this->assertNull($errors->first('required_if_field:numeric'));
        $this->assertNull($errors->first('required_if_field:min'));

        $this->assertNotNull($errors->first('must_present_field:present'));
        $this->assertNull($errors->first('must_present_field:numeric'));
        $this->assertNull($errors->first('must_present_field:min'));

        $this->assertNotNull($errors->first('must_accepted_field:accepted'));
        $this->assertNull($errors->first('must_accepted_field:numeric'));
        $this->assertNull($errors->first('must_accepted_field:min'));
    }

    public function testNextRulesAppliedWhenEmptyValueWithPresent()
    {
        $validation = $this->validator->validate([
            'must_present_field' => '',
        ], [
            'must_present_field' => 'present|array',
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->count(), 1);

        $this->assertNull($errors->first('must_present_field:present'));
        $this->assertNotNull($errors->first('must_present_field:array'));
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
        $this->assertNotNull($errors->first('optional_field:ipv4'));
        $this->assertNotNull($errors->first('optional_field:in'));
        $this->assertNotNull($errors->first('required_if_field:email'));
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
        $this->assertEquals(implode(',', $errors->all()), '1,2,3,4,5,6,7,8,9,10');
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

        $this->assertNotNull($errors->first('user.email:email'));
        $this->assertNotNull($errors->first('user.age:min'));
        $this->assertNull($errors->first('user.name:required'));
    }

    public function testEmptyArrayAssocValidation()
    {
        $validation = $this->validator->validate([], [
            'user'=> 'required',
            'user.email' => 'email',
        ]);

        $this->assertFalse($validation->passes());
    }

    /**
     * Test root asterisk validation.
     *
     * @dataProvider rootAsteriskProvider
     */
    public function testRootAsteriskValidation(array $data, array $rules, $errors = null)
    {
        $validation = $this->validator->validate($data, $rules);
        $this->assertSame(empty($errors), $validation->passes());
        $errorBag = $validation->errors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $field = $error[0];
                $rule = $error[1] ?? null;
                $error = $errorBag->get($field);
                $this->assertNotEmpty($error);
                if ($rule !== null) {
                    $this->assertArrayHasKey($rule, $error);
                }
            }
        }
    }

    public function rootAsteriskProvider(): array
    {
        return [
            'control sample success' => [
                ['Body' => ['a' => 1, 'b' => 2]],
                ['Body.*' => 'integer|min:0'],
            ],
            'control sample failure' => [
                ['Body' => ['a' => 1, 'b' => -2]],
                ['Body.*' => 'integer|min:0'],
                [['Body.b', 'min']],
            ],
            'root field success' => [
                ['a' => 1, 'b' => 2],
                ['*' => 'integer|min:0'],
            ],
            'root field failure' => [
                ['a' => 1, 'b' => -2],
                ['*' => 'integer|min:0'],
                [['b', 'min']],
            ],
            'root array success' => [
                [[1], [2]],
                ['*.*' => 'integer|min:0'],
            ],
            'root array failure' => [
                [[1], [-2]],
                ['*.*' => 'integer|min:0'],
                [['1.0', 'min']],
            ],
            'root dict success' => [
                ['a' => ['c' => 1, 'd' => 4], 'b' => ['c' => 'e', 'd' => 8]],
                ['*.c' => 'required'],
            ],
            'root dict failure' => [
                ['a' => ['c' => 1, 'd' => 4], 'b' => ['d' => 8]],
                ['*.c' => 'required'],
                [['b.c', 'required']],
            ],
        ];
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

        $this->assertNotNull($errors->first('cart_items.1.id_product:required'));
        $this->assertNotNull($errors->first('cart_items.2.qty:required'));
        $this->assertNotNull($errors->first('cart_items.3.qty:numeric'));
        $this->assertNotNull($errors->first('cart_items.4.id_product:numeric'));
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
        $this->assertEquals($errors->first('foo:required'), 'foo');
        $this->assertEquals($errors->first('email:email'), 'bar');
        $this->assertEquals($errors->first('something:numeric'), 'baz');
        $this->assertEquals($errors->first('comments.0.text:required'), 'baz');
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
        $this->assertEquals($errors->first('foo:required'), 'foo');
        $this->assertEquals($errors->first('email:email'), 'bar');
        $this->assertEquals($errors->first('something:numeric'), 'baz');
        $this->assertEquals($errors->first('comments.0.text:required'), 'baz');
    }

    public function testCustomMessageInCallbackRule()
    {
        $evenNumberValidator = function ($value) {
            if (!is_numeric($value) or $value % 2 !== 0) {
                return ":attribute must be even number";
            }
            return true;
        };

        $validation = $this->validator->make([
            'foo' => 'abc',
        ], [
            'foo' => [$evenNumberValidator],
        ]);

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals($errors->first('foo:callback'), "Foo must be even number");
    }

    public function testSpecificRuleMessage()
    {
        $validation = $this->validator->make([
            'something' => 'value',
        ], [
            'something' => 'email|max:3|numeric',
        ]);

        $validation->setMessages([
            'something:email' => 'foo',
            'something:numeric' => 'bar',
            'something:max' => 'baz',
        ]);

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals($errors->first('something:email'), 'foo');
        $this->assertEquals($errors->first('something:numeric'), 'bar');
        $this->assertEquals($errors->first('something:max'), 'baz');
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
        $this->assertEquals($errors->first('foo:required'), 'Foo foo');
        $this->assertEquals($errors->first('email:email'), 'Bar bar');
        $this->assertEquals($errors->first('something:numeric'), 'Baz baz');
        $this->assertEquals($errors->first('comments.0.text:required'), 'Qux qux');
    }

    public function testUsingDefaults()
    {
        $validation = $this->validator->validate([
            'is_active' => null,
            'is_published' => 'invalid-value'
        ], [
            'is_active' => 'defaults:0|required|in:0,1',
            'is_enabled' => 'defaults:1|required|in:0,1',
            'is_published' => 'required|in:0,1'
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNull($errors->first('is_active'));
        $this->assertNull($errors->first('is_enabled'));
        $this->assertNotNull($errors->first('is_published'));

        // Getting (all) validated data
        $validatedData = $validation->getValidatedData();
        $this->assertEquals($validatedData, [
            'is_active' => '0',
            'is_enabled' => '1',
            'is_published' => 'invalid-value'
        ]);

        // Getting only valid data
        $validData = $validation->getValidData();
        $this->assertEquals($validData, [
            'is_active' => '0',
            'is_enabled' => '1'
        ]);

        // Getting only invalid data
        $invalidData = $validation->getInvalidData();
        $this->assertEquals($invalidData, [
            'is_published' => 'invalid-value',
        ]);
    }

    public function testHumanizedKeyInArrayValidation()
    {
        $validation = $this->validator->validate([
            'cart' => [
                [
                    'qty' => 'xyz',
                ],
            ]
        ], [
            'cart.*.itemName' => 'required',
            'cart.*.qty' => 'required|numeric'
        ]);

        $errors = $validation->errors();

        $this->assertEquals($errors->first('cart.*.qty'), 'The Cart 1 qty must be numeric');
        $this->assertEquals($errors->first('cart.*.itemName'), 'The Cart 1 item name is required');
    }

    public function testCustomMessageInArrayValidation()
    {
        $validation = $this->validator->make([
            'cart' => [
                [
                    'qty' => 'xyz',
                    'itemName' => 'Lorem ipsum'
                ],
                [
                    'qty' => 10,
                    'attributes' => [
                        [
                            'name' => 'color',
                            'value' => null
                        ]
                    ]
                ],
            ]
        ], [
            'cart.*.itemName' => 'required',
            'cart.*.qty' => 'required|numeric',
            'cart.*.attributes.*.value' => 'required'
        ]);

        $validation->setMessages([
            'cart.*.itemName:required' => 'Item [0] name is required',
            'cart.*.qty:numeric' => 'Item {0} qty is not a number',
            'cart.*.attributes.*.value' => 'Item {0} attribute {1} value is required',
        ]);

        $validation->validate();

        $errors = $validation->errors();

        $this->assertEquals($errors->first('cart.*.qty'), 'Item 1 qty is not a number');
        $this->assertEquals($errors->first('cart.*.itemName'), 'Item 1 name is required');
        $this->assertEquals($errors->first('cart.*.attributes.*.value'), 'Item 2 attribute 1 value is required');
    }

    public function testRequiredIfOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'products' => [
                // invalid because has_notes is not empty
                '10' => [
                    'quantity' => 8,
                    'has_notes' => 1,
                    'notes' => ''
                ],
                // valid because has_notes is null
                '12' => [
                    'quantity' => 0,
                    'has_notes' => null,
                    'notes' => ''
                ],
                // valid because no has_notes
                '14' => [
                    'quantity' => 0,
                    'notes' => ''
                ],
            ]
        ], [
            'products.*.notes' => 'required_if:products.*.has_notes,1',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNotNull($errors->first('products.10.notes'));
        $this->assertNull($errors->first('products.12.notes'));
        $this->assertNull($errors->first('products.14.notes'));
    }

    public function testRequiredUnlessOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'products' => [
                // valid because has_notes is 1
                '10' => [
                    'quantity' => 8,
                    'has_notes' => 1,
                    'notes' => ''
                ],
                // invalid because has_notes is not 1
                '12' => [
                    'quantity' => 0,
                    'has_notes' => null,
                    'notes' => ''
                ],
                // invalid because no has_notes
                '14' => [
                    'quantity' => 0,
                    'notes' => ''
                ],
            ]
        ], [
            'products.*.notes' => 'required_unless:products.*.has_notes,1',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNull($errors->first('products.10.notes'));
        $this->assertNotNull($errors->first('products.12.notes'));
        $this->assertNotNull($errors->first('products.14.notes'));
    }

    public function testSameRuleOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'users' => [
                [
                    'password' => 'foo',
                    'password_confirmation' => 'foo'
                ],
                [
                    'password' => 'foo',
                    'password_confirmation' => 'bar'
                ],
            ]
        ], [
            'users.*.password_confirmation' => 'required|same:users.*.password',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNull($errors->first('users.0.password_confirmation:same'));
        $this->assertNotNull($errors->first('users.1.password_confirmation:same'));
    }

    public function testGetValidData()
    {
        $validation = $this->validator->validate([
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                'foo@bar.com',
                'something',
                'foo@blah.com'
            ],
            'stuffs' => [
                'one' => '1',
                'two' => '2',
                'three' => 'three',
            ],
            'thing' => 'exists',
        ], [
            'thing' => 'required',
            'items.*.product_id' => 'required|numeric',
            'emails.*' => 'required|email',
            'items.*.qty' => 'required|numeric',
            'something' => 'default:on|required|in:on,off',
            'stuffs' => 'required|array',
            'stuffs.one' => 'required|numeric',
            'stuffs.two' => 'required|numeric',
            'stuffs.three' => 'required|numeric',
        ]);

        $validData = $validation->getValidData();

        $this->assertEquals([
            'items' => [
                [
                    'product_id' => 1
                ]
            ],
            'emails' => [
                0 => 'foo@bar.com',
                2 => 'foo@blah.com'
            ],
            'thing' => 'exists',
            'something' => 'on',
            'stuffs' => [
                'one' => '1',
                'two' => '2',
            ]
        ], $validData);

        $stuffs = $validData['stuffs'];
        $this->assertFalse(isset($stuffs['three']));
    }

    public function testGetInvalidData()
    {
        $validation = $this->validator->validate([
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                'foo@bar.com',
                'something',
                'foo@blah.com'
            ],
            'stuffs' => [
                'one' => '1',
                'two' => '2',
                'three' => 'three',
            ],
            'thing' => 'exists',
        ], [
            'thing' => 'required',
            'items.*.product_id' => 'required|numeric',
            'emails.*' => 'required|email',
            'items.*.qty' => 'required|numeric',
            'something' => 'required|in:on,off',
            'stuffs' => 'required|array',
            'stuffs.one' => 'numeric',
            'stuffs.two' => 'numeric',
            'stuffs.three' => 'numeric',
        ]);

        $invalidData = $validation->getInvalidData();

        $this->assertEquals([
            'items' => [
                [
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                1 => 'something'
            ],
            'something' => null,
            'stuffs' => [
                'three' => 'three',
            ]
        ], $invalidData);

        $stuffs = $invalidData['stuffs'];
        $this->assertFalse(isset($stuffs['one']));
        $this->assertFalse(isset($stuffs['two']));
    }

    public function testRuleInInvalidMessages()
    {
        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'in:7,8,9',
        ]);

        $this->assertEquals($validation->errors()->first('number'), "The Number only allows '7', '8', or '9'");

        // Using translation
        $this->validator->setTranslation('or', 'atau');

        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'in:7,8,9',
        ]);

        $this->assertEquals($validation->errors()->first('number'), "The Number only allows '7', '8', atau '9'");
    }

    public function testRuleNotInInvalidMessages()
    {
        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'not_in:1,2,3',
        ]);

        $this->assertEquals($validation->errors()->first('number'), "The Number is not allowing '1', '2', and '3'");

        // Using translation
        $this->validator->setTranslation('and', 'dan');

        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'not_in:1,2,3',
        ]);

        $this->assertEquals($validation->errors()->first('number'), "The Number is not allowing '1', '2', dan '3'");
    }

    public function testRuleMimesInvalidMessages()
    {
        $file = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1000,
            'error' => UPLOAD_ERR_OK,
        ];

        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => 'mimes:jpeg,png,bmp',
        ]);

        $expectedMessage = "The Sample file type must be 'jpeg', 'png', or 'bmp'";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);

        // Using translation
        $this->validator->setTranslation('or', 'atau');

        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => 'mimes:jpeg,png,bmp',
        ]);

        $expectedMessage = "The Sample file type must be 'jpeg', 'png', atau 'bmp'";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);
    }

    public function testRuleUploadedFileInvalidMessages()
    {
        $file = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1024 * 1024 * 2, // 2M
            'error' => UPLOAD_ERR_OK,
        ];

        $rule = $this->getMockedUploadedFileRule();

        // Invalid uploaded file (!is_uploaded_file($file['tmp_name']))
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => 'uploaded_file',
        ]);

        $expectedMessage = "The Sample is not valid uploaded file";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);

        // Invalid min size
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->minSize('3M')],
        ]);

        $expectedMessage = "The Sample file is too small, minimum size is 3M";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);

        // Invalid max size
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->maxSize('1M')],
        ]);

        $expectedMessage = "The Sample file is too large, maximum size is 1M";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);

        // Invalid file types
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->fileTypes(['jpeg', 'png', 'bmp'])],
        ]);

        $expectedMessage = "The Sample file type must be 'jpeg', 'png', or 'bmp'";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);

        // Invalid file types with translation
        $this->validator->setTranslation('or', 'atau');
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->fileTypes(['jpeg', 'png', 'bmp'])],
        ]);

        $expectedMessage = "The Sample file type must be 'jpeg', 'png', atau 'bmp'";
        $this->assertEquals($validation->errors()->first('sample'), $expectedMessage);
    }

    public function testIgnoreNextRulesWithNullableRule()
    {
        $emptyFile = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $invalidFile = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1000,
            'error' => UPLOAD_ERR_OK,
        ];

        $data1 = [
            'file' => $emptyFile,
            'name' => ''
        ];

        $data2 = [
            'file' => $invalidFile,
            'name' => 'a@b.c'
        ];

        $rules = [
            'file' => 'nullable|uploaded_file:0,500K,png,jpeg',
            'name' => 'nullable|email'
        ];

        $validation1 = $this->validator->validate($data1, $rules);
        $validation2 = $this->validator->validate($data2, $rules);

        $this->assertTrue($validation1->passes());
        $this->assertFalse($validation2->passes());
    }

    public function testNumericStringSizeWithoutNumericRule()
    {
        $validation = $this->validator->validate([
            'number' => '1.2345'
        ], [
            'number' => 'max:2',
        ]);

        $this->assertFalse($validation->passes());
    }

    public function testNumericStringSizeWithNumericRule()
    {
        $validation = $this->validator->validate([
            'number' => '1.2345'
        ], [
            'number' => 'numeric|max:2',
        ]);

        $this->assertTrue($validation->passes());
    }
}
