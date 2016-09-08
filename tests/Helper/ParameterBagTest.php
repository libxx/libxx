<?php

namespace LibxxTest\Helper;

use Libxx\Helper\ParameterBag;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $parameters = [
            'foo' => 'bar'
        ];
        $bag = new ParameterBag($parameters);
        $this->assertAttributeEquals($parameters, 'parameters', $bag);
    }

    public function testSetAndGetAndHas()
    {
        $bag = new ParameterBag();
        $this->assertFalse($bag->has('foo'));
        $this->assertNull($bag->get('foo'));
        $this->assertEquals(PHP_INT_MAX, $bag->get('foo', PHP_INT_MAX));
        $value = 'bar';
        $bag->set('foo', $value);
        $this->assertEquals($value, $bag->get('foo'));
    }

    public function testReplace()
    {
        $parameters = [
            'foo' => 'bar'
        ];
        $bag = new ParameterBag($parameters);
        $bag->replace(['foo' => 'baz', 'baz' => 'bar']);
        $this->assertEquals('baz', $bag->get('foo'));
        $this->assertEquals('bar', $bag->get('baz'));
    }

    public function testRemove()
    {
        $parameters = [
            'foo' => 'bar'
        ];
        $bag = new ParameterBag($parameters);
        $bag->remove('foo');
        $this->assertFalse($bag->has('foo'));
    }

    public function testAllAndKeysAndCount()
    {
        $parameters = [
            'foo' => 'bar'
        ];
        $bag = new ParameterBag($parameters);
        $this->assertEquals($parameters, $bag->all());
        $this->assertEquals(array_keys($parameters), $bag->keys());
        $this->assertCount(1, $bag);
    }
}
