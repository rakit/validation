<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Url;
    }

    public function testValids()
    {
        // Without specific schemes
        $this->assertTrue($this->rule->check('ftp://foobar.com'));
        $this->assertTrue($this->rule->check('any://foobar.com'));
        $this->assertTrue($this->rule->check('http://foobar.com'));
        $this->assertTrue($this->rule->check('https://foobar.com'));
        $this->assertTrue($this->rule->check('https://foobar.com/path?a=123&b=blah'));

        // Using specific schemes
        $this->assertTrue($this->rule->fillParameters(['ftp'])->check('ftp://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['any'])->check('any://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['http'])->check('http://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['https'])->check('https://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['http', 'https'])->check('https://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['foo', 'bar'])->check('bar://foobar.com'));
        $this->assertTrue($this->rule->fillParameters(['mailto'])->check('mailto:johndoe@gmail.com'));
        $this->assertTrue($this->rule->fillParameters(['jdbc'])->check('jdbc:mysql://localhost/dbname'));

        // Using forScheme
        $this->assertTrue($this->rule->forScheme('ftp')->check('ftp://foobar.com'));
        $this->assertTrue($this->rule->forScheme('http')->check('http://foobar.com'));
        $this->assertTrue($this->rule->forScheme('https')->check('https://foobar.com'));
        $this->assertTrue($this->rule->forScheme(['http', 'https'])->check('https://foobar.com'));
        $this->assertTrue($this->rule->forScheme('mailto')->check('mailto:johndoe@gmail.com'));
        $this->assertTrue($this->rule->forScheme('jdbc')->check('jdbc:mysql://localhost/dbname'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo:'));
        $this->assertFalse($this->rule->check('mailto:johndoe@gmail.com'));
        $this->assertFalse($this->rule->forScheme('mailto')->check('http://www.foobar.com'));
        $this->assertFalse($this->rule->forScheme('ftp')->check('http://www.foobar.com'));
        $this->assertFalse($this->rule->forScheme('jdbc')->check('http://www.foobar.com'));
        $this->assertFalse($this->rule->forScheme(['http', 'https'])->check('any://www.foobar.com'));
    }
}
