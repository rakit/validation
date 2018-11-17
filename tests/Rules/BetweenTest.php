<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Between;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([6, 10])->check('foobar'));
        $this->assertTrue($this->rule->fillParameters([6, 10])->check('футбол'));
        $this->assertTrue($this->rule->fillParameters([2, 3])->check([1,2,3]));
        $this->assertTrue($this->rule->fillParameters([100, 150])->check(123));
        $this->assertTrue($this->rule->fillParameters([100, 150])->check(123.4));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([2, 5])->check('foobar'));
        $this->assertFalse($this->rule->fillParameters([2, 5])->check('футбол'));
        $this->assertFalse($this->rule->fillParameters([4, 6])->check([1,2,3]));
        $this->assertFalse($this->rule->fillParameters([50, 100])->check(123));
        $this->assertFalse($this->rule->fillParameters([50, 100])->check(123.4));
    }
}
