<?php

namespace Kelemen\ApiNette\Validator;

use Kelemen\ApiNette\Exception\ValidatorException;
use Kelemen\ApiNette\Validator\Input\CookieInput;
use Kelemen\ApiNette\Validator\Input\FileInput;
use Kelemen\ApiNette\Validator\Input\GetInput;
use Kelemen\ApiNette\Validator\Input\InputInterface;
use Kelemen\ApiNette\Validator\Input\JsonInput;
use Kelemen\ApiNette\Validator\Input\PostInput;
use Kelemen\ApiNette\Validator\Input\PostRawInput;
use Nette\Utils\Validators;

class Validator implements ValidatorInterface
{
    /** @var array */
    private $validators = [];

    /** @var array */
    private $inputs = [];

    /** @var array */
    private $values = [];

    /** @var array */
    private $errors = [];

    /**
     * Configure validator
     */
    public function __construct()
    {
        $this->inputs = [
            'get' => new GetInput(),
            'post' => new PostInput(),
            'cookie' => new CookieInput(),
            'file' => new FileInput(),
            'postRaw' => new PostRawInput(),
            'json' => new JsonInput()
        ];
    }

    /**
     * Check if all validations are valid
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Get all validation errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set validator
     * @param string $name
     * @param callable $callback
     */
    public function setValidator($name, callable $callback)
    {
        $this->validators[$name] = $callback;
    }

    /**
     * Set input
     * @param string $name
     * @param InputInterface $input
     */
    public function setInput($name, InputInterface $input)
    {
        $this->inputs[$name] = $input;
    }

    /**
     * Validate given validators and store errors
     * @param array $validations
     * @throws ValidatorException
     */
    public function validate(array $validations)
    {
        $this->reset(); // For multiple use

        foreach ($validations as $validation) {
            if (!isset($this->inputs[$validation->getType()])) {
                throw new ValidatorException('Type ' . $validation->getType() . ' not registered');
            }

            $data = $this->inputs[$validation->getType()]->getData();
            $rules = $validation->getRules() !== null ? explode('|', $validation->getRules()) : [];

            // Check if param is mandatory
            if (in_array('required', $rules)) {
                if (!isset($data[$validation->getKey()])) {
                    $this->errors[] = 'Validation for ' . $validation->getKey() . ' failed | required';
                    continue;
                }
                unset($rules[array_search('required', $rules)]);
            }

            // Check if optional param is set
            if (!isset($data[$validation->getKey()])) {
                continue;
            }

            $value = $data[$validation->getKey()];
            if ($this->validateRules($validation, $value, $rules)) {
                $this->values[$validation->getResultKey() ?: $validation->getKey()] = $value;
            }
        }
    }

    /**
     * Validate all validation rules for given value
     * @param Validation $validation
     * @param mixed $value
     * @param array $rules
     * @return bool
     */
    private function validateRules(Validation $validation, $value, array $rules)
    {
        $result = true;
        foreach ($rules as $rule) {
            list($type) = explode(':', $rule);

            if (!isset($this->validators[$type])) {
                $validateResult = Validators::is($value, $rule);
            } elseif (strpos($rule, ':') === false) {
                $validateResult = call_user_func($this->validators[$rule], $value);
            } else {
                list($type, $ruleParams) = explode(':', $rule, 2);
                $validateResult = call_user_func_array($this->validators[$type], [
                    'value' => $value,
                    'ruleParams' => $ruleParams
                ]);
            }

            if (!$validateResult) {
                $result = false;
                $this->errors[] = 'Validation for ' . $validation->getKey() . '(' . $value . ') failed | ' . $rule;
            }
        }

        return $result;
    }

    /**
     * Reset data
     */
    private function reset()
    {
        $this->errors = [];
        $this->values = [];
    }
}
