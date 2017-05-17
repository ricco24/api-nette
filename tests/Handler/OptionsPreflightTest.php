<?php

namespace Kelemen\ApiNette\Tests;

require_once __DIR__ . '/../Mock/DummyLogger.php';
require_once __DIR__ . '/../Mock/DummyLoggerStorage.php';
require_once __DIR__ . '/../Mock/BufferResponse.php';

use Kelemen\ApiNette\Api;
use Kelemen\ApiNette\Handler\OptionsPreflightHandler;
use Kelemen\ApiNette\Logger\Logger;
use Kelemen\ApiNette\Tests\Mock\BufferResponse;
use Kelemen\ApiNette\Tests\Mock\DummyLogger;
use Kelemen\ApiNette\Tests\Mock\DummyLoggerStorage;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit_Framework_TestCase;
use Tracy\Debugger;

class OptionsPreflightTest extends PHPUnit_Framework_TestCase
{
    /**
     * Empty configuration test
     */
    public function testEmptyConfig()
    {
        list($api, $httpResponse, $container) = $this->prepare();
        $api->get('user', '#handler');

        /** @var Container $container */
        $container->addService('handler', new OptionsPreflightHandler());
        $apiResponse = $api->run('user', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(null, $apiResponse->getData());
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Max-Age'));
        $this->assertEquals(null, $httpResponse->getHeader('Access-Control-Expose-Headers'));
        $this->assertEquals('POST,DELETE,PUT,GET,OPTIONS', $httpResponse->getHeader('Access-Control-Allow-Methods'));
        $this->assertEquals('Authorization,X-Requested-With', $httpResponse->getHeader('Access-Control-Allow-Headers'));
    }

    /**
     * Full configuration test
     */
    public function testFullConfig()
    {
        list($api, $httpResponse, $container) = $this->prepare();
        $api->get('user', '#handler');

        /** @var Container $container */
        $container->addService('handler', (new OptionsPreflightHandler())
            ->setAllowHeaders(['Vary', 'Authorization'])
            ->setAllowMethods(['GET', 'PATCH'])
            ->setExposeHeaders(['MyCustomHeader', 'MyCustomHeader2'])
            ->setControlMaxAge(600)
        );
        $apiResponse = $api->run('user', $this->prepareLogger());

        $this->assertEquals(200, $apiResponse->getCode());
        $this->assertEquals(null, $apiResponse->getData());
        $this->assertEquals(600, $httpResponse->getHeader('Access-Control-Max-Age'));
        $this->assertEquals('MyCustomHeader,MyCustomHeader2', $httpResponse->getHeader('Access-Control-Expose-Headers'));
        $this->assertEquals('GET,PATCH', $httpResponse->getHeader('Access-Control-Allow-Methods'));
        $this->assertEquals('Vary,Authorization', $httpResponse->getHeader('Access-Control-Allow-Headers'));
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