<?php

return [

    'debug' => false,
    'config_cache_enabled' => true,

    'factories' => [
        \Psr\Http\Message\ServerRequestInterface::class => \App\Factory\RequestFactory::class,
        \Libxx\Error\ExceptionHandlerInterface::class => \App\Factory\ExceptionHandlerFactory::class,
    ],
    'objects' => [
        \Libxx\Kernel\App::class,
        \Psr\Http\Message\ResponseInterface::class => \Zend\Diactoros\Response::class,
        \Libxx\Error\ErrorHandlerInterface::class => \Libxx\Error\ErrorHandler::class,
        \Zend\Diactoros\Response\EmitterInterface::class => \Zend\Diactoros\Response\SapiEmitter::class,
        \Libxx\Middleware\DispatcherInterface::class => \Libxx\Middleware\PathBasedDispatcher::class,
        \Libxx\Kernel\CallableResolverInterface::class => \Libxx\Kernel\CallableResolver::class,
        \Libxx\Routing\RouterInterface::class => \Libxx\Routing\Router::class,
        \Libxx\Error\HandleExceptions::class,
        \Libxx\Routing\RouteRegister::class,
    ],

    'bootstraps' => [
        \Libxx\Error\HandleExceptions::class,
        \Libxx\Routing\RouteRegister::class,
    ],

];