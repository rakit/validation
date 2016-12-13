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
        $this->assertTrue($this->rule->setParameters([200])->check(123));
        $this->assertTrue($this->rule->setParameters([6])->check('foobar'));
        $this->assertTrue($this->rule->setParameters([3])->check([1,2,3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->setParameters([5])->check('foobar'));
        $this->assertFalse($this->rule->setParameters([2])->check([1,2,3]));
        $this->assertFalse($this->rule->setParameters([100])->check(123));
    }

}
