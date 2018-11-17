<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Max;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Max;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([200])->check(123));
        $this->assertTrue($this->rule->fillParameters([6])->check('foobar'));
        $this->assertTrue($this->rule->fillParameters([3])->check([1,2,3]));

        $this->assertTrue($this->rule->fillParameters([3])->check('мин'));
        $this->assertTrue($this->rule->fillParameters([4])->check('كلمة'));
        $this->assertTrue($this->rule->fillParameters([3])->check('ワード'));
        $this->assertTrue($this->rule->fillParameters([1])->check('字'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([5])->check('foobar'));
        $this->assertFalse($this->rule->fillParameters([2])->check([1,2,3]));
        $this->assertFalse($this->rule->fillParameters([100])->check(123));
    }
}
