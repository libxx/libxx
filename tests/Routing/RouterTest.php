<?php

namespace LibxxTest\Routing;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdRouteParser;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use Libxx\Routing\Route;
use Libxx\Routing\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyConstructor()
    {
        $router = new Router();
        $this->assertAttributeInstanceOf(StdRouteParser::class, 'routeParser', $router);
        $this->assertAttributeInstanceOf(GroupCountBasedDataGenerator::class, 'dataGenerator', $router);
        $this->assertAttributeEquals(null, 'dispatcherFactory', $router);
    }

    public function testConstructor()
    {
        $routeParser = $this->createMock(RouteParser::class);
        $dataGenerator = $this->createMock(DataGenerator::class);
        $dispatcherFactory = function () {
        };
        $router = new Router($routeParser, $dataGenerator, $dispatcherFactory);
        $this->assertAttributeSame($routeParser, 'routeParser', $router);
        $this->assertAttributeSame($dataGenerator, 'dataGenerator', $router);
        $this->assertAttributeSame($dispatcherFactory, 'dispatcherFactory', $router);
    }

    public function testAddRoute()
    {
        $router = new Router();
        $route1 = new Route('GET', '/foo', null);
        $route2 = new Route('GET', '/foo', null);
        $router->add($route1);
        $this->assertContains($route1, $router->getRoutes());
        $this->assertNotContains($route2, $router->getRoutes());
    }

    public function testGetRoutes()
    {
        $router = new Router();
        $route1 = new Route('GET', '/foo', null);
        $route2 = new Route('GET', '/foo1', null);
        $router->add($route1);
        $router->add($route2);
        $this->assertContains($route1, $router->getRoutes());
        $this->assertContains($route2, $router->getRoutes());
        $this->assertCount(2, $router->getRoutes());
    }

    public function testGetRouteByName()
    {
        $name = 'bar';
        $router = new Router();
        $route1 = new Route('GET', '/foo', null, $name);
        $router->add($route1);
        $this->assertSame($route1, $router->getRouteByName($name));
        $this->assertNull($router->getRouteByName('baz'));
    }

    public function testMatch()
    {
        $router = new Router();
        $router->add(new Route('GET', '/foo/{id:\d+}', null));
        $res = $router->match('GET', '/foo/123');
        $this->assertTrue($res->isSuccess());
        $this->assertArraySubset(['id' => '123'], $res->getMatchedParameters());

        $res = $router->match('POST', '/foo/123');
        $this->assertTrue($res->isMethodFailure());

        $res = $router->match('GET', '/foo');
        $this->assertFalse($res->isSuccess());
        $this->assertFalse($res->isMethodFailure());
    }

    public function testDispatcherFactory()
    {
        $methods = ['GET', 'POST'];
        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->method('dispatch')->willReturn([Dispatcher::METHOD_NOT_ALLOWED, $methods]);

        $dispatcherFactory = function () use ($dispatcher) {
            return $dispatcher;
        };

        $router = new Router(null, null, $dispatcherFactory);
        $res = $router->match('foo', 'bar');
        $this->assertFalse($res->isSuccess());
        $this->assertTrue($res->isMethodFailure());
        $this->assertEquals($methods, $res->getAllowedMethods());
    }

    public function testCreateURL()
    {
        $router = new Router();
        $router->add(new Route('GET', '/foo', null, 'foo'));
        $this->assertEquals('/foo', $router->createURL('foo'));
        $this->assertEquals('/foo?bar=baz', $router->createURL('foo', ['bar' => 'baz']));

        $router->add(new Route('GET', '/bar/{id}', null, 'bar'));
        $this->assertEquals('/bar/123', $router->createURL('bar', ['id' => '123']));

        $router->add(new Route('GET', '/baz[/{id}]', null, 'baz'));
        $this->assertEquals('/baz/123', $router->createURL('baz', ['id' => '123']));
        $this->assertEquals('/baz', $router->createURL('baz'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateURLForUndefinedName()
    {
        $router = new Router();
        $router->createURL('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateURLForMissingSegment()
    {
        $router = new Router();
        $router->add(new Route('GET', '/foo/{id}', null, 'foo'));
        $router->createURL('foo');
    }
}
