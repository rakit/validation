<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ValidationTest extends TestCase
{
    /**
     * @param string $rules
     * @param array $expectedResult
     *
     * @dataProvider parseRuleProvider
     */
    public function testParseRule($rules, $expectedResult)
    {
        $class = new ReflectionClass(Validation::class);
        $method = $class->getMethod('parseRule');
        $method->setAccessible(true);

        $validation = new Validation(new Validator(), [], []);

        $result = $method->invokeArgs($validation, [$rules]);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function parseRuleProvider()
    {
        return [
            [
                'email',
                [
                    'email',
                    [],
                ],
            ],
            [
                'min:6',
                [
                    'min',
                    ['6'],
                ],
            ],
            [
                'uploaded_file:0,500K,png,jpeg',
                [
                    'uploaded_file',
                    ['0', '500K', 'png', 'jpeg'],
                ],
            ],
            [
                'same:password',
                [
                    'same',
                    ['password'],
                ],
            ],
            [
                'regex:/^([a-zA-Z\,]*)$/',
                [
                    'regex',
                    ['/^([a-zA-Z\,]*)$/'],
                ],
            ],
        ];
    }
}
