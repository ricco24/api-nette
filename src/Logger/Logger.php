<?php

namespace Kelemen\ApiNette\Logger;

use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Logger\Storage\LoggerStorageInterface;
use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Route\ResolvedRoute;
use Nette\Http\Request;

class Logger
{
    /** @var LoggerStorageInterface */
    private $storage;

    /** @var Request */
    private $httpRequest;

    /** @var ResolvedRoute */
    private $resolvedRoute;

    /** @var BaseHandler */
    private $handler;

    /** @var int */
    private $start = 0;

    /**
     * @param Request $httpRequest
     * @param LoggerStorageInterface $storage
     */
    public function __construct(Request $httpRequest, LoggerStorageInterface $storage)
    {
        $this->httpRequest = $httpRequest;
        $this->storage = $storage;
    }

    /**
     * @param ResolvedRoute $resolvedRoute
     */
    public function setResolvedRoute(ResolvedRoute $resolvedRoute)
    {
        $this->resolvedRoute = $resolvedRoute;
    }

    /**
     * @param BaseHandler $handler
     */
    public function setHandler(BaseHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Start logging
     */
    public function start()
    {
        $this->start = microtime(true);
    }

    /**
     * Finish logging
     * @param ApiResponse $response
     */
    public function finish(ApiResponse $response)
    {
        $this->storage->store(
            $this->httpRequest,
            $response,
            round((microtime(true) - $this->start) * 1000),
            $this->resolvedRoute,
            $this->handler
        );
    }
}
