<?php

use Rakit\Validation\Rules\Defaults;

class DefaultsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new Defaults;
    }

    public function testDefaults()
    {
        $this->assertEquals($this->rule->fillParameters([10])->check(null), 10);
        $this->assertEquals($this->rule->fillParameters(['something'])->check(null), 'something');
        $this->assertEquals($this->rule->fillParameters([[1,2,3]])->check('anything'), [1,2,3]);
    }

}
