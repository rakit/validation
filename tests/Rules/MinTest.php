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
        $this->assertTrue($this->rule->setParameters([100])->check(123));
        $this->assertTrue($this->rule->setParameters([6])->check('foobar'));
        $this->assertTrue($this->rule->setParameters([3])->check([1,2,3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->setParameters([7])->check('foobar'));
        $this->assertFalse($this->rule->setParameters([4])->check([1,2,3]));
        $this->assertFalse($this->rule->setParameters([200])->check(123));
    }

}
