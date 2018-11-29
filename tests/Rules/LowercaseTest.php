<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Lowercase;
use PHPUnit\Framework\TestCase;

class LowercaseTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Lowercase;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('username'));
        $this->assertTrue($this->rule->check('full name'));
        $this->assertTrue($this->rule->check('full_name'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('USERNAME'));
        $this->assertFalse($this->rule->check('Username'));
        $this->assertFalse($this->rule->check('userName'));
    }
}
