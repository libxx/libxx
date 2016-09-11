<?php

namespace LibxxTest\Error;

use Libxx\Container\Container;
use Libxx\Error\ErrorHandler;
use Libxx\Error\ExceptionHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{

    public function testError()
    {
        $container = new Container();

        $exceptionHandler = $this->createMock(ExceptionHandlerInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $exceptionHandler->method('handle')->willReturn($response);

        $container[ExceptionHandlerInterface::class] = $exceptionHandler;

        $errorHandler = new ErrorHandler();
        $errorHandler->setContainer($container);

        $error = $this->createMock(\Error::class);
        $this->assertSame($response, $errorHandler->handle($error, $request, $response));
    }
}
