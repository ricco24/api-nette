<?php

namespace Kelemen\ApiNette\Route;

class ResolvedRoute
{
    /** @var Route */
    private $route;

    /** @var array */
    private $params = [];

    /**
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : false;
    }
}
