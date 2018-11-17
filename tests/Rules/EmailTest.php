<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Email;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('johndoe@gmail.com'));
        $this->assertTrue($this->rule->check('johndoe@foo.bar'));
        $this->assertTrue($this->rule->check('foo123123@foo.bar.baz'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(1));
        $this->assertFalse($this->rule->check('john doe@gmail.com'));
        $this->assertFalse($this->rule->check('johndoe'));
        $this->assertFalse($this->rule->check('johndoe.gmail.com'));
        $this->assertFalse($this->rule->check('johndoe.gmail.com'));
    }
}
