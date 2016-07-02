<?php

namespace Kelemen\ApiNette\Validator\Input;

class GetInput implements InputInterface
{
    public function getData()
    {
        return $_GET;
    }
}
