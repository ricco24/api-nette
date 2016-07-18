<?php

namespace Kelemen\ApiNette\Validator;

class Validation
{
    /** @var string */
    private $type;

    /** @var string */
    private $key;

    /** @var string */
    private $rules;

    /** @var null|string */
    private $resultKey;

    /**
     * @param string $type
     * @param string $key
     * @param null|string $rules
     * @param null|string $resultKey
     */
    public function __construct($type, $key, $rules = null, $resultKey = null)
    {
        $this->type = $type;
        $this->key = $key;
        $this->rules = $rules;
        $this->resultKey = $resultKey !== null ? $resultKey : $key;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return null|string
     */
    public function getResultKey()
    {
        return $this->resultKey;
    }
}
