<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Numeric;
use PHPUnit\Framework\TestCase;

class NumericTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Numeric;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('123'));
        $this->assertTrue($this->rule->check('123.456'));
        $this->assertTrue($this->rule->check('-123.456'));
        $this->assertTrue($this->rule->check(123));
        $this->assertTrue($this->rule->check(123.456));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo123'));
        $this->assertFalse($this->rule->check('123foo'));
        $this->assertFalse($this->rule->check([123]));
    }
}
