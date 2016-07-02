<?php

namespace Kelemen\ApiNette\Validator\Input;

class FileInput implements InputInterface
{
    public function getData()
    {
        return $_FILES;
    }
}
