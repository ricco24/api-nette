<?php

namespace Kelemen\ApiNette\Tests\Mock;

use Tracy\ILogger;

class DummyLogger implements ILogger
{
    public function log($value, $priority = self::INFO)
    {
        // do nothing
    }
}