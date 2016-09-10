<?php

namespace Libxx\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class PathBasedDispatcher implements DispatcherInterface
{

    /**
     * The middleware storage.
     *
     * @var array
     */
    private $middleware = [];

    /**
     * Add a middleware context with the given path
     *
     * @param string $path
     * @param mixed $middleware
     * @param int $priority
     */
    public function add($path, $middleware, $priority = 0)
    {
        $this->middleware[$path][$priority][] = $middleware;
    }

    /**
     * @inheritDoc
     */
    public function getMiddleware(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        $ret = [];
        ksort($this->middleware);
        foreach ($this->middleware as $path => $middlewareGroup) {
            if (strncmp($path, $uri, strlen($path)) === 0) {
                ksort($middlewareGroup);
                foreach ($middlewareGroup as $middleware) {
                    $ret = array_merge($ret, $middleware);
                }
            }
        }
        return $ret;
    }
}
