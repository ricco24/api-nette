<?php

namespace Kelemen\ApiNette\Logger\Storage;

use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Route\ResolvedRoute;
use Nette\Http\Request;

interface LoggerStorageInterface
{
    public function store(
        $requestUrl,
        Request $httpRequest,
        ResolvedRoute $resolvedRoute,
        BaseHandler $handler,
        ApiResponse $response,
        $duration
    );
}
