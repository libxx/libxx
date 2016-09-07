<?php

namespace LibxxTest\Routing;

use Libxx\Routing\MatchResult;
use Libxx\Routing\Route;

class MatchResultTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteMatched()
    {
        $matchedParameters = ['foo' => 1];
        $route = new Route('GET', '/foo', null);
        $matchResult = new MatchResult(true, [], $matchedParameters, $route);
        $this->assertTrue($matchResult->isSuccess());
        $this->assertFalse($matchResult->isMethodFailure());
        $this->assertEmpty($matchResult->getAllowedMethods());
        $this->assertEquals($matchedParameters, $matchResult->getMatchedParameters());
        $this->assertSame($route, $matchResult->getMatchedRoute());
    }

    public function testRouteMethodNotAllowed()
    {
        $allowedMethods = ['GET', 'POST'];
        $matchResult = new MatchResult(false, $allowedMethods, []);
        $this->assertFalse($matchResult->isSuccess());
        $this->assertTrue($matchResult->isMethodFailure());
        $this->assertEquals($allowedMethods, $matchResult->getAllowedMethods());
        $this->assertEmpty($matchResult->getMatchedParameters());
        $this->assertNull($matchResult->getMatchedRoute());
    }

    public function testRouteNotFound()
    {
        $matchResult = new MatchResult(false, [], []);
        $this->assertFalse($matchResult->isSuccess());
        $this->assertFalse($matchResult->isMethodFailure());
        $this->assertEmpty($matchResult->getAllowedMethods());
        $this->assertEmpty($matchResult->getMatchedParameters());
        $this->assertNull($matchResult->getMatchedRoute());
    }
}
