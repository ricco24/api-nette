<?php

namespace Kelemen\ApiNette\Response;

use Nette\Application\Responses\JsonResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonApiResponse extends JsonResponse implements ApiResponse
{
    /** @var int */
    private $code;

    /** @var string */
    private $charset;

    /** @var string|null */
    private $encodedData = null;

    /**
     * @param int $code
     * @param array|\stdClass $data
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($code, $data, $contentType = 'application/json', $charset = 'utf-8')
    {
        parent::__construct($data, $contentType);
        $this->charset = $charset;
        $this->code = $code;
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
     * @return string
     * @throws JsonException
     */
    public function getEncodedData()
    {
        if ($this->encodedData === null) {
            $this->encodedData = Json::encode($this->getPayload());
        }
        return $this->encodedData;
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
        $httpResponse->setExpiration(false);
        $result = $this->getEncodedData();
        $httpResponse->setHeader('Content-Length', strlen($result));
        echo $result;
    }
}
