<?php

namespace Libxx\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Middleware
{

    /**
     * The middleware callable.
     *
     * @var callable
     */
    private $middleware;

    /**
     * CallableMiddleware constructor.
     *
     * @param callable $middleware
     */
    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Handle the request and response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response)
    {
        return call_user_func($this->middleware, $request, $response);
    }
}
