<?php

use Rakit\Validation\Rules\Between;

class BetweenTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Between;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('foobar', [6, 10]));
        $this->assertTrue($this->rule->check([1,2,3], [2, 3]));
        $this->assertTrue($this->rule->check(123, [100, 150]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foobar', [2, 5]));
        $this->assertFalse($this->rule->check([1,2,3], [4, 6]));
        $this->assertFalse($this->rule->check(123, [50, 100]));
    }

}
