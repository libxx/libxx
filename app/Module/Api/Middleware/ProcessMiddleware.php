<?php

namespace App\Module\Api\Middleware;

use Libxx\Middleware\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProcessMiddleware
{

    /**
     * Added processed time to response header.
     *
     * @param ServerRequestInterface $req
     * @param ResponseInterface $resp
     * @param Middleware $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $resp, Middleware $next)
    {
        $resp = $next->handle($req, $resp);
        return $resp->withHeader('Libxx-Processed-In', strval(round((microtime(true) - APP_STARTED_AT) * 1000, 2)));
    }
}
