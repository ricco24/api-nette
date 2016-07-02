<?php

namespace Kelemen\ApiNette\Validator\Input;

class JsonInput implements InputInterface
{
    private $data = null;

    public function getData()
    {
        if ($this->data === null) {
            $this->data = json_decode(file_get_contents("php://input"), true);
        }

        return $this->data;
    }
}
