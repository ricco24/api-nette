<?php

namespace Kelemen\ApiNette\Validator;

interface ValidatorInterface
{
    /**
     * Check if all validations are valid
     * @return bool
     */
    public function isValid();

    /**
     * Get all validation errors
     * @return array
     */
    public function getErrors();

    /**
     * Get all successfully parsed values
     * @return array
     */
    public function getValues();

    /**
     * Validate given validators and store errors
     * @param array $validations
     */
    public function validate(array $validations);
}
