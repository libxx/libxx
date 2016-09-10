<?php

namespace LibxxTest\Middleware;

use Libxx\Kernel\CallableResolverInterface;
use Libxx\Middleware\Pipeline;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class PipelineTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $callableResolver = $this->createMock(CallableResolverInterface::class);
        $callableResolver->method('resolve')->willReturnArgument(0);
        $pipeline = new Pipeline($callableResolver);

        $header = 'X-Count';

        $pipe = function (ServerRequestInterface $request, ResponseInterface $response, $next) use ($header) {
            $response = $next->handle($request, $response);
            $num = intval($response->getHeader($header));
            return $response->withHeader($header, strval($num += 1));
        };

        $destination = function () use ($header) {
            return (new Response())->withHeader($header, '1');
        };

        $pipeline->pipe($pipe);
        $pipeline->then($destination);

        $req = new ServerRequest();
        $resp = new Response();

        $response = $pipeline($req, $resp);

        $this->assertEquals(2, intval($response->getHeader($header)[0]));
    }
}
