<?php

use Rakit\Validation\Validation;

class ValidatonTest extends PHPUnit_Framework_TestCase
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

        $validationMock = $this->getMockBuilder(Validation::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['parseRule'])
            ->getMock();

        $result = $method->invokeArgs($validationMock, [$rules]);
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
