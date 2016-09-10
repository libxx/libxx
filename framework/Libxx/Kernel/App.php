<?php

namespace Libxx\Kernel;

use Interop\Container\ContainerInterface;
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
use Libxx\Middleware\DispatcherInterface as MiddlewareDispatcher;

class App
{

    /**
     * The application service container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Whether the application booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Application constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
        $request = $this->container->get('request');
        $response = $this->container->get('response');

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
            return $callable($req, $resp);
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
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * @return EmitterInterface
     */
    private function getResponseEmitter()
    {
        return $this->container->get('response_emitter');
    }

    /**
     * @return CallableResolverInterface
     */
    private function getCallableResolver()
    {
        return $this->container->get('callable_resolver');
    }

    /**
     * @return MiddlewareDispatcher
     */
    private function getMiddlewareDispatcher()
    {
        return $this->container->get('middleware_dispatcher');
    }

    /**
     * @return ExceptionHandlerInterface
     */
    private function getExceptionHandler()
    {
        return $this->container->get('exception_handler');
    }

    /**
     * @return ErrorHandlerInterface
     */
    private function getErrorHandler()
    {
        return $this->container->get('error_handler');
    }
}
