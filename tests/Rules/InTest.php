<?php

use Rakit\Validation\Rules\In;

class InTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new In;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(1, [1,2,3]));
        $this->assertTrue($this->rule->check('bar', ['1', 'bar', '3']));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(4, [1,2,3]));
    }

}
