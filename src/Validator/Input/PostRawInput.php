<?php

namespace Kelemen\ApiNette\Validator\Input;

class PostRawInput implements InputInterface
{
    public function getData()
    {
        return file_get_contents("php://input");
    }
}
