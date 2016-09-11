<?php

use Interop\Container\ContainerInterface;
use Libxx\Kernel\App;

return [

    'factories' => [
        App::class => function (ContainerInterface $container) {
            return new App($container);
        },
    ],
    'objects' => [
        \Psr\Http\Message\ServerRequestInterface::class => \Zend\Diactoros\ServerRequestFactory::fromGlobals(),
        \Psr\Http\Message\ResponseInterface::class => new \Zend\Diactoros\Response(),
        \Libxx\Error\ExceptionHandlerInterface::class => new \Libxx\Error\ExceptionHandler(true),
        \Libxx\Error\ErrorHandlerInterface::class => new \Libxx\Error\ErrorHandler(),
        \Zend\Diactoros\Response\EmitterInterface::class => new \Zend\Diactoros\Response\SapiEmitter(),
        \Libxx\Middleware\DispatcherInterface::class => new \Libxx\Middleware\PathBasedDispatcher(),
        \Libxx\Kernel\CallableResolverInterface::class => new \Libxx\Kernel\CallableResolver(),
        \Libxx\Routing\RouterInterface::class => new \Libxx\Routing\Router(),
        \Libxx\Error\HandleExceptions::class
    ],

    'bootstraps' => [
        \Libxx\Error\HandleExceptions::class,
    ],

];