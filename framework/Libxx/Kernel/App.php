<?php

namespace Libxx\Kernel;

use Interop\Container\ContainerInterface;
use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;
use Libxx\Error\ErrorHandlerInterface;
use Libxx\Error\ExceptionHandlerInterface;
use Libxx\Kernel\Exception\MethodNotAllowedHttpException;
use Libxx\Kernel\Exception\NotFoundHttpException;
use Libxx\Middleware\Pipeline;
use Libxx\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\Diactoros\Response\EmitterInterface;
use Libxx\Middleware\DispatcherInterface as MiddlewareDispatcherInterface;

class App implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * Whether the application booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Boot the application.
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }
        $bootstraps = $this->container->get('config')->get('bootstraps');
        if ($bootstraps) {
            foreach ($bootstraps as $bootstrap) {
                $this->container->get($bootstrap)->boot($this->container);
            }
        }
        $this->booted = true;
    }

    /**
     * Run the application.
     */
    public function run()
    {
        $this->boot();

        $request = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);

        try {
            $middleware = $this->getMiddlewareDispatcher()->getMiddleware($request);
            $kernel = $this->pipeCallable($middleware, $this);
            $response = $kernel($request, $response);
        } catch (\Error $e) {
            $response = $this->getErrorHandler()->handle($e, $request, $response);
        } catch (\Exception $e) {
            $response = $this->getExceptionHandler()->handle($e, $request, $response);
        }

        $this->getResponseEmitter()->emit($response);
    }

    public function __invoke(ServerRequestInterface $req, ResponseInterface $resp)
    {
        $result = $this->getRouter()->match($req->getMethod(), $req->getUri()->getPath());
        if ($result->isSuccess()) {
            $route = $result->getMatchedRoute();
            $callable = $route->getContext();
            $parameters = $result->getMatchedParameters();
            $parameters['_route'] = $route;
            foreach ($parameters as $key => $value) {
                $req = $req->withAttribute($key, $value);
            }
            $pipeline = $this->pipeCallable($route->getMiddleware(), $callable);
            return $pipeline($req, $resp);
        } else {
            if ($result->isMethodFailure()) {
                throw new MethodNotAllowedHttpException($result->getAllowedMethods(), sprintf('Allowed HTTP method: %s', implode(', ', $result->getAllowedMethods())));
            } else {
                throw new NotFoundHttpException($req->getUri()->getPath(), sprintf('The request uri "%s" is not found.', $req->getUri()->getPath()));
            }
        }
    }

    /**
     * Pipe the middleware.
     *
     * @param array $middleware
     * @param callable $destination
     * @return callable
     */
    private function pipeCallable($middleware, $destination)
    {
        $pipeline = new Pipeline($this->getCallableResolver());
        foreach ($middleware as $callable) {
            $pipeline = $pipeline->pipe($callable);
        }
        return $pipeline->then($destination);
    }

    /**
     * Get the event dispatcher service.
     *
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return $this->container->get(EventDispatcherInterface::class);
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        return $this->container->get(RouterInterface::class);
    }

    /**
     * @return EmitterInterface
     */
    private function getResponseEmitter()
    {
        return $this->container->get(EmitterInterface::class);
    }

    /**
     * @return CallableResolverInterface
     */
    private function getCallableResolver()
    {
        return $this->container->get(CallableResolverInterface::class);
    }

    /**
     * @return MiddlewareDispatcherInterface
     */
    private function getMiddlewareDispatcher()
    {
        return $this->container->get(MiddlewareDispatcherInterface::class);
    }

    /**
     * @return ExceptionHandlerInterface
     */
    private function getExceptionHandler()
    {
        return $this->container->get(ExceptionHandlerInterface::class);
    }

    /**
     * @return ErrorHandlerInterface
     */
    private function getErrorHandler()
    {
        return $this->container->get(ErrorHandlerInterface::class);
    }
}
