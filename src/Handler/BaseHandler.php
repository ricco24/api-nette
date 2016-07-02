<?php

namespace Kelemen\ApiNette\Handler;

use Nette\Application\IResponse;
use Nette\Http\Request;
use Nette\Http\Response;

abstract class BaseHandler
{
    /** @var array      Values validated by validator. Doesn't contains all input values! */
    protected $values;

    /**
     * @param array $values
     */
    public function setValidatedValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * Validate input
     * @return array
     */
    public function validate()
    {
        return [];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return IResponse
     */
    abstract public function __invoke(Request $request, Response $response, callable $next);
}
