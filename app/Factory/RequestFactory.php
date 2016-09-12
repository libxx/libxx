<?php

namespace App\Factory;

use Interop\Container\ContainerInterface;
use Zend\Diactoros\ServerRequestFactory;

class RequestFactory
{

    /**
     * Factory the request instance.
     *
     * @param ContainerInterface $container
     * @return \Zend\Diactoros\ServerRequest
     */
    public function __invoke(ContainerInterface $container)
    {
        return ServerRequestFactory::fromGlobals();
    }
}
