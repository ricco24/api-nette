<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Route\BaseRouteResolver;
use Kelemen\ApiNette\Route\Route;
use Kelemen\ApiNette\Route\RouteContainer;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit_Framework_TestCase;

class RouteResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test resolving url to right route
     */
    public function testResolvingMethod1()
    {
        $request = new Request(new UrlScript(), null, null, null, null, null, 'get');
        $routeContainer = $this->prepareRouteContainer();
        $routeResolver = new BaseRouteResolver($request);

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user3/5');
        $this->assertEquals(new Route('get', 'user3/{id}', '#handler3'), $resolvedRoute->getRoute());
        $this->assertEquals(['id' => 5], $resolvedRoute->getParams());
        $this->assertEquals(5, $resolvedRoute->getParam('id'));

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user1/10');
        $this->assertFalse($resolvedRoute);
    }

    /**
     * Test resolving url to right route
     */
    public function testResolvingMethod2()
    {
        $request = new Request(new UrlScript(), null, null, null, null, null, 'options');
        $routeContainer = $this->prepareRouteContainer();
        $routeResolver = new BaseRouteResolver($request);

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user5/10/message/123');
        $this->assertEquals(new Route('options', 'user5/{id}/message/{messageId}', '#handler5'), $resolvedRoute->getRoute());
        $this->assertEquals(['id' => 10, 'messageId' => 123], $resolvedRoute->getParams());
        $this->assertEquals(10, $resolvedRoute->getParam('id'));
        $this->assertEquals(123, $resolvedRoute->getParam('messageId'));

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user5/10/message/unknown/123');
        $this->assertFalse($resolvedRoute);
    }

    /**
     * Test resolving url to right route
     */
    public function testResolvingMethodManyParamsInRow()
    {
        $request = new Request(new UrlScript(), null, null, null, null, null, 'options');
        $routeContainer = $this->prepareRouteContainer();
        $routeResolver = new BaseRouteResolver($request);

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user7/10/20/message/123');
        $this->assertEquals(new Route('options', 'user7/{id}/{subId}/message/{messageId}', '#handler7'), $resolvedRoute->getRoute());
        $this->assertEquals(['id' => 10, 'subId' => 20, 'messageId' => 123], $resolvedRoute->getParams());
        $this->assertEquals(10, $resolvedRoute->getParam('id'));
        $this->assertEquals(20, $resolvedRoute->getParam('subId'));
        $this->assertEquals(123, $resolvedRoute->getParam('messageId'));

        $resolvedRoute = $routeResolver->resolve($routeContainer, 'user7/10/message/unknown/123');
        $this->assertFalse($resolvedRoute);
    }

    /**
     * Test many bad urls, they cant match our route!
     */
    public function testManyBadUrls()
    {
        $request = new Request(new UrlScript(), null, null, null, null, null, 'options');
        $routeContainer = $this->prepareRouteContainer();
        $routeResolver = new BaseRouteResolver($request);

        $badUrls = [
            'user7',
            'user7/10',
            'user7/10/message',
            'user7/10/message/unknown/123',
            'user7/10/message/123/unknown',
            'user7/10/20/30/message/123',
            'user5',
            'user5/20',
            'user5/20/message',
            'user5/20/message/123/unknown',
            'user5/message/123',
            'user5/message/123/unknown'
        ];

        foreach ($badUrls as $badUrl) {
            $resolvedRoute = $routeResolver->resolve($routeContainer, $badUrl);
            $this->assertFalse($resolvedRoute);
        }
    }

    /**
     * Prepare route container for later use
     * @return RouteContainer
     */
    private function prepareRouteContainer()
    {
        $route1 = new Route('post', 'user1/{id}', '#handler1');
        $route2 = new Route('post', 'user2/{id}', '#handler2');
        $route3 = new Route('get', 'user3/{id}', '#handler3');
        $route4 = new Route('put', 'user4/{id}', '#handler4');
        $route5 = new Route('options', 'user5/{id}/message/{messageId}', '#handler5');
        $route6 = new Route('delete', 'user6/{id}', '#handler6');
        $route7 = new Route('options', 'user7/{id}/{subId}/message/{messageId}', '#handler7');

        $routeContainer = new RouteContainer();
        $routeContainer->add($route1);
        $routeContainer->add($route2);
        $routeContainer->add($route3);
        $routeContainer->add($route4);
        $routeContainer->add($route5);
        $routeContainer->add($route6);
        $routeContainer->add($route7);

        return $routeContainer;
    }
}