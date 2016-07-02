<?php

namespace Kelemen\ApiNette\Validator\Input;

interface InputInterface
{
    /**
     * Returns data for input
     * @return array
     */
    public function getData();
}
