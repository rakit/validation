<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\UploadedFile;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{

    public function setUp()
    {
        $this->rule = new UploadedFile();
    }

    public function testValidUploadedFile()
    {
        $file = [
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK
        ];

        $uploadedFileRule = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['isUploadedFile'])
            ->getMock();

        $uploadedFileRule->expects($this->once())
            ->method('isUploadedFile')
            ->willReturn(true);

        $this->assertTrue($uploadedFileRule->check($file));
    }

    /**
     * Make sure we can't just passing array like valid $_FILES['key']
     */
    public function testValidateWithoutMockShouldBeInvalid()
    {
        $this->assertFalse($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK
        ]));
    }

    /**
     * Missing UPLOAD_ERR_NO_FILE should be valid because it is job for required rule
     */
    public function testEmptyUploadedFileShouldBeValid()
    {
        $this->assertTrue($this->rule->check([
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ]));
    }

    public function testUploadError()
    {
        $this->assertFalse($this->rule->check([
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => 5
        ]));
    }

    public function testMaxSize()
    {
        $rule = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['isUploadedFile'])
            ->getMock();

        $rule->expects($this->exactly(2))
            ->method('isUploadedFile')
            ->willReturn(true);

        $rule->maxSize("1MB");

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024 * 1024 * 1.1,
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1000000,
            'tmp_name' => __FILE__,
            'error' => 0
        ]));
    }

    public function testMinSize()
    {
        $rule = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['isUploadedFile'])
            ->getMock();

        $rule->expects($this->exactly(2))
            ->method('isUploadedFile')
            ->willReturn(true);

        $rule->minSize('10K');

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024, // 1K
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 10 * 1024,
            'tmp_name' => __FILE__,
            'error' => 0
        ]));
    }

    public function testFileTypes()
    {
        $rule = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['isUploadedFile'])
            ->getMock();

        $rule->expects($this->exactly(3))
            ->method('isUploadedFile')
            ->willReturn(true);

        $rule->fileTypes('png|jpeg');

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024, // 1K
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'image/png',
            'size' => 10 * 1024,
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'image/jpeg',
            'size' => 10 * 1024,
            'tmp_name' => __FILE__,
            'error' => 0
        ]));
    }

    /**
     * Missing array key(s) should be valid because it is job for required rule
     */
    public function testMissingAKeyShouldBeValid()
    {
        // missing name
        $this->assertTrue($this->rule->check([
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        // missing type
        $this->assertTrue($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        // missing size
        $this->assertTrue($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'tmp_name' => __FILE__,
            'error' => 0
        ]));

        // missing tmp_name
        $this->assertTrue($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'error' => 0
        ]));
    }
}
