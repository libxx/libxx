<?php

namespace Libxx\Routing;

class Route implements RouteInterface
{

    /**
     * The supported methods.
     *
     * @var array
     */
    private $methods;

    /**
     * The route path pattern.
     *
     * @var string
     */
    private $path;

    /**
     * The route context.
     *
     * @var mixed
     */
    private $context;

    /**
     * The route name.
     *
     * @var string
     */
    private $name;

    /**
     * The route middleware.
     *
     * @var array
     */
    private $middleware;

    /**
     * Route constructor.
     *
     * @param array|string $methods
     * @param string $path
     * @param mixed $context
     * @param array $middleware
     * @param string|null $name
     */
    public function __construct($methods, $path, $context, array $middleware = [], $name = null)
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }
        if (!is_array($methods)) {
            throw new \InvalidArgumentException("The route methods should be string or array type.");
        }
        if (!is_string($path)) {
            throw new \InvalidArgumentException("The route path should be string type.");
        }
        if (!is_null($name) && !is_string($name)) {
            throw new \InvalidArgumentException("The route name should be string type.");
        }
        $this->methods = $methods;
        $this->path = $path;
        $this->context = $context;
        $this->middleware = $middleware;
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}
