<?php

namespace LibxxTest\Routing;

use Libxx\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{

    public function testMethod()
    {
        $route = new Route('GET', '/', null);
        $this->assertEquals(['GET'], $route->getMethods());

        $route = new Route(['GET', 'POST'], '/', null);
        $this->assertCount(2, $route->getMethods());
        $this->assertContains('GET', $route->getMethods());
        $this->assertContains('POST', $route->getMethods());

        $this->assertNotContains('FOO', $route->getMethods());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMethodType()
    {
        new Route(1, '/', null);
    }

    public function testPath()
    {
        $methods = ['GET'];
        $path = '/foo/bar';
        $route = new Route($methods, $path, null);
        $this->assertEquals($path, $route->getPath());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPathType()
    {
        new Route('GET', [], null);
    }

    public function testContext()
    {
        $context = ['foo' => 'bar'];
        $route = new Route('GET', '/foo', $context);
        $this->assertEquals($context, $route->getContext());
    }

    public function testName()
    {
        $route = new Route('GET', '/foo', null);
        $this->assertNull($route->getName());

        $name = 'bar';
        $route = new Route('GET', '/foo', null, $name);
        $this->assertEquals($name, $route->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidNameType()
    {
        new Route('GET', '/foo', null, []);
    }
}
