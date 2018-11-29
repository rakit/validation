<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\In;
use PHPUnit\Framework\TestCase;

class InTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new In;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([1,2,3])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', 'bar', '3'])->check('bar'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([1,2,3])->check(4));
    }

    public function testStricts()
    {
        // Not strict
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(true));

        // Strict
        $this->rule->strict();
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
    }
}
