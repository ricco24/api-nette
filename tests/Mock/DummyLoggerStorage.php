<?php

namespace Kelemen\ApiNette\Tests\Mock;

use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Logger\Storage\LoggerStorageInterface;
use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Route\ResolvedRoute;
use Nette\Http\Request;

class DummyLoggerStorage implements LoggerStorageInterface
{
    public function store(
        Request $httpRequest,
        ApiResponse $response,
        $duration,
        ResolvedRoute $resolvedRoute = null,
        BaseHandler $handler = null
    ) {
        // Do nothing
    }
}