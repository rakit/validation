<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Digits;
use PHPUnit\Framework\TestCase;

class DigitsTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Digits;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([4])->check(1243));
        $this->assertTrue($this->rule->fillParameters([6])->check(124567));
        $this->assertTrue($this->rule->fillParameters([3])->check('123'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([7])->check(12345678));
        $this->assertFalse($this->rule->fillParameters([4])->check(12));
        $this->assertFalse($this->rule->fillParameters([3])->check('foo'));
    }
}
