<?php

namespace Libxx\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorHandlerInterface
{

    /**
     * Handle the php error.
     *
     * @param \Error $error
     * @param ServerRequestInterface $req
     * @param ResponseInterface $resp
     * @return ResponseInterface
     */
    public function handle(\Error $error, ServerRequestInterface $req, ResponseInterface $resp);
}
