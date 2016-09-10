<?php

namespace LibxxTest\Middleware;

use Libxx\Middleware\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $middleware = new Middleware(function ($req, $resp) {
            return $resp;
        });
        $this->assertSame($response, $middleware->handle($request, $response));
    }
}
