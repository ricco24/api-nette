<?php

namespace Kelemen\ApiNette\Response;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use DateTimeInterface;

abstract class BaseApiResponse implements ApiResponse
{
    /** @var int */
    protected $code;

    /** @var mixed */
    protected $data;

    /** @var string */
    protected $contentType;

    /** @var string */
    protected $charset;

    /** @var string|int|DateTimeInterface */
    protected $expiration = 0;

    /**
     * @param int $code
     * @param mixed $data
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($code, $data, $contentType, $charset)
    {
        $this->code = $code;
        $this->data = $data;
        $this->contentType = $contentType;
        $this->charset = $charset;
    }

    /**
     * @param string|int|DateTimeInterface $expiration
     * @return BaseApiResponse
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * Return api response http code
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the MIME content type of a downloaded file.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Return encoding charset for http response
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param IRequest $httpRequest
     * @param IResponse $httpResponse
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse)
    {
        $httpResponse->setCode($this->getCode());
        $httpResponse->setContentType($this->getContentType(), $this->charset);
        $httpResponse->setExpiration($this->expiration);
        $result = $this->getEncodedData();
        $httpResponse->setHeader('Content-Length', strlen($result));
        echo $result;
    }
}
