<?php

namespace Kelemen\ApiNette\Tests\Mock;

use Nette\Http\Response;

class BufferResponse extends Response
{
    /** @var array */
    private $headers = [];

    /**
     * @param string $name
     * @param mixed $value
     * @return BufferResponse
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return BufferResponse
     */
    public function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = [$this->headers[$name], $value];
                return $this;
            }

            $this->headers[$name] = array_merge($this->headers[$name], $value);
            return $this;
        }

        $this->setHeader($name, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getHeader($header, $default = NULL)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : $default;
    }
}