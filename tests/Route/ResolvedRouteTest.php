<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Route\ResolvedRoute;
use Kelemen\ApiNette\Route\Route;
use Kelemen\ApiNette\Route\RouteContainer;
use PHPUnit_Framework_TestCase;

class ResolvedRouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic usage
     */
    public function testBasics()
    {
        $params = [
            'id' => 5,
            'messageId' => 10
        ];

        $route = new Route('post', 'user/{id}', 'Kelemen/ApiNette/Handler/UserGetHandler');
        $resolvedRoute = new ResolvedRoute($route, $params);

        $this->assertEquals($route, $resolvedRoute->getRoute());
        $this->assertEquals($params, $resolvedRoute->getParams());
        $this->assertEquals($params['id'], $resolvedRoute->getParam('id'));
        $this->assertEquals($params['messageId'], $resolvedRoute->getParam('messageId'));
        $this->assertFalse($resolvedRoute->getParam('unknown'));
    }
}