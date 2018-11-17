<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Integer;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
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
