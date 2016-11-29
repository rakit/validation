<?php

use Rakit\Validation\Rules\Max;

class MaxTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Max;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(123, [200]));
        $this->assertTrue($this->rule->check('foobar', [6]));
        $this->assertTrue($this->rule->check([1,2,3], [3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foobar', [5]));
        $this->assertFalse($this->rule->check([1,2,3], [2]));
        $this->assertFalse($this->rule->check(123, [100]));
    }

}
