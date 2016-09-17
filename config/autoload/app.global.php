<?php

return [

    'debug' => false,
    'config_cache_enabled' => true,
    'base_path' => realpath(__DIR__ . '/../../'),

    'factories' => [
        \Psr\Http\Message\ServerRequestInterface::class => \App\Factory\RequestFactory::class,
        \Libxx\Error\ExceptionHandlerInterface::class => \App\Factory\ExceptionHandlerFactory::class,
        \Symfony\Component\Console\Application::class => \Libxx\Console\AppFactory::class,
    ],
    'objects' => [
        \Libxx\Kernel\App::class,
        \Psr\Http\Message\ResponseInterface::class => \Zend\Diactoros\Response::class,
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