<?php

namespace Kelemen\ApiNette\Exception;

use Kelemen\ApiNette\Validator\ValidatorInterface;

class ValidationFailedException extends ApiNetteException
{
    /** @var ValidatorInterface */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct("", 0, null);
        $this->validator = $validator;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
