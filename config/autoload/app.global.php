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
        \Libxx\Error\HandleExceptions::class,
        \Libxx\Routing\RouteRegister::class,
    ],

    'bootstraps' => [
        \Libxx\Error\HandleExceptions::class,
        \Libxx\Routing\RouteRegister::class,
    ],

    'routes' => [
        'api' => [
            'path' => '/api',
            'middleware' => [
                function($req, $resp, \Libxx\Middleware\Middleware $next) {
                    $resp = $next->handle($req, $resp);
                    return $resp->withAddedHeader('Content-Type', 'application/json');
                },
                function($req, $resp, \Libxx\Middleware\Middleware $next) {
                    $resp = $next->handle($req, $resp);
                    return $resp->withAddedHeader('Libxx-Process-In', strval(round((microtime(true) - APP_STARTED_AT) * 1000, 2)));
                },
            ],
            'routes' => [
                'ping' => [
                    'path' => '/ping',
                    'context' => function() {
                        $resp = new \Zend\Diactoros\Response();
                        $resp->getBody()->write(json_encode([
                            'timestamp' => microtime(true)
                        ]));
                        return $resp;
                    }
                ],
            ]
        ]
    ]

];