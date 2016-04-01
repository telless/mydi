<?php
namespace SmplTest\Mydi;

use Smpl\Mydi\Locator;
use Smpl\Mydi\LocatorInterface;

abstract class AbstractLocator extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

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

    protected function setUp()
    {
        parent::setUp();
        $this->locator = new Locator();
    }
}