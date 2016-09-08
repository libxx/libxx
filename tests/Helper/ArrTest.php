<?php

namespace LibxxTest\Helper;

use Libxx\Helper\Arr;

class ArrTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider mergeDataProvider
     *
     * @param array $a
     * @param array $b
     * @param array $expected
     */
    public function testMerge($a, $b, $expected)
    {
        $this->assertEquals($expected, Arr::merge($a, $b));
    }

    public function testMap()
    {
        $a = [1, 2, 3];
        $b = function ($v, $k) {
            return [$v * 2, $k];
        };
        $c = [[2, 0], [4, 1], [6, 2]];

        $this->assertEquals($c, Arr::map($a, $b));
    }

    public function testMapWithAdditionalParameters()
    {
        $a = [1, 2, 3];
        $b = function ($v, $k, $arg) {
            return $v * $arg;
        };
        $c = 2;
        $d = [2, 4, 6];

        $this->assertEquals($d, Arr::map($a, $b, $c));
    }

    public function testGet()
    {
        $data = [
            'foo' => 1,
            'bar' => [
                'baz' => 2
            ]
        ];

        $this->assertEquals(1, Arr::get($data, 'foo'));
        $this->assertEquals($data['bar'], Arr::get($data, 'bar'));
        $this->assertNull(Arr::get($data, 'baz'));
        $this->assertEquals(PHP_INT_MAX, Arr::get($data, 'baz', PHP_INT_MAX));
        $this->assertEquals(2, Arr::get($data, 'bar.baz'));
    }

    public function mergeDataProvider()
    {
        return [
            [
                [1, 2, 3],
                [1, 2, 3],
                [1, 2, 3, 1, 2, 3]
            ],
            [
                [1, 2, 'foo' => 'bar', 'baz' => []],
                [3, 4, 'foo' => 'bar1', 'bar' => 'baz', 'baz' => 1],
                [
                    1, 2, 'foo' => 'bar1', 3, 4, 'bar' => 'baz', 'baz' => 1
                ]
            ],
            [
                ['bar' => 'bar'],
                ['bar' => ['baz' => 'baz1']],
                [
                    'bar' => ['baz' => 'baz1']
                ]
            ]
        ];
    }
}
