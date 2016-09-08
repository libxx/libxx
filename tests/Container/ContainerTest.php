<?php

namespace LibxxTest\Container;

use Libxx\Container\Container;
use Libxx\Container\ContainerAwareTrait;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetOrHasService()
    {
        $container = new Container();
        $name = 'foo';
        $foo = new \stdClass();
        $container[$name] = $foo;
        $this->assertSame($foo, $container->get($name));

        $this->assertTrue($container->has('foo'));
        $this->assertFalse($container->has('bar'));
    }

    /**
     * @expectedException \Libxx\Container\NotFoundException
     */
    public function testGetUndefinedService()
    {
        $container = new Container();
        $container->get("foo");
    }

    public function testContainerAwareTrait()
    {
        $aware = $this->getMockForTrait(ContainerAwareTrait::class);
        $container = new Container();
        $aware->setContainer($container);
        $this->assertAttributeSame($container, 'container', $aware);
    }
}
