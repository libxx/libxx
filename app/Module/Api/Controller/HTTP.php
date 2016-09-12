<?php

namespace App\Module\Api\Controller;

use Libxx\Middleware\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class HTTP extends Controller
{

    /**
     * Handle the ping request.
     *
     * @param ServerRequestInterface $req
     * @param ResponseInterface $resp
     * @return ResponseInterface
     */
    public function ping(ServerRequestInterface $req, ResponseInterface $resp)
    {
        return new JsonResponse([
            'timestamp' => microtime(true)
        ]);
    }
}
