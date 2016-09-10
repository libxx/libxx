<?php

namespace Libxx\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{

    /**
     * Retrieve middleware related to the request.
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getMiddleware(ServerRequestInterface $request);
}
