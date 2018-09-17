<?php

use Rakit\Validation\Rules\DigitsBetween;

class DigitsBetweenTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new DigitsBetween;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([2, 6])->check(12345));
        $this->assertTrue($this->rule->fillParameters([2, 3])->check(12));
        $this->assertTrue($this->rule->fillParameters([2, 3])->check(123));
        $this->assertTrue($this->rule->fillParameters([3, 5])->check('12345'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([4, 6])->check(12));
        $this->assertFalse($this->rule->fillParameters([1, 3])->check(12345));
        $this->assertFalse($this->rule->fillParameters([1, 3])->check(12345));
        $this->assertFalse($this->rule->fillParameters([3, 6])->check('foobar'));
    }

}
