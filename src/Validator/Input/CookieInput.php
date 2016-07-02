<?php

namespace Kelemen\ApiNette\Validator\Input;

class CookieInput implements InputInterface
{
    public function getData()
    {
        return $_COOKIE;
    }
}
