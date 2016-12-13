<?php

use Rakit\Validation\Rules\NotIn;

class NotInTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new NotIn;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->setParameters(['2', '3', '4'])->check('1'));
        $this->assertTrue($this->rule->setParameters([1, 2, 3])->check(5));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->setParameters(['bar', 'baz', 'qux'])->check('bar'));
    }

}
