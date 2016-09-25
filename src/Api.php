<?php

namespace Kelemen\ApiNette;

use Kelemen\ApiNette\Exception\UnresolvedHandlerException;
use Kelemen\ApiNette\Exception\UnresolvedRouteException;
use Kelemen\ApiNette\Exception\ValidationFailedException;
use Kelemen\ApiNette\Logger\Logger;
use Kelemen\ApiNette\Response\ApiResponse;
use Kelemen\ApiNette\Route\BaseRouteResolver;
use Kelemen\ApiNette\Route\RouteResolverInterface;
use Kelemen\ApiNette\Route\Route;
use Kelemen\ApiNette\Route\RouteContainer;
use Kelemen\ApiNette\Validator\Input\CustomInput;
use Kelemen\ApiNette\Validator\Validator;
use Kelemen\ApiNette\Validator\ValidatorInterface;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Http\Request;
use Nette\Http\Response;

class Api
{
    /** @var RouteResolverInterface */
    private $routeResolver;

    /** @var RouteContainer */
    private $routes;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var Container */
    private $container;

    /** @var ValidatorInterface */
    private $validator;

    /**
     * @param Request $request
     * @param Response $response
     * @param Container $container
     * @param RouteResolverInterface $routeResolver
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Request $request,
        Response $response,
        Container $container,
        RouteResolverInterface $routeResolver = null,
        ValidatorInterface $validator = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        $this->routeResolver = $routeResolver ?: new BaseRouteResolver($request);
        $this->validator = $validator ?: new Validator();
        $this->routes = new RouteContainer();
    }

    /**
     * Add api call
     * @param string $method
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function add($method, $pattern, $handler, $params = [])
    {
        $this->routes->add(new Route($method, $pattern, $handler, $params));
    }

    /**
     * Add get api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function get($pattern, $handler, $params = [])
    {
        $this->add('get', $pattern, $handler, $params);
    }

    /**
     * Add post api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function post($pattern, $handler, $params = [])
    {
        $this->add('post', $pattern, $handler, $params);
    }

    /**
     * Add put api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function put($pattern, $handler, $params = [])
    {
        $this->add('put', $pattern, $handler, $params);
    }

    /**
     * Add patch api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function patch($pattern, $handler, $params = [])
    {
        $this->add('patch', $pattern, $handler, $params);
    }

    /**
     * Add delete api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function delete($pattern, $handler, $params = [])
    {
        $this->add('delete', $pattern, $handler, $params);
    }

    /**
     * Add options api call
     * @param string $pattern
     * @param string $handler
     * @param array $params
     */
    public function options($pattern, $handler, $params = [])
    {
        $this->add('options', $pattern, $handler, $params);
    }

    /**
     * Returns all registered routes
     * @return RouteContainer
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $url
     * @param Logger $logger
     * @return ApiResponse
     * @throws UnresolvedHandlerException
     * @throws UnresolvedRouteException
     * @throws ValidationFailedException
     */
    public function run($url, Logger $logger)
    {
        $resolvedRoute = $this->routeResolver->resolve($this->routes, $url);
        if (!$resolvedRoute) {
            throw new UnresolvedRouteException();
        }
        $logger->setResolvedRoute($resolvedRoute);

        $handler = $this->getFromContainer($resolvedRoute->getRoute()->getHandler());
        if (!$handler) {
            throw new UnresolvedHandlerException('Handler ' . $resolvedRoute->getRoute()->getHandler() . ' not found in container');
        }
        $logger->setHandler($handler);

        $this->validator->setInput('path', new CustomInput($resolvedRoute->getParams()));
        $this->validator->validate($handler->validate());

        if (!$this->validator->isValid()) {
            throw new ValidationFailedException($this->validator);
        }

        $handler->setValidatedValues($this->validator->getValues());

        $middleware = $resolvedRoute->getRoute()->getConfig('middleware');
        $runner = new Runner($middleware ?: [], $handler, $this->container);
        $response = $runner($this->request, $this->response);
        return $response;
    }

    /**
     * Get service by type or name from container
     * @param string $entry
     * @return bool|object
     */
    private function getFromContainer($entry)
    {
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
