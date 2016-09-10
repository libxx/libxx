<?php

namespace LibxxTest\Helper;

use Libxx\Helper\HTTP;
use Psr\Http\Message\MessageInterface;

class HTTPTest extends \PHPUnit_Framework_TestCase
{

    public function testParseContentType()
    {
        $contentType = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeader')->with('Content-Type')->willReturn([$contentType]);

        $this->assertEquals('text/html', HTTP::parseContentType($message));
    }
}
