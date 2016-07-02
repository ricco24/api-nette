<?php

namespace Kelemen\ApiNette\Route;

use Nette\Http\Request;

class BaseRouteResolver implements RouteResolverInterface
{
    /** @var Request */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Resolve route for url
     * @param RouteContainer $routes
     * @param string $url
     * @return bool|ResolvedRoute
     */
    public function resolve(RouteContainer $routes, $url)
    {
        foreach ($routes->getRoutes($this->request->getMethod()) as $route) {
            $params = $this->resolveParams($route, $url);
            if (is_array($params)) {
                return new ResolvedRoute($route, $params);
            }
        }

        return false;
    }

    /**
     * Resolve parameters for route
     * @param Route $route
     * @param string $url
     * @return array|bool
     */
    private function resolveParams(Route $route, $url)
    {
        preg_match_all('#' . $route->getPregPattern() . '#', $url, $values, PREG_SET_ORDER);

        // If nothing found
        if (count($values) === 0) {
            return false;
        }

        // Remove full match from matches (leave only part matches)
        $values = $values[0];
        array_shift($values);

        $result = [];
        foreach ($route->getParams() as $key => $param) {
            // If parameter key is not set in matched values, route not match and return false
            if (!isset($values[$key])) {
                return false;
            }
            $result[$param] = $values[$key];
        }

        return $result;
    }
}
