<?php

namespace Kelemen\ApiNette\Tests;

require_once __DIR__ . '/../Mock/DummyLogger.php';
require_once __DIR__ . '/../Mock/DummyLoggerStorage.php';
require_once __DIR__ . '/../Mock/DummyHandler.php';
require_once __DIR__ . '/../Mock/HandlerWithValidation.php';
require_once __DIR__ . '/../Mock/HandlerWithException.php';

use Kelemen\ApiNette\Api;
use Kelemen\ApiNette\Logger\Logger;
use Kelemen\ApiNette\Presenter\ApiPresenter;
use Kelemen\ApiNette\Tests\Mock\DummyHandler;
use Kelemen\ApiNette\Tests\Mock\DummyLogger;
use Kelemen\ApiNette\Tests\Mock\DummyLoggerStorage;
use Kelemen\ApiNette\Tests\Mock\HandlerWithException;
use Kelemen\ApiNette\Tests\Mock\HandlerWithValidation;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Application\Request as AppRequest;
use Nette\Http\UrlScript;
use PHPUnit_Framework_TestCase;
use Tracy\Debugger;

class PresenterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Success flow test
     */
    public function testFlow()
    {
        list($api, $presenter, $container) = $this->prepare();
        $api->get('user/{id}', '#handler1');

        /** @var Container $container */
        $container->addService('handler1', new DummyHandler());

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'user/5']);
        $result = $presenter->run($request);

        $this->assertEquals(200, $result->getCode());
        $this->assertEquals(['message' => 'I am dummy'], $result->getPayload());
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Failed route
     */
    public function testUnregisteredRoute()
    {
        list($api, $presenter) = $this->prepare();
        $api->get('user/{id}', '#unknownHandler');

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'message/5']);
        $result = $presenter->run($request);

        $this->assertEquals(400, $result->getCode());
        $this->assertEquals(['error' => 'Unresolved api route'], $result->getPayload());
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Failed handler
     */
    public function testUnregisteredHandler()
    {
        list($api, $presenter) = $this->prepare();
        $api->get('user/{id}', '#unknownHandler');

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'user/5']);
        $result = $presenter->run($request);

        $this->assertEquals(500, $result->getCode());
        $this->assertEquals(['error' => 'Internal server error'], $result->getPayload());
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Failed middleware
     */
    public function testUnregisteredMiddleware()
    {
        list($api, $presenter, $container) = $this->prepare();
        $api->get('user/{id}', '#handler1', ['middleware' => ['#unknownMiddleware']]);

        /** @var Container $container */
        $container->addService('handler1', new DummyHandler());

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'user/5']);
        $result = $presenter->run($request);

        $this->assertEquals(500, $result->getCode());
        $this->assertEquals(['error' => 'Internal server error'], $result->getPayload());
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Failed validation
     */
    public function testFailedValidation()
    {
        list($api, $presenter, $container) = $this->prepare();
        $api->get('user/{id}', '#handler1');

        /** @var Container $container */
        $container->addService('handler1', new HandlerWithValidation());

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'user/some_string']);
        $result = $presenter->run($request);

        $this->assertEquals(400, $result->getCode());
        $this->assertEquals('Bad input parameter', $result->getPayload()['error']);
        $this->assertEquals(1, count($result->getPayload()['errors']));
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Failed not library thing
     */
    public function testNoLibraryException()
    {
        list($api, $presenter, $container) = $this->prepare();
        $api->get('user/{id}', '#handler1');

        /** @var Container $container */
        $container->addService('handler1', new HandlerWithException());

        $request = new AppRequest('Api:Api:default', 'GET', ['params' => 'user/10']);
        $result = $presenter->run($request);

        $this->assertEquals(500, $result->getCode());
        $this->assertEquals(['error' => 'Internal server error'], $result->getPayload());
        $this->assertEquals('application/json', $result->getContentType());
        $this->assertEquals('utf-8', $result->getCharset());
    }

    /**
     * Prepare objects for tests
     * @return array
     */
    private function prepare()
    {
        Debugger::setLogger(new DummyLogger());

        $container = new Container();
        $request = new Request(new UrlScript());
        $response = new Response();
        $api = new Api($request, $response, $container);

        $presenter = new ApiPresenter($api, $this->prepareLogger());
        $presenter->injectPrimary($container, null, null, $request, $response);

        return [$api, $presenter, $container];
    }

    /**
     * @return Logger
     */
    private function prepareLogger()
    {
        return new Logger(new Request(new UrlScript()), new DummyLoggerStorage());
    }
}