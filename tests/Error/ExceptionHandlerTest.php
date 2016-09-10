<?php

namespace LibxxTest\Error;

use Libxx\Error\ExceptionHandler;
use Libxx\Kernel\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{

    public function testHandleException()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $exception = $this->createMock(\Exception::class);
        $httpException = $this->createMock(HttpException::class);
        $httpException->method('getHeaders')->willReturn([]);
        $httpException->method('getStatusCode')->willReturn(404);

        $exceptionHandler = new ExceptionHandler(true);
        $exceptionResponse = $exceptionHandler->handle($exception, $request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $exceptionResponse);
        $this->assertEquals(500, $exceptionResponse->getStatusCode());

        $httpExceptionResponse = $exceptionHandler->handle($httpException, $request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $httpExceptionResponse);
        $this->assertEquals($httpException->getStatusCode(), $httpExceptionResponse->getStatusCode());
    }
}
