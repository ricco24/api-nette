<?php

namespace Kelemen\ApiNette\Handler;

use Kelemen\ApiNette\Response\ApiResponse;
use Nette\Http\Request;
use Nette\Http\Response;

abstract class BaseHandler
{
    /** @var array      Values validated by validator. Doesn't contains all input values! */
    protected $values;

    /**
     * Get validated value or default if key is not set (for optional values)
     * @param string $key
     * @param string $default
     * @return string
     */
    protected function getValue($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }

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
     * @return ApiResponse
     */
    abstract public function __invoke(Request $request, Response $response, callable $next);
}
