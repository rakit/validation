<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Ip;
use PHPUnit\Framework\TestCase;

class IpTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Ip;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('1.2.3.4'));
        $this->assertTrue($this->rule->check('255.255.255.255'));
        $this->assertTrue($this->rule->check('ff02::2'));
        $this->assertTrue($this->rule->check('2001:0000:3238:DFE1:0063:0000:0000:FEFB'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('1.2.3.4.5'));
        $this->assertFalse($this->rule->check('256.255.255.255'));
        $this->assertFalse($this->rule->check('hf02::2'));
        $this->assertFalse($this->rule->check('12345:0000:3238:DFE1:0063:0000:0000:FEFB'));
    }
}
