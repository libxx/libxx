<?php

namespace Libxx\Error;

use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorHandler implements ErrorHandlerInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function handle(\Error $error, ServerRequestInterface $req, ResponseInterface $resp)
    {
        $exception = ErrorUtils::convertErrorToException($error);
        return $this->container->get(ExceptionHandlerInterface::class)->handle($exception, $req, $resp);
    }
}
