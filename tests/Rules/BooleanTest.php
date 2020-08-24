<?php

namespace Rakit\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Rakit\Validation\Rules\Boolean;

class BooleanTest extends TestCase
{
    public function setUp()
    {
        $this->rule = new Boolean;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(\true));
        $this->assertTrue($this->rule->check(\false));
        $this->assertTrue($this->rule->check(1));
        $this->assertTrue($this->rule->check(0));
        $this->assertTrue($this->rule->check('1'));
        $this->assertTrue($this->rule->check('0'));
        $this->assertTrue($this->rule->check('y'));
        $this->assertTrue($this->rule->check('n'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(11));
        $this->assertFalse($this->rule->check([]));
        $this->assertFalse($this->rule->check('foo123'));
        $this->assertFalse($this->rule->check('123foo'));
        $this->assertFalse($this->rule->check([123]));
        $this->assertFalse($this->rule->check('123.456'));
        $this->assertFalse($this->rule->check('-123.456'));
    }
}
