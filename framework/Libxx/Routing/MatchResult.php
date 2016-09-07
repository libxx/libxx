<?php

namespace Libxx\Routing;

class MatchResult
{

    /**
     * Matched flag.
     *
     * @var bool
     */
    private $success;

    /**
     * Allowed methods.
     *
     * @var array
     */
    private $allowedMethods = [];

    /**
     * Matched parameters.
     *
     * @var array
     */
    private $matchedParams = [];

    /**
     * Matched route.
     *
     * @var RouteInterface|null
     */
    private $matchedRoute;

    /**
     * MatchResult constructor.
     *
     * @param bool $success
     * @param array $allowedMethods
     * @param array $params
     * @param RouteInterface|null $matchedRoute
     */
    public function __construct($success, array $allowedMethods, array $params, RouteInterface $matchedRoute = null)
    {
        $this->success = $success;
        $this->allowedMethods = $allowedMethods;
        $this->matchedParams = $params;
        $this->matchedRoute = $matchedRoute;
    }

    /**
     * Check if the match result is success.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Check if the match result is failure for HTTP method not allowed.
     *
     * @return bool
     */
    public function isMethodFailure()
    {
        return !$this->success && !empty($this->allowedMethods);
    }

    /**
     * Get the allowed methods for this match.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    /**
     * Get the matched parameters.
     *
     * @return array
     */
    public function getMatchedParameters()
    {
        return $this->matchedParams;
    }

    /**
     * Get the matched route.
     *
     * @return RouteInterface|null
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }
}
