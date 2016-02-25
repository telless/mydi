<?php
namespace smpl\mydi\tests\unit\loader\executor;

use smpl\mydi\container\Lazy;
use smpl\mydi\loader\executor\LazyFactory;
use smpl\mydi\LocatorInterface;

class LazyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyFactory
     */
    protected $executor;

    public function testExecute()
    {
        $locator = $this->getMock(LocatorInterface::class);
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('stdClass', []);
        $this->assertInstanceOf(Lazy::class, $result);
        $this->assertInstanceOf(\Closure::class, $result->resolve($locator));
        $value1 = $result->resolve($locator);
        $value2 = $result->resolve($locator);
        $this->assertNotSame($value1(), $value2());
    }

    public function testExecuteWithString()
    {
        $locator = $this->getMock(LocatorInterface::class);
        $locator->expects($this->once())
            ->method('resolve')
            ->with('stdClass')
            ->will($this->returnValue(1234));
        /** @var LocatorInterface $locator */

        $result = $this->executor->execute('ololo', \stdClass::class);
        $this->assertInstanceOf(Lazy::class, $result);
        $result = $result->resolve($locator);
        $this->assertSame(1234, $result());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Config must be string or array
     */
    public function testExecuteInvalidConfig()
    {
        $this->executor->execute('test', null);
    }

    public function testExecuteWithConstructs()
    {
        $locator = $this->getMock(LocatorInterface::class);
        $locator->expects($this->once())
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue('2000-01-01'));
        /** @var LocatorInterface $locator */
        $result = $this->executor->execute('\DateTime', ['construct' => ['test']]);
        /** @var \DateTime $obj */
        $obj = $result->resolve($locator);
        if (!$obj instanceof \Closure) {
            $this->assertSame('2000-01-01', $obj->format('Y-m-d'));
        } else {
            /** @var \Closure $obj */
            $this->assertSame('2000-01-01', $obj()->format('Y-m-d'));
        }

    }

    protected function setUp()
    {
        parent::setUp();
        $this->executor = new LazyFactory();
    }
}
