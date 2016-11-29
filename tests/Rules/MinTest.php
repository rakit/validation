<?php

use Rakit\Validation\Rules\Min;

class MinTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Min;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(123, [100]));
        $this->assertTrue($this->rule->check('foobar', [6]));
        $this->assertTrue($this->rule->check([1,2,3], [3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foobar', [7]));
        $this->assertFalse($this->rule->check([1,2,3], [4]));
        $this->assertFalse($this->rule->check(123, [200]));
    }

}
