<?php
namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\LoaderInterface;

trait LoaderInterfaceTestTrait
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var array
     */
    protected static $exampleConfiguration = [
        "int" => 15,
        "string" => "some string",
        "float" => 0.5,
        "null" => null,
        "arrayWithKeyInt" => ["test0", "test1"],
        "arrayWithKeyString" => [
            "key1" => "value1",
            "key2" => 15
        ]
    ];

    /**
     * @dataProvider providerData
     * @param $key
     * @param $value
     */
    public function testLoad($key, $value)
    {
        assertSame($value, $this->loader->load($key));
    }

    public function providerData()
    {
        $result = [];
        foreach (self::$exampleConfiguration as $key => $value) {
            $call = [];
            $call[] = $key;
            $call[] = $value;
            $result[] = $call;
        }
        return $result;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`test`, must be loadable
     */
    public function testInvalidConfiguration()
    {
        $this->loader->load('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Container:`not declared Container`, must be loadable
     */
    public function testLoadNotDeclared()
    {
        $this->loader->load('not declared Container');
    }

    public function testGetLoadableContainerNames()
    {
        assertSame(array_keys(self::$exampleConfiguration), $this->loader->getLoadableContainerNames());
    }
}