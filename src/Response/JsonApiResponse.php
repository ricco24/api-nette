<?php

namespace Kelemen\ApiNette\Response;

use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonApiResponse extends BaseApiResponse
{
    /** @var string|null */
    private $encodedData = null;

    /**
     * @param int $code
     * @param mixed $data
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($code, $data = '', $contentType = 'application/json', $charset = 'utf-8')
    {
        parent::__construct($code, $data, $contentType, $charset);
    }

    /**
     * @return string
     * @throws JsonException
     */
    public function getEncodedData()
    {
        if ($this->encodedData === null) {
            $this->encodedData = Json::encode($this->data);
        }
        return $this->encodedData;
    }
}
