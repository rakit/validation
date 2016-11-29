<?php

use Rakit\Validation\Rules\UploadedFile;

class UploadedFileTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rule = new UploadedFile;
    }

    public function testValidUploadedFile()
    {
        $this->assertTrue($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__
        ], []));
    }

    public function testMaxSize()
    {
        $rule = new UploadedFile;
        $rule->maxSize('1M');

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024*1024*1.1,
            'tmp_name' => __FILE__
        ], []));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1000000,
            'tmp_name' => __FILE__
        ], []));
    }

    public function testMinSize()
    {
        $rule = new UploadedFile;
        $rule->minSize('10K');

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024, // 1K
            'tmp_name' => __FILE__
        ], []));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 10*1024,
            'tmp_name' => __FILE__
        ], []));
    }


    public function testFileTypes()
    {
        $rule = new UploadedFile;
        $rule->fileTypes('png|jpeg');

        $this->assertFalse($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => 1024, // 1K
            'tmp_name' => __FILE__
        ], []));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'image/png',
            'size' => 10*1024,
            'tmp_name' => __FILE__
        ], []));

        $this->assertTrue($rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'image/jpeg',
            'size' => 10*1024,
            'tmp_name' => __FILE__
        ], []));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check([
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__
        ], []));

        $this->assertFalse($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'size' => filesize(__FILE__),
            'tmp_name' => __FILE__
        ], []));

        $this->assertFalse($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'tmp_name' => __FILE__
        ], []));

        $this->assertFalse($this->rule->check([
            'name' => pathinfo(__FILE__, PATHINFO_BASENAME),
            'type' => 'text/plain',
            'size' => filesize(__FILE__),
        ], []));
    }

}
