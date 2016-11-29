<?php

use Rakit\Validation\Rules\NotIn;

class NotInTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new NotIn;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('1', ['2', '3', '4']));
        $this->assertTrue($this->rule->check(5, [1, 2, 3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('bar', ['bar', 'baz', 'qux']));
    }

}
