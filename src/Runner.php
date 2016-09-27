<?php

namespace Kelemen\ApiNette;

use Kelemen\ApiNette\Exception\UnresolvedMiddlewareException;
use Kelemen\ApiNette\Handler\BaseHandler;
use Kelemen\ApiNette\Response\ApiResponse;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Http\Request;
use Nette\Http\Response;
use Closure;

class Runner
{
    /** @var array */
    protected $stack = [];

    /** @var BaseHandler */
    protected $handler;

    /** @var Container */
    protected $container;

    /**
     * @param array $stack
     * @param BaseHandler $handler
     * @param Container $container
     */
    public function __construct(array $stack, BaseHandler $handler, Container $container)
    {
        $this->stack = $stack;
        $this->handler = $handler;
        $this->container = $container;
    }

    /**
     * Run
     * @param Request $request
     * @param Response $response
     * @return ApiResponse
     * @throws UnresolvedMiddlewareException
     */
    public function __invoke(Request $request, Response $response)
    {
        $entry = array_shift($this->stack);
        $middleware = $this->getMiddleware($entry);
        if (!$middleware) {
            throw new UnresolvedMiddlewareException('Middleware ' . $entry . ' not found in container');
        }
        return $middleware($request, $response, $this);
    }

    /**
     * Get middleware - service from container or given handler
     * @param string $entry
     * @return Closure|false
     */
    private function getMiddleware($entry)
    {
        // If entry is empty return handler
        if (!$entry) {
            return $this->handler;
        }

        try {
            if (substr($entry, 0, 1) === '#') {
                return $this->container->getService(substr($entry, 1));
            }

            return $this->container->getByType($entry);
        } catch (MissingServiceException $e) {
            return false;
        }
    }
}
