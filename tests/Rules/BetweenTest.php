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
        $this->assertTrue($this->rule->setParameters([6, 10])->check('foobar'));
        $this->assertTrue($this->rule->setParameters([2, 3])->check([1,2,3]));
        $this->assertTrue($this->rule->setParameters([100, 150])->check(123));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->setParameters([2, 5])->check('foobar'));
        $this->assertFalse($this->rule->setParameters([4, 6])->check([1,2,3]));
        $this->assertFalse($this->rule->setParameters([50, 100])->check(123));
    }

}
