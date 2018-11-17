<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Json;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('{}'));
        $this->assertTrue($this->rule->check('[]'));
        $this->assertTrue($this->rule->check('false'));
        $this->assertTrue($this->rule->check('null'));
        $this->assertTrue($this->rule->check('{"username": "John Doe"}'));
        $this->assertTrue($this->rule->check('{"number": 12345678}'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(''));
        $this->assertFalse($this->rule->check(123));
        $this->assertFalse($this->rule->check(false));
        $this->assertFalse($this->rule->check('{"username": John Doe}'));
        $this->assertFalse($this->rule->check('{number: 12345678}'));
    }
}
