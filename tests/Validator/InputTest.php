<?php

namespace Kelemen\ApiNette\Tests;

use Kelemen\ApiNette\Validator\Input\CookieInput;
use Kelemen\ApiNette\Validator\Input\CustomInput;
use Kelemen\ApiNette\Validator\Input\FileInput;
use Kelemen\ApiNette\Validator\Input\GetInput;
use Kelemen\ApiNette\Validator\Input\JsonInput;
use Kelemen\ApiNette\Validator\Input\PostInput;
use Kelemen\ApiNette\Validator\Input\PostRawInput;
use PHPUnit_Framework_TestCase;

class InputTest extends PHPUnit_Framework_TestCase
{
    public function testCookieInput()
    {
        $_COOKIE = [
            'id' => 5,
            'city' => 'Bratislava'
        ];
        $input = new CookieInput();
        $this->assertEquals($_COOKIE, $input->getData());
    }

    public function testCustomInput()
    {
        $data = [
            'id' => 5,
            'city' => 'Bratislava'
        ];
        $input = new CustomInput($data);
        $this->assertEquals($data, $input->getData());
    }

    public function testFileInput()
    {
        $_FILES = [
            'id' => 5,
            'city' => 'Bratislava'
        ];
        $input = new FileInput();
        $this->assertEquals($_FILES, $input->getData());
    }

    public function testGetInput()
    {
        $_GET = [
            'id' => 5,
            'city' => 'Bratislava'
        ];
        $input = new GetInput();
        $this->assertEquals($_GET, $input->getData());
    }

    public function testPostInput()
    {
        $_POST = [
            'id' => 5,
            'city' => 'Bratislava'
        ];
        $input = new PostInput();
        $this->assertEquals($_POST, $input->getData());
    }
}