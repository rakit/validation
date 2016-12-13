<?php

use Rakit\Validation\Rules\Accepted;

class AcceptedTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Accepted;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('yes'));
        $this->assertTrue($this->rule->check('on'));
        $this->assertTrue($this->rule->check('1'));
        $this->assertTrue($this->rule->check(1));
        $this->assertTrue($this->rule->check(true));
        $this->assertTrue($this->rule->check('true'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(''));
        $this->assertFalse($this->rule->check('onn'));
        $this->assertFalse($this->rule->check(' 1'));
        $this->assertFalse($this->rule->check(10));
    }

}
