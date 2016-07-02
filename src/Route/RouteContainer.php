<?php

namespace Kelemen\ApiNette\Route;

class RouteContainer
{
    /** @var array */
    private $routes = [];

    /**
     * @param Route $route
     */
    public function add(Route $route)
    {
        $this->routes[$route->getMethod()][] = $route;
    }

    /**
     * @param string|null $method
     * @return array
     */
    public function getRoutes($method = null)
    {
        if ($method !== null) {
            $method = strtolower($method);
            return isset($this->routes[$method]) ? $this->routes[$method] : [];
        }

        $routes = [];
        foreach ($this->routes as $routesByMethod) {
            $routes = array_merge($routes, $routesByMethod);
        }
        return $routes;
    }
}
