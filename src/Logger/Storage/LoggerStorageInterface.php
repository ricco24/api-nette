<?php

namespace Kelemen\ApiNette\Logger\Storage;

use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Route\ResolvedRoute;
use Nette\Http\Request;

interface LoggerStorageInterface
{
    public function store(
        Request $httpRequest,
        ApiResponse $response,
        $duration,
        ResolvedRoute $resolvedRoute = null,
        BaseHandler $handler = null
    );
}
