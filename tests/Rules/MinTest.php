<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Min;
use PHPUnit\Framework\TestCase;

class MinTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Min;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([100])->check(123));
        $this->assertTrue($this->rule->fillParameters([6])->check('foobar'));
        $this->assertTrue($this->rule->fillParameters([3])->check([1,2,3]));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([7])->check('foobar'));
        $this->assertFalse($this->rule->fillParameters([4])->check([1,2,3]));
        $this->assertFalse($this->rule->fillParameters([200])->check(123));

        $this->assertFalse($this->rule->fillParameters([4])->check('мин'));
        $this->assertFalse($this->rule->fillParameters([5])->check('كلمة'));
        $this->assertFalse($this->rule->fillParameters([4])->check('ワード'));
        $this->assertFalse($this->rule->fillParameters([2])->check('字'));
    }

    public function testUploadedFileValue()
    {
        $twoMega = 1024 * 1024 * 2;
        $sampleFile = [
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => $twoMega,
            'tmp_name' => __FILE__,
            'error' => 0
        ];

        $this->assertTrue($this->rule->fillParameters([$twoMega])->check($sampleFile));
        $this->assertTrue($this->rule->fillParameters(['2M'])->check($sampleFile));

        $this->assertFalse($this->rule->fillParameters([$twoMega + 1])->check($sampleFile));
        $this->assertFalse($this->rule->fillParameters(['2.1M'])->check($sampleFile));
    }
}
