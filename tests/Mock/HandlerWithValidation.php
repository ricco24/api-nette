<?php

namespace Kelemen\ApiNette\Tests\Mock;

use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Response\JsonApiResponse;
use Kelemen\ApiNette\Validator\Validation;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;

class HandlerWithValidation extends BaseHandler
{
    public function validate()
    {
        return [
            new Validation('path', 'id', 'numeric')
        ];
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        return new JsonApiResponse(IResponse::S200_OK, ['message' => 'I am dummy']);
    }
}