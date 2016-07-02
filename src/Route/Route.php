<?php

namespace Kelemen\ApiNette\Route;

class Route
{
    /** @var string */
    private $method;

    /** @var string */
    private $pattern;

    /** @var string */
    private $handler;

    /** @var array */
    private $config;

    /**
     * @param string $method
     * @param string $pattern
     * @param string $handler
     * @param array $config
     */
    public function __construct($method, $pattern, $handler, array $config = [])
    {
        $this->method = strtolower($method);
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string|null $key
     * @return array|false
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return isset($this->config[$key]) ? $this->config[$key] : false;
    }

    /**
     * Parse defined pattern params
     * @return array
     */
    public function getParams()
    {
        preg_match_all('#\{(.*?)\}#', $this->pattern, $params);
        return $params[1];
    }

    /**
     * Prepare preg replace pattern from defined route pattern
     * @return string
     */
    public function getPregPattern()
    {
        return '^' . preg_replace('#\{.*?\}#', '([^/]*)?', $this->pattern) . '$';
    }
}
