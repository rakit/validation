<?php

use Rakit\Validation\Rules\Integer;

class IntegerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Integer;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(0));
        $this->assertTrue($this->rule->check('0'));
        $this->assertTrue($this->rule->check('123'));
        $this->assertTrue($this->rule->check('-123'));
        $this->assertTrue($this->rule->check(123));
        $this->assertTrue($this->rule->check(-123));

    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo123'));
        $this->assertFalse($this->rule->check('123foo'));
        $this->assertFalse($this->rule->check([123]));
        $this->assertFalse($this->rule->check('123.456'));
        $this->assertFalse($this->rule->check('-123.456'));
    }

}
