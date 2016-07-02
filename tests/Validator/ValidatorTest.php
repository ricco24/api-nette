<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Exception\ValidatorException;
use Kelemen\ApiNette\Validator\Input\CustomInput;
use Kelemen\ApiNette\Validator\Validation;
use Kelemen\ApiNette\Validator\Validator;
use PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidateBaseSuccess()
    {
        $validator = new Validator();
        $validator->setInput('get', new CustomInput([
            'id' => 5,
            'web' => 'http://www.kelemen-samuel.com'
        ]));
        $validator->setInput('post', new CustomInput([
            'name' => 'Samuel',
            'surname' => 'Kelemen'
        ]));
        $validator->validate([
            new Validation('get', 'id', 'required|integer'),
            new Validation('get', 'web', 'required|string:10..|url'),
            new Validation('post', 'surname', 'string:..10'),
            new Validation('post', 'address', 'string:..30')
        ]);

        $this->assertEquals([], $validator->getErrors());
        $this->assertTrue($validator->isValid());
    }

    public function testValidateFail()
    {
        $validator = new Validator();
        $validator->setInput('get', new CustomInput([
            'id' => 'Some string',
            'name' => 'Other sting'
        ]));
        $validator->validate([
            new Validation('get', 'id', 'integer'),
            new Validation('get', 'name', 'integer')
        ]);

        $this->assertNotEmpty($validator->getErrors());
        $this->assertEquals(2, count($validator->getErrors()));
        $this->assertFalse($validator->isValid());
    }

    public function testValidateRequiredFail()
    {
        $validator = new Validator();
        $validator->setInput('get', new CustomInput([
            'id' => 5
        ]));
        $validator->validate([
            new Validation('get', 'id', 'integer'),
            new Validation('get', 'name', 'required|string:5..20')
        ]);

        $this->assertNotEmpty($validator->getErrors());
        $this->assertEquals(1, count($validator->getErrors()));
        $this->assertFalse($validator->isValid());
    }

    public function testCustomValidatorSuccess()
    {
        $validator = new Validator();

        // Non parametric validator
        $validator->setValidator('isSamuel', function ($value) {
            return $value === 'Samuel';
        });

        // Parametric validator
        $validator->setValidator('enum', function ($value, $ruleParams) {
            return in_array($value, explode(',', $ruleParams));
        });

        $validator->setInput('get', new CustomInput([
            'id' => 5,
            'name' => 'Samuel',
            'surname' => 'Kelemen'
        ]));
        $validator->validate([
            new Validation('get', 'name', 'required|string|isSamuel'),
            new Validation('get', 'surname', 'string:3..|enum:Peter,Kelemen,Weiss')
        ]);

        $this->assertEquals([], $validator->getErrors());
        $this->assertTrue($validator->isValid());
    }

    public function testCustomValidatorFail()
    {
        $validator = new Validator();

        // Non parametric validator
        $validator->setValidator('isSamuel', function ($value) {
            return $value === 'Samuel';
        });

        // Parametric validator
        $validator->setValidator('enum', function ($value, $ruleParams) {
            return in_array($value, explode(',', $ruleParams));
        });

        $validator->setInput('get', new CustomInput([
            'id' => 5,
            'name' => 'Peter',
            'surname' => 'Morho'
        ]));
        $validator->validate([
            new Validation('get', 'name', 'required|string|isSamuel'),
            new Validation('get', 'surname', 'string:3..|enum:Peter,Kelemen,Weiss')
        ]);

        $this->assertNotEmpty($validator->getErrors());
        $this->assertEquals(2, count($validator->getErrors()));
        $this->assertFalse($validator->isValid());
    }

    public function testNotRegisteredInput()
    {
        $this->expectException(ValidatorException::class);
        $validator = new Validator();

        $validator->setInput('get', new CustomInput([
            'id' => 5,
            'web' => 'http://www.kelemen-samuel.com'
        ]));
        $validator->validate([
            new Validation('get', 'id', 'required|integer'),
            new Validation('unknown', 'name', 'string')
        ]);
    }
}