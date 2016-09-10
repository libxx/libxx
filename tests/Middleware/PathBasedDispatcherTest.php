<?php

namespace LibxxTest\Middleware;

use Libxx\Middleware\PathBasedDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Uri;

class PathBasedDispatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testPath()
    {
        $dispatcher = new PathBasedDispatcher();
        $dispatcher->add('/foo', 'foo');
        $this->assertEquals(['foo'], $dispatcher->getMiddleware($this->createRequestForPath('/foo')));
        $this->assertEmpty($dispatcher->getMiddleware($this->createRequestForPath('/bar')));

        $dispatcher->add('/', 'bar');
        $this->assertEquals(['bar', 'foo'], $dispatcher->getMiddleware($this->createRequestForPath('/foo')));
        $this->assertEquals(['bar'], $dispatcher->getMiddleware($this->createRequestForPath('/bar')));
    }

    public function testPriority()
    {
        $dispatcher = new PathBasedDispatcher();
        $dispatcher->add('/foo', 'foo');
        $dispatcher->add('/foo', 'bar');
        $dispatcher->add('/foo', 'baz', -1);

        $this->assertEquals(['baz', 'foo', 'bar'], $dispatcher->getMiddleware($this->createRequestForPath('/foo')));
    }

    private function createRequestForPath($path)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(Uri::class);
        $uri->method('getPath')->willReturn($path);
        $request->method('getUri')->willReturn($uri);
        return $request;
    }
}
