<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Route\Route;
use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic get set
     */
    public function testBasics()
    {
        $route = new Route('post', 'user/<id>', 'Kelemen/ApiNette/Handler/UserGetHandler');
        $this->assertEquals('post', $route->getMethod());
        $this->assertEquals('user/<id>', $route->getPattern());
        $this->assertEquals('Kelemen/ApiNette/Handler/UserGetHandler', $route->getHandler());
    }

    /**
     * Test config array getter
     */
    public function testConfig()
    {
        $config = [
            'middleware' => [
                'netteAuth',
                'corsHeaders'
            ]
        ];

        $route = new Route('get', 'user', '#handler', $config);
        $this->assertEquals($config, $route->getConfig());
        $this->assertEquals($config['middleware'], $route->getConfig('middleware'));
    }

    /**
     * Test params parsing form pattern
     */
    public function testParamsParseFromPattern()
    {
        $route = new Route('post', 'user/<id>', '#handler');
        $this->assertEquals(['id'], $route->getParams());

        $route = new Route('post', 'user/<id>/message/<messageId>', '#handler');
        $this->assertEquals(['id', 'messageId'], $route->getParams());

        $route = new Route('post', 'user/<id>/<subId>/<subSubId>', '#handler');
        $this->assertEquals(['id', 'subId', 'subSubId'], $route->getParams());
    }

    /**
     * Test preg patter building
     */
    public function testPregPattern()
    {
        $route = new Route('post', 'user/<id>', '#handler');
        $this->assertEquals("^user/(?'id'[^/]*)?$", $route->getPregPattern());

        $route = new Route('post', 'user/<id>/message/<messageId>', '#handler');
        $this->assertEquals("^user/(?'id'[^/]*)?/message/(?'messageId'[^/]*)?$", $route->getPregPattern());

        $route = new Route('post', 'user/<id>/<subId>/<subSubId>', '#handler');
        $this->assertEquals("^user/(?'id'[^/]*)?/(?'subId'[^/]*)?/(?'subSubId'[^/]*)?$", $route->getPregPattern());
    }
}