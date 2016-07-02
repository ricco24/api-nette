<?php

namespace Kelemen\ApiNette\Middleware;

use Nette\Application\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return IResponse
     */
    public function __invoke(Request $request, Response $response, callable $next);
}
