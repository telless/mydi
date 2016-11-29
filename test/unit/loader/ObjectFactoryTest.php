<?php
namespace smpl\mydi\tests\unit\loader;

use smpl\mydi\loader\ObjectFactory;
use smpl\mydi\LocatorInterface;
use smpl\mydi\test\example\ClassArgument;
use smpl\mydi\test\example\ClassEmpty;

class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStd()
    {
        $loader = ObjectFactory::factory(ClassEmpty::class);
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        /** @var LocatorInterface $locator */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassEmpty);
        $this->assertNotSame($result, $loader->get($locator));
    }

    public function testGetWithDependency()
    {
        $loader = ObjectFactory::factory(ClassArgument::class, ['example']);
        $locator = $this->getMockBuilder(LocatorInterface::class)->getMock();
        $argumentValue = 123;
        $locator->method('get')
            ->willReturn($argumentValue);
        /** @var LocatorInterface $locator */
        /** @var ClassArgument $result */
        $result = $loader->get($locator);
        $this->assertTrue($result instanceof ClassArgument);
        $this->assertSame($argumentValue, $result->value);
        $this->assertNotSame($result, $loader->get($locator));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage ClassName must be string
     */
    public function testFactoryNotString()
    {
        ObjectFactory::factory(123);
    }
}