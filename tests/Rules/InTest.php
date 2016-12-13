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
        $this->assertTrue($this->rule->setParameters([1,2,3])->check(1));
        $this->assertTrue($this->rule->setParameters(['1', 'bar', '3'])->check('bar'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->setParameters([1,2,3])->check(4));
    }

}
