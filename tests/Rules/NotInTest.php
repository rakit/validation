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
        $this->assertTrue($this->rule->fillParameters(['2', '3', '4'])->check('1'));
        $this->assertTrue($this->rule->fillParameters([1, 2, 3])->check(5));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters(['bar', 'baz', 'qux'])->check('bar'));
    }

    public function testStricts()
    {
        // Not strict
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));        
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(true));

        // Strict        
        $this->rule->strict();
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));        
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));        
    }

}
