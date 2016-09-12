<?php

namespace App\Factory;

use Interop\Container\ContainerInterface;
use Libxx\Error\ExceptionHandler;

class ExceptionHandlerFactory
{

    /**
     * Factory the exception handler instance.
     *
     * @param ContainerInterface $container
     * @return ExceptionHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        $debug = $container->get('config')->get('debug', false);
        $exceptionHandler = new ExceptionHandler($debug);
        return $exceptionHandler;
    }
}
