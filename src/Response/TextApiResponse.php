<?php

namespace Kelemen\ApiNette\Response;

use Nette\Utils\JsonException;

class TextApiResponse extends BaseApiResponse
{
    /**
     * @param int $code
     * @param string $data
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($code, $data = '', $contentType = 'text/plain', $charset = 'utf-8')
    {
        parent::__construct($code, $data, $contentType, $charset);
    }

    /**
     * @return string
     * @throws JsonException
     */
    public function getEncodedData()
    {
        return $this->data;
    }
}
