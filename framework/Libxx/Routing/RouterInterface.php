<?php

namespace Libxx\Routing;

interface RouterInterface
{

    /**
     * Add a route to the router.
     *
     * @param RouteInterface $route
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function add(RouteInterface $route);

    /**
     * Retrieve a route by the given name.
     *
     * @param string $name
     * @return RouteInterface|null
     */
    public function getRouteByName($name);

    /**
     * Retrieve all routes.
     *
     * @return RouteInterface[]
     */
    public function getRoutes();

    /**
     * Match the request.
     *
     * @param string $method
     * @param string $uri
     * @return MatchResult
     */
    public function match($method, $uri);

    /**
     * Create an URL by the given route name and parameters.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function createURL($name, array $parameters = []);
}
