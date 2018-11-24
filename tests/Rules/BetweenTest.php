<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new Between;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([6, 10])->check('foobar'));
        $this->assertTrue($this->rule->fillParameters([6, 10])->check('футбол'));
        $this->assertTrue($this->rule->fillParameters([2, 3])->check([1,2,3]));
        $this->assertTrue($this->rule->fillParameters([100, 150])->check(123));
        $this->assertTrue($this->rule->fillParameters([100, 150])->check(123.4));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([2, 5])->check('foobar'));
        $this->assertFalse($this->rule->fillParameters([2, 5])->check('футбол'));
        $this->assertFalse($this->rule->fillParameters([4, 6])->check([1,2,3]));
        $this->assertFalse($this->rule->fillParameters([50, 100])->check(123));
        $this->assertFalse($this->rule->fillParameters([50, 100])->check(123.4));
    }

    public function testUploadedFileValue()
    {
        $mb = function ($n) {
            return $n * 1024 * 1024;
        };

        $sampleFile = [
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => $mb(2),
            'tmp_name' => __FILE__,
            'error' => 0
        ];

        $this->assertTrue($this->rule->fillParameters([$mb(2), $mb(5)])->check($sampleFile));
        $this->assertTrue($this->rule->fillParameters(['2M', '5M'])->check($sampleFile));
        $this->assertTrue($this->rule->fillParameters([$mb(1), $mb(2)])->check($sampleFile));
        $this->assertTrue($this->rule->fillParameters(['1M', '2M'])->check($sampleFile));

        $this->assertFalse($this->rule->fillParameters([$mb(2.1), $mb(5)])->check($sampleFile));
        $this->assertFalse($this->rule->fillParameters(['2.1M', '5M'])->check($sampleFile));
        $this->assertFalse($this->rule->fillParameters([$mb(1), $mb(1.9)])->check($sampleFile));
        $this->assertFalse($this->rule->fillParameters(['1M', '1.9M'])->check($sampleFile));
    }
}
