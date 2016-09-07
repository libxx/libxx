<?php

namespace Libxx\Routing;

interface RouteInterface
{

    /**
     * Get the route context.
     *
     * @return mixed
     */
    public function getContext();

    /**
     * Get the methods supported by this route.
     *
     * @return array
     */
    public function getMethods();

    /**
     * Get the path pattern for this route.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get the route name.
     *
     * @return string|null
     */
    public function getName();
}
