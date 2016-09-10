<?php

namespace Libxx\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionHandlerInterface
{

    /**
     * Handle the exception.
     *
     * @param \Exception $e
     * @param ServerRequestInterface $req
     * @param ResponseInterface $resp
     * @return ResponseInterface
     */
    public function handle(\Exception $e, ServerRequestInterface $req, ResponseInterface $resp);
}
