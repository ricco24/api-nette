<?php

namespace Kelemen\ApiNette\Validator\Input;

class PostInput implements InputInterface
{
    public function getData()
    {
        return $_POST;
    }
}
