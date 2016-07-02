<?php

namespace Kelemen\ApiNette\Route;

interface RouteResolverInterface
{
    /**
     * @param RouteContainer $routes
     * @param string $url
     * @return ResolvedRoute
     */
    public function resolve(RouteContainer $routes, $url);
}
