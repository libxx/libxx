<?php

namespace LibxxTest\Kernel;

use Libxx\Container\Container;
use Libxx\Kernel\CallableResolver;

class Invokable
{

    public function __invoke()
    {
    }

    public function invoke()
    {
    }
}


class CallableResolverTest extends \PHPUnit_Framework_TestCase
{

    public function testClosure()
    {
        $context = function () {
        };
        $this->assertSame($context, $this->createCallableResolver()->resolve($context));
    }

    public function testObjectMethod()
    {
        $object = new Invokable();
        $context = [$object, 'invoke'];

        $this->assertSame($context, $this->createCallableResolver()->resolve($context));
    }

    public function testClassMethod()
    {
        $context = ['invokable', 'invoke'];

        $callable = $this->createCallableResolver([
            'invokable' => function () {
                return new Invokable();
            }
        ])->resolve($context);

        $this->assertInternalType('array', $callable);
        $this->assertInternalType('object', $callable[0]);
        $this->assertInstanceOf(Invokable::class, $callable[0]);
        $this->assertEquals('invoke', $callable[1]);
    }

    public function testStringWithMethod()
    {
        $context = 'invokable::invoke';

        $callable = $this->createCallableResolver([
            'invokable' => function () {
                return new Invokable();
            }
        ])->resolve($context);

        $this->assertInternalType('array', $callable);
        $this->assertInternalType('object', $callable[0]);
        $this->assertInstanceOf(Invokable::class, $callable[0]);
        $this->assertEquals('invoke', $callable[1]);
    }

    public function testStringWithInvokableClass()
    {
        $context = 'invokable';

        $callable = $this->createCallableResolver([
            'invokable' => function () {
                return new Invokable();
            }
        ])->resolve($context);

        $this->assertInternalType('array', $callable);
        $this->assertInternalType('object', $callable[0]);
        $this->assertInstanceOf(Invokable::class, $callable[0]);
        $this->assertEquals('__invoke', $callable[1]);
    }

    public function testInvokableObject()
    {
        $context = new Invokable();
        $this->assertSame($context, $this->createCallableResolver()->resolve($context));
    }

    private function createCallableResolver(array $config = [])
    {
        $callableResolver = new CallableResolver();
        $container = new Container($config);
        $callableResolver->setContainer($container);
        return $callableResolver;
    }
}
