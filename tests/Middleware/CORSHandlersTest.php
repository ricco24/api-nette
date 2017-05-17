<?php

namespace Kelemen\ApiNette\Tests;

require_once __DIR__ . '/../Mock/DummyLogger.php';
require_once __DIR__ . '/../Mock/DummyLoggerStorage.php';
require_once __DIR__ . '/../Mock/DummyHandler.php';
require_once __DIR__ . '/../Mock/BufferResponse.php';

use Kelemen\ApiNette\Api;
use Kelemen\ApiNette\Exception\MiddlewareException;
use Kelemen\ApiNette\Logger\Logger;
use Kelemen\ApiNette\Middleware\CORSHeaders;
use Kelemen\ApiNette\Tests\Mock\BufferResponse;
use Kelemen\ApiNette\Tests\Mock\DummyHandler;
use Kelemen\ApiNette\Tests\Mock\DummyLogger;
use Kelemen\ApiNette\Tests\Mock\DummyLoggerStorage;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit_Framework_TestCase;
use Tracy\Debugger;

class CORSHandlersTest extends PHPUnit_Framework_TestCase
{
    /**
     * All behaviour
     */
    public function testBehaviourAll()
    {
        list($api, $httpResponse, $container) = $this->prepare();
        $api->get('user', '#handler', ['middleware' => ['#corsMiddleware']]);
        $api->get('user_full', '#handler', ['middleware' => ['#corsFullMiddleware']]);

        /** @var Container $container */
        $container->addService('handler', new DummyHandler());
        $container->addService('corsMiddleware', new CORSHeaders());
        $container->addService('corsFullMiddleware', (new CORSHeaders())
            ->allowCredentials(true)
            ->allowedOrigins(['http://example2.org'])
        );

        $apiResponse = $api->run('user', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('*', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Allow-Credentials'));

        $apiResponse = $api->run('user_full', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('*', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Allow-Credentials'));
    }

    /**
     * Mirror behaviour
     */
    public function testBehaviourMirror()
    {
        list($api, $httpResponse, $container) = $this->prepare();
        $api->get('user', '#handler', ['middleware' => ['#corsMiddleware']]);
        $api->get('user_full', '#handler', ['middleware' => ['#corsFullMiddleware']]);

        /** @var Container $container */
        $container->addService('handler', new DummyHandler());
        $container->addService('corsMiddleware', new CORSHeaders(CORSHeaders::MIRROR));
        $container->addService('corsFullMiddleware', (new CORSHeaders(CORSHeaders::MIRROR))
            ->allowCredentials(true)
            ->allowedOrigins(['http://example2.org'])
        );

        $apiResponse = $api->run('user', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('http://example.org', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Allow-Credentials'));

        $apiResponse = $api->run('user_full', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('http://example.org', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(true, $httpResponse->getHeader('Access-Control-Allow-Credentials'));
    }

    /**
     * Custom behaviour
     */
    public function testBehaviourCustom()
    {
        list($api, $httpResponse, $container) = $this->prepare();
        $api->get('user', '#handler', ['middleware' => ['#corsMiddleware']]);
        $api->get('user_full', '#handler', ['middleware' => ['#corsFullMiddleware']]);
        $api->get('user_error', '#handler', ['middleware' => ['#corsErrorMiddleware']]);

        /** @var Container $container */
        $container->addService('handler', new DummyHandler());
        $container->addService('corsMiddleware', (new CORSHeaders(CORSHeaders::CUSTOM))
            ->allowedOrigins(['http://example7.org'])
        );
        $container->addService('corsFullMiddleware', (new CORSHeaders(CORSHeaders::CUSTOM))
            ->allowCredentials(true)
            ->allowedOrigins(['http://example2.org', 'http://example3.org'])
        );
        $container->addService('corsErrorMiddleware', (new CORSHeaders(CORSHeaders::CUSTOM))
            ->allowCredentials(true)
        );

        $apiResponse = $api->run('user', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('http://example7.org', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Allow-Credentials'));

        $apiResponse = $api->run('user_full', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $apiResponse->getData());
        $this->assertEquals('http://example2.org,http://example3.org', $httpResponse->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(true, $httpResponse->getHeader('Access-Control-Allow-Credentials'));

        $this->expectException(MiddlewareException::class);
        $api->run('user_error', $this->prepareLogger());
    }

    /**
     * Prepare objects for tests
     * @return array
     */
    private function prepare()
    {
        Debugger::setLogger(new DummyLogger());

        $container = new Container();
        $request = new Request(new UrlScript(), null, null, null, null, ['Origin' => 'http://example.org']);
        $response = new BufferResponse();
        $api = new Api($request, $response, $container);

        return [$api, $response, $container];
    }

    /**
     * @return Logger
     */
    private function prepareLogger()
    {
        return new Logger(new Request(new UrlScript()), new DummyLoggerStorage());
    }
}