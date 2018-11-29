<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Required;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequiredTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Required;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('foo'));
        $this->assertTrue($this->rule->check([1]));
        $this->assertTrue($this->rule->check(1));
        $this->assertTrue($this->rule->check(true));
        $this->assertTrue($this->rule->check('0'));
        $this->assertTrue($this->rule->check(0));
        $this->assertTrue($this->rule->check(new stdClass));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(null));
        $this->assertFalse($this->rule->check(''));
        $this->assertFalse($this->rule->check([]));
    }
}
