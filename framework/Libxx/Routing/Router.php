<?php

namespace Libxx\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteParser;
use FastRoute\DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;

class Router implements RouterInterface
{

    /**
     * The routes to be injected.
     *
     * @var RouteInterface[]
     */
    private $routesToInject = [];

    /**
     * The routes already injected.
     *
     * @var RouteInterface[]
     */
    private $routes = [];

    /**
     * The route parser.
     *
     * @var RouteParser
     */
    private $routeParser;

    /**
     * The route data generator.
     *
     * @var DataGenerator
     */
    private $dataGenerator;

    /**
     * The route collector.
     *
     * @var RouteCollector
     */
    private $routeCollector;

    /**
     * The route dispatcher factory.
     *
     * @var callable
     */
    private $dispatcherFactory;

    /**
     * Router constructor.
     *
     * @param callable $dispatcherFactory
     */
    public function __construct(callable $dispatcherFactory = null)
    {
        $this->routeParser = new Std();
        $this->dataGenerator = new GroupCountBasedDataGenerator();
        $this->routeCollector = new RouteCollector($this->routeParser, $this->dataGenerator);
        $this->dispatcherFactory = $dispatcherFactory;
    }

    /**
     * @inheritDoc
     */
    public function add(RouteInterface $route)
    {
        $this->routesToInject[] = $route;
    }

    /**
     * @inheritDoc
     */
    public function getRouteByName($name)
    {
        $this->injectRoutes();
        if (isset($this->routes[$name])) {
            return $this->routes[$name];
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRoutes()
    {
        $this->injectRoutes();
        return $this->routes;
    }

    /**
     * @inheritDoc
     */
    public function match($method, $uri)
    {
        $this->injectRoutes();
        $dispatcher = $this->createDispatcher($this->routeCollector->getData());
        $ret = $dispatcher->dispatch($method, $uri);

        $success = false;
        $allowedMethods = [];
        $params = [];
        $route = null;

        switch ($ret[0]) {
            case Dispatcher::NOT_FOUND:
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $ret[1];
                break;
            case Dispatcher::FOUND:
                $success = true;
                $route = $ret[1];
                $params = $ret[2];
                break;
        }

        return new MatchResult($success, $allowedMethods, $params, $route);
    }

    /**
     * Injects all routes.
     */
    private function injectRoutes()
    {
        foreach ($this->routesToInject as $index => $route) {
            $this->injectRoute($route);
            unset($this->routesToInject[$index]);
        }
    }

    /**
     * Inject routes to the route collector.
     *
     * @param RouteInterface $route
     */
    private function injectRoute(RouteInterface $route)
    {
        $methods = $route->getMethods();
        $path = $route->getPath();
        $handler = $route;
        $this->routeCollector->addRoute($methods, $path, $handler);
        $name = $route->getName();
        if ($name) {
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }
    }

    /**
     * Create a dispatcher.
     *
     * @param mixed $data
     * @return Dispatcher
     */
    private function createDispatcher($data)
    {
        if ($this->dispatcherFactory) {
            $dispatcher = call_user_func($this->dispatcherFactory, $data);
        } else {
            $dispatcher = new GroupCountBasedDispatcher($data);
        }
        return $dispatcher;
    }
}
