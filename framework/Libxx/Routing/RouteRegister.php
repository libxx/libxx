<?php

namespace Libxx\Routing;

use Interop\Container\ContainerInterface;
use Libxx\Kernel\BootstrapInterface;

class RouteRegister implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function boot(ContainerInterface $container)
    {
        $routesData = $container->get('config')->get('routes', []);

        $routes = $this->collectRoutes($routesData);

        $router = $container->get(RouterInterface::class);

        foreach ($routes as $route) {
            $router->add($route);
        }
    }

    /**
     * Create routes by config data.
     *
     * @param array $routesData
     * @param string|null $parentName
     * @param string|null $parentPath
     * @param array $parentMiddleware
     * @return Route[]
     */
    private function collectRoutes(array $routesData, $parentName = null, $parentPath = null, array $parentMiddleware = [])
    {
        $routes = [];
        foreach ($routesData as $name => $routeData) {

            if (!isset($routeData['path'])) {
                throw new \InvalidArgumentException('The path for current route is not defined.');
            }
            $path = is_null($parentPath) ? $routeData['path'] : ($parentPath . $routeData['path']);
            $methods = isset($routeData['methods']) ? $routeData['methods'] : ['GET'];
            $middleware = array_merge($parentMiddleware, isset($routeData['middleware']) ? $routeData['middleware'] : []);
            $name = is_null($parentName) ? $name : ($parentName . '.' . $name);

            if (isset($routeData['routes'])) {

                $routes = array_merge($routes, $this->collectRoutes($routeData['routes'], $name, $path, $middleware));

            } else {
                if (!isset($routeData['context'])) {
                    throw new \InvalidArgumentException('The context for current route is not defined.');
                }
                $routes[] = new Route($methods, $path, $routeData['context'], $middleware, $name);
            }
        }
        return $routes;
    }
}