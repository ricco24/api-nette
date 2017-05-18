<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Route\Route;
use Kelemen\ApiNette\Route\RouteContainer;
use PHPUnit_Framework_TestCase;

class RouteContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic usage (add and get by key)
     */
    public function testBasics()
    {
        $route1 = new Route('post', 'user1/<id>', '#handler1');
        $route2 = new Route('post', 'user2/<id>', '#handler2');
        $route3 = new Route('get', 'user3/<id>', '#handler3');
        $route4 = new Route('put', 'user4/<id>', '#handler4');
        $route5 = new Route('options', 'user5/<id>', '#handler5');
        $route6 = new Route('delete', 'user6/<id>', '#handler6');
        $route7 = new Route('options', 'user7/<id>', '#handler7');

        $routeContainer = new RouteContainer();
        $routeContainer->add($route1);
        $routeContainer->add($route2);
        $routeContainer->add($route3);
        $routeContainer->add($route4);
        $routeContainer->add($route5);
        $routeContainer->add($route6);
        $routeContainer->add($route7);

        $this->assertEquals([$route1, $route2, $route3, $route4, $route5, $route7, $route6], $routeContainer->getRoutes());
        $this->assertEquals([$route1, $route2], $routeContainer->getRoutes('post'));
        $this->assertEquals([$route3], $routeContainer->getRoutes('get'));
        $this->assertEquals([$route4], $routeContainer->getRoutes('put'));
        $this->assertEquals([$route5, $route7], $routeContainer->getRoutes('options'));
        $this->assertEquals([$route6], $routeContainer->getRoutes('delete'));
    }
}