<?php

namespace Libxx\Middleware;

use Libxx\Kernel\CallableResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    /**
     * The pipeline callables.
     *
     * @var mixed[]
     */
    private $pipes = [];
    /**
     * The pipeline destination.
     *
     * @var mixed
     */
    private $destination;
    /**
     * The callable resolver.
     *
     * @var CallableResolverInterface
     */
    private $callableResolver;

    /**
     * Pipeline constructor.
     *
     * @param CallableResolverInterface $callableResolver
     */
    public function __construct(CallableResolverInterface $callableResolver)
    {
        $this->callableResolver = $callableResolver;
    }

    /**
     * @inheritDoc
     */
    public function pipe($middleware)
    {
        $this->pipes[] = $middleware;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function then($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $resp)
    {
        $next = function () {
            return call_user_func_array($this->callableResolver->resolve($this->destination), func_get_args());
        };
        foreach (array_reverse($this->pipes) as $pipe) {
            $next = function () use ($next, $pipe) {
                $payloads = func_get_args();
                $payloads[] = new Middleware($next);
                return call_user_func_array($this->callableResolver->resolve($pipe), $payloads);
            };
        }
        return $next($req, $resp);
    }
}
