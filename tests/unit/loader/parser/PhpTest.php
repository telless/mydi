<?php
namespace smpl\mydi\tests\unit\loader\parser;

use smpl\mydi\loader\parser\Php;
use smpl\mydi\tests\unit\unit\loader\parser\JsonTest;

class PhpTest extends JsonTest {
    /**
     * @var Php
     */
    protected $parser = 'smpl\mydi\loader\parser\Php';
    protected $file = 'php.php';

    protected function setUp()
    {
        parent::setUp();
        $this->filePathInvalidFormat = $this->getResource('phpInvalid.php');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseWithOutput()
    {
        $this->parser->parse($this->getResource('phpWithOutput.php'));
    }
}