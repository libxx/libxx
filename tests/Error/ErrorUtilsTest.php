<?php

namespace LibxxTest\Error;

use Libxx\Error\ErrorUtils;

class ErrorUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testErrorConvert()
    {
        $error = $this->createMock(\Error::class);
        $this->assertInstanceOf(\ErrorException::class, ErrorUtils::convertErrorToException($error));

        $arrayError = [
            'message' => null,
            'type' => null,
            'code' => null,
            'file' => null,
            'line' => null,
        ];
        $this->assertInstanceOf(\ErrorException::class, ErrorUtils::convertErrorToException($arrayError));
    }
}
