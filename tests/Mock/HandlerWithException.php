<?php

namespace Kelemen\ApiNette\Tests\Mock;

use Kelemen\ApiNette\Handler\BaseHandler;
use Nette\Http\Request;
use Nette\Http\Response;
use Exception;

class HandlerWithException extends BaseHandler
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        throw new Exception('Here i am!');
    }
}