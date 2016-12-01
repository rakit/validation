<?php

use Rakit\Validation\Rules\Regex;

class RegexTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Regex;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check("foo", ["/^F/i"]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check("bar", ["/^F/i"]));
    }

}
