<?php

namespace Kelemen\ApiNette\Handler;

use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Response\TextApiResponse;
use Nette\Http\Request;
use Nette\Http\Response;

class OptionsPreflightHandler extends BaseHandler
{
    /** @var array */
    private $allowMethods = ['POST', 'DELETE', 'PUT', 'GET', 'OPTIONS'];

    /** @var array */
    private $allowHeaders = ['Authorization', 'X-Requested-With'];

    /** @var int */
    private $controlMaxAge = 0;

    /** @var array */
    private $exposeHeaders = [];

    /**
     * @param array $allowMethods
     * @return OptionsPreflightHandler
     */
    public function setAllowMethods(array $allowMethods)
    {
        $this->allowMethods = $allowMethods;
        return $this;
    }

    /**
     * @param array $allowHeaders
     * @return OptionsPreflightHandler
     */
    public function setAllowHeaders(array $allowHeaders)
    {
        $this->allowHeaders = $allowHeaders;
        return $this;
    }

    /**
     * @param int $controlMaxAge
     * @return OptionsPreflightHandler
     */
    public function setControlMaxAge($controlMaxAge)
    {
        $this->controlMaxAge = (int) $controlMaxAge;
        return $this;
    }

    /**
     * @param array $exposeHeaders
     * @return OptionsPreflightHandler
     */
    public function setExposeHeaders(array $exposeHeaders)
    {
        $this->exposeHeaders = $exposeHeaders;
        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return ApiResponse
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ($this->controlMaxAge) {
            $response->addHeader('Access-Control-Max-Age', $this->controlMaxAge);
        }

        if (count($this->exposeHeaders)) {
            $response->addHeader('Access-Control-Expose-Headers', implode(',', $this->exposeHeaders));
        }

        if (count($this->allowMethods)) {
            $response->addHeader('Access-Control-Allow-Methods', implode(',', $this->allowMethods));
        }

        if (count($this->allowHeaders)) {
            $response->addHeader('Access-Control-Allow-Headers', implode(',', $this->allowHeaders));
        }
        return new TextApiResponse(200);
    }
}
