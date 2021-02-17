<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Stringy;
use PHPUnit\Framework\TestCase;
use stdClass;

class StringyTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Stringy;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('foo'));
        $this->assertTrue($this->rule->check('123asd'));
        $this->assertTrue($this->rule->check('asd123'));
        $this->assertTrue($this->rule->check('foo123bar'));
        $this->assertTrue($this->rule->check('foo bar'));
        $this->assertTrue($this->rule->check('<p><a href="#">Lorem ipsum dolor sit amet</a> cum omnis voluptatum! </p>'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(2));
        $this->assertFalse($this->rule->check([]));
        $this->assertFalse($this->rule->check(new stdClass));
    }
}
