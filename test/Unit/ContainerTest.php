<?php

namespace Smpl\Mydi\Test\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Container;
use Smpl\Mydi\LoaderInterface;
use Smpl\Mydi\ProviderInterface;

class ContainerTest extends TestCase
{
    public function testHasFromLoader()
    {
        $mockLoader = $this->getMockBuilder(ProviderInterface::class)->getMock();
        $mockLoader
            ->expects($this->once())
            ->method('hasProvide')
            ->with($this->equalTo('magic'))
            ->willReturn(true);
        $locator = new Container($mockLoader);
        $this->assertTrue($locator->has('magic'));
    }

    /**
     * @expectedException \Psr\Container\ContainerExceptionInterface
     * @expectedExceptionMessage Container name must be string
     */
    public function testGetNotString()
    {
        $locator = new Container();
        $locator->get(123);
    }

    /**
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     * @expectedExceptionMessage Container: `test`, is not defined
     */
    public function testGetNameNotExist()
    {
        $locator = new Container();
        $locator->get('test');
    }

    public function testSetLoader()
    {
        $result = 123;

        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader->expects($this->any())
            ->method('load')
            ->will($this->returnValue($result));
        $provider = $this->getMockBuilder(ProviderInterface::class)->getMock();
        $provider->method('hasProvide')->willReturn(true);
        $provider->method('provide')->willReturn($loader);
        $locator = new Container($provider);
        $this->assertSame($result, $locator->get('test'));
    }

    /**
     * @expectedException \Psr\Container\ContainerExceptionInterface
     * @expectedExceptionMessage Infinite recursion in the configuration, name called again: a, call stack: a, b.
     */
    public function testNotCorrectConfiguration()
    {
        $loaderA = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loaderA->method('load')->willReturnCallback(function (ContainerInterface $locator) {
            $obj = new \stdClass();
            $obj->test = $locator->get('b');
            return $obj;
        });

        $loaderB = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loaderB->method('load')->willReturnCallback(function (ContainerInterface $locator) {
            $obj = new \stdClass();
            $obj->test = $locator->get('a');
            return $obj;
        });
        $provider = $this->getMockBuilder(ProviderInterface::class)->getMock();
        $provider->method('hasProvide')->willReturn(true);
        $provider->method('provide')->willReturnCallback(function ($name) use ($loaderA, $loaderB) {
            $result = $loaderA;
            if ($name === 'b') {
                $result = $loaderB;
            }
            return $result;
        });
        $locator = new Container($provider);
        $locator->get('a');
    }

    /**
     * @expectedException \TypeError
     */
    public function testSetLoadersInvalid()
    {
        new Container([1]);
    }

    /**
     * @dataProvider providerValidParams
     * @param string $name
     * @param mixed $value
     */
    public function testGetUseLoader($name, $value)
    {

        $provider = $this->getMockBuilder(ProviderInterface::class)->getMock();
        $provider->expects($this->once())
            ->method('hasProvide')
            ->with($this->equalTo($name))
            ->will($this->returnValue(true));
        $provider->expects($this->once())
            ->method('provide')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value));
        /** @var LoaderInterface $provider */
        $locator = new Container($provider);
        $this->assertSame($value, $locator->get($name));
    }

    public function providerValidParams()
    {
        return [
            ['int', 1],
            ['float', 0.5],
            ['bool', true],
            ['string', 'test'],
            ['object', new \stdClass()],
            ['null', null]
        ];
    }
}
