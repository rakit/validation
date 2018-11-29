<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Ipv6;
use PHPUnit\Framework\TestCase;

class Ipv6Test extends TestCase
{

    public function setUp()
    {
        $this->rule = new Ipv6;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'));
        $this->assertTrue($this->rule->check('ff02::2'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('hf02::2'));
        $this->assertFalse($this->rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'));
    }
}
