<?php

namespace Kelemen\ApiNette\Middleware;

use Kelemen\ApiNette\Response\ApiResponse;
use Nette\Http\Request;
use Nette\Http\Response;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return ApiResponse
     */
    public function __invoke(Request $request, Response $response, callable $next);
}
