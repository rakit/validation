<?php

use Rakit\Validation\Rules\Url;

class UrlTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Url;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('ftp://foobar.com', []));
        $this->assertTrue($this->rule->check('http://foobar.com', []));
        $this->assertTrue($this->rule->check('https://foobar.com', []));
        $this->assertTrue($this->rule->check('https://foobar.com/path?a=123&b=blah', []));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo:', []));
    }

}
