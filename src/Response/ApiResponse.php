<?php

namespace Kelemen\ApiNette\Response;

use Nette\Application\IResponse;

interface ApiResponse extends IResponse
{
    /**
     * Returns response code
     * @return int
     */
    public function getCode();

    /**
     * Returns encoded response data
     * @return string
     */
    public function getEncodedData();
}
