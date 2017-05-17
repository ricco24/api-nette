<?php

namespace Kelemen\ApiNette\Middleware;

use Kelemen\ApiNette\Exception\MiddlewareException;
use Kelemen\ApiNette\Response\JsonApiResponse;
use Nette\Http\Request;
use Nette\Http\Response;

class CORSHeaders implements MiddlewareInterface
{
    const MIRROR = 'mirror';
    const ALL = 'all';
    const CUSTOM = 'custom';

    /** @var string */
    private $behaviour;

    /** @var array */
    private $allowedOrigins = [];

    /** @var bool */
    private $allowedCredentials = false;

    /**
     * @param string $behaviour
     * @throws MiddlewareException
     */
    public function __construct($behaviour = self::ALL)
    {
        if (!in_array($behaviour, [self::MIRROR, self::ALL, self::CUSTOM])) {
            throw new MiddlewareException('Behaviour ' . $behaviour . ' is not allowed value');
        }

        $this->behaviour = $behaviour;
    }

    /**
     * @param array $allowedOrigins
     * @return CORSHeaders
     */
    public function allowedOrigins(array $allowedOrigins)
    {
        $this->allowedOrigins = $allowedOrigins;
        return $this;
    }

    /**
     * @param bool $allowCredentials
     * @return CORSHeaders
     */
    public function allowCredentials($allowCredentials = true)
    {
        $this->allowedCredentials = $allowCredentials;
        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return JsonApiResponse
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $origin = $request->getHeader('Origin');

        // Add CORS headers only if mandatory "Origin" header is set
        if (!$origin) {
            return $next($request, $response);
        }

        switch ($this->behaviour) {
            case self::ALL:
                $this->applyAll($response);
                break;
            case self::MIRROR:
                $this->applyMirror($response, $origin);
                break;
            case self::CUSTOM:
                $this->applyCustom($response);
        }

        return $next($request, $response);
    }

    /**
     * @param Response $response
     * @throws MiddlewareException
     */
    private function applyAll(Response $response)
    {
        $response->setHeader('Access-Control-Allow-Origin', '*');
    }

    /**
     * @param Response $response
     * @param string $origin
     */
    private function applyMirror(Response $response, $origin)
    {
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        if ($this->allowedCredentials) {
            $response->setHeader('Access-Control-Allow-Credentials', true);
        }
    }

    /**
     * @param Response $response
     * @throws MiddlewareException
     */
    private function applyCustom(Response $response)
    {
        if (!count($this->allowedOrigins)) {
            throw new MiddlewareException('Variable $allowedOrigins cannot be empty for CUSTOM behaviour');
        }

        $response->setHeader('Access-Control-Allow-Origin', implode(',', $this->allowedOrigins));
        if ($this->allowedCredentials) {
            $response->setHeader('Access-Control-Allow-Credentials', true);
        }
    }
}
