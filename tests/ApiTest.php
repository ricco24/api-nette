<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Api;
use Kelemen\ApiNette\Exception\UnresolvedHandlerException;
use Kelemen\ApiNette\Route\BaseRouteResolver;
use Kelemen\ApiNette\Route\Route;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use PHPUnit_Framework_TestCase;

class ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Unregistered handler exception
     */
    public function testUnregisteredHandler()
    {
        $this->expectException(UnresolvedHandlerException::class);

        $api = $this->prepareApi('post');
        $api->add('post', 'user/{id}', '#hander1');
        $api->post('message/{id}', '#hander2');

        $api->run('user/10');
    }

    /**
     * Test all register methods for routes
     */
    public function testRegisterHttpMethods()
    {
        $api = $this->prepareApi('get');
        $api->get('userGet', '#handler');
        $api->get('userGet/{id}', '#handler');
        $api->get('userGetPost', '#handler');
        $api->post('userPost', '#handler');
        $api->post('userGetPost', '#handler');
        $api->post('userPost/{id}', '#handler');
        $api->put('userPut', '#handler');
        $api->delete('userDelete', '#handler');
        $api->put('userPut/{id}', '#handler');
        $api->delete('userDelete/{id}', '#handler');
        $api->options('userOptions', '#handler');
        $api->patch('userPatch', '#handler');
        $api->patch('userPatch/{id}', '#handler');
        $api->options('userOptions/{id}', '#handler');
        $api->add('purge', 'userPurge', '#handler');
        $api->add('purge', 'userPurge/{id}', '#handler');
        $api->add('get', 'userGetAdd', '#handler');

        $routeDefinition = [
            'get' => [
                new Route('get', 'userGet', '#handler'),
                new Route('get', 'userGet/{id}', '#handler'),
                new Route('get', 'userGetPost', '#handler'),
                new Route('get', 'userGetAdd', '#handler')
            ],
            'post' => [
                new Route('post', 'userPost', '#handler'),
                new Route('post', 'userGetPost', '#handler'),
                new Route('post', 'userPost/{id}', '#handler')
            ],
            'put' => [
                new Route('put', 'userPut', '#handler'),
                new Route('put', 'userPut/{id}', '#handler')
            ],
            'delete' => [
                new Route('delete', 'userDelete', '#handler'),
                new Route('delete', 'userDelete/{id}', '#handler')
            ],
            'options' => [
                new Route('options', 'userOptions', '#handler'),
                new Route('options', 'userOptions/{id}', '#handler')
            ],
            'patch' => [
                new Route('patch', 'userPatch', '#handler'),
                new Route('patch', 'userPatch/{id}', '#handler')
            ],
            'purge' => [
                new Route('purge', 'userPurge', '#handler'),
                new Route('purge', 'userPurge/{id}', '#handler')
            ]
        ];

        $routes = $api->getRoutes();

        $this->assertEquals($routeDefinition['get'], $routes->getRoutes('get'));
        $this->assertEquals($routeDefinition['post'], $routes->getRoutes('post'));
        $this->assertEquals($routeDefinition['put'], $routes->getRoutes('put'));
        $this->assertEquals($routeDefinition['delete'], $routes->getRoutes('delete'));
        $this->assertEquals($routeDefinition['options'], $routes->getRoutes('options'));
        $this->assertEquals($routeDefinition['patch'], $routes->getRoutes('patch'));
        $this->assertEquals($routeDefinition['purge'], $routes->getRoutes('purge'));
    }

    /**
     * @param string $method
     * @return Api
     */
    private function prepareApi($method)
    {
        $container = new Container();
        $request = new Request(new UrlScript(), null, null, null, null, null, $method);
        $response = new Response();
        return new Api($request, $response, $container);
    }
}