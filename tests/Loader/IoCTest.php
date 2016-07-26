<?php
namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\Loader\IoC;

class IoCTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IoC
     */
    private $loader;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $subDirTest = <<<'php'
<?php
return 15;
php;
        $test = <<<'php'
<?php
return 15;
php;
        $testContext = <<<'php'
<?php
/** @var int $a */
return 15 + $a;
php;
        $testOutput = <<<'php'
<?php
echo 'Magic';
return 15;
php;
        $root = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        mkdir($root);
        file_put_contents($root . 'test.php', $test);
        file_put_contents($root . 'testContext.php', $testContext);
        file_put_contents($root . 'testOutput.php', $testOutput);
        mkdir($root . 'subDir');
        file_put_contents(
            $root . 'subDir' . DIRECTORY_SEPARATOR . 'test.php',
            $subDirTest
        );
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $root = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        unlink($root . 'test.php');
        unlink($root . 'subDir' . DIRECTORY_SEPARATOR . 'test.php');
        unlink($root . 'testContext.php');
        unlink($root . 'testOutput.php');

        rmdir($root . 'subDir');
        rmdir($root);
    }

    public function testhas()
    {
        $this->assertSame(true, $this->loader->has('test'));
        $this->assertSame(false, $this->loader->has('invalidName'));
        $this->assertSame(true, $this->loader->has('subDir_test'));

        // Попытаемся загрузить что то за пределами указанного каталога (не должно грузить)
        $this->assertSame(false, $this->loader->has('../test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testhasNotString()
    {
        $this->loader->has(1);
    }

    public function testGet()
    {
        $this->loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp', ['a' => 5]);
        // Загрузка простого компонента
        $this->assertSame(15, $this->loader->get('test'));
        $this->assertSame(15, $this->loader->get('subDir_test'));

        // проверим работу контекста
        $this->assertSame(20, $this->loader->get('testContext'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container:`invalid` must be loadable
     */
    public function testLoadInvalidContainer()
    {
        $this->loader->get('invalid');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @exceptedExceptionMessage Container name must be string
     */
    public function testLoadNotString()
    {
        $this->loader->get(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLoadWithOutput()
    {
        $this->loader->get('testOutput');
    }

    public function testGetContainerNames()
    {
        $expected = ['test', 'testContext', 'testOutput', 'subDir_test'];
        sort($expected);
        $this->assertSame($expected, $this->loader->getContainerNames());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->loader = new IoC(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');
    }
}
