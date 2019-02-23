<?php

namespace ThoroughPHP\Arrays\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ThoroughPHP\Arrays\CompositeKeyArray;
use ThoroughPHP\Arrays\UndefinedOffsetException;

class CompositeKeyArrayTest extends TestCase
{
    /**
     * @dataProvider arrayDataProviderForIssetTesting
     */
    public function testOffsetExists($array, $offset, bool $exists): void
    {
        $array = new CompositeKeyArray($array);

        $this->assertEquals($exists, isset($array[$offset]));
    }

    public function arrayDataProviderForIssetTesting(): array
    {
        return [
            [[1], 0, true],
            [[1], 1, false],
            [[1], '', false],
            [[1], null, false],

            [['foo' => 'bar'], 'foo', true],
            [['foo' => 'bar'], 'bar', false],
            [['foo' => 'bar'], ['foo'], true],
            [['foo' => 'bar'], ['bar'], false],

            [['foo' => ['bar' => 'baz']], ['foo', 'bar'], true],
            [['foo' => ['bar' => 'baz']], ['foo', 'baz'], false],
        ];  
    }

    /**
     * @dataProvider arrayDataProviderForGetTesting
     */
    public function testOffsetGet($array, $offset, $value): void
    {
        $array = new CompositeKeyArray($array);

        $this->assertEquals($value, $array[$offset]);
    }

    public function arrayDataProviderForGetTesting(): array
    {
        return [
            [[1], 0, 1],
            [[1 => [2 => 3]], [1, 2], 3],

            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => 'bar'], ['foo'], 'bar'],
            [['foo' => ['bar' => 'baz']], ['foo', 'bar'], 'baz'],
        ];
    }

    public function testOffsetGetThrowsException(): void
    {
        $this->expectException(UndefinedOffsetException::class);

        $array = new CompositeKeyArray();

        $value = $array[['foo', 'bar']];
    }

    /**
     * @dataProvider arrayDataProviderForSetTesting
     */
    public function testOffsetSet($array, $offset, $value): void
    {
        $array = new CompositeKeyArray($array);
        $array[$offset] = $value;

        $this->assertEquals($value, $array[$offset]);
    }

    public function arrayDataProviderForSetTesting(): array
    {
        return [
            [[], 0, 1],
            [[], [0, 1], 2],
        ];
    }

    public function testOffsetSetEdgeCases(): void
    {
        $array = new CompositeKeyArray();
        $array[[[]]] = 'foo';

        $this->assertEquals('foo', $array[0]);

        $array = new CompositeKeyArray();
        $array[[1, [], 2]] = 3;

        $this->assertEquals(3, $array[[1, 0, 2]]);

        $array = new CompositeKeyArray();
        $array[[1, [], [], [], 2]] = 3;

        $this->assertEquals(3, $array[[1, 0, 0, 0, 2]]);
    }

    /**
     * @dataProvider arrayDataProviderForUnsetTesting
     */
    public function testOffsetUnset($array, $offset, $arrayAfterUnset): void
    {
        $array = new CompositeKeyArray($array);
        unset($array[$offset]);

        $this->assertEquals($arrayAfterUnset, $array->toArray());
    }

    public function arrayDataProviderForUnsetTesting(): array
    {
        return [
            [[1], 0, []],
            [[1], [0], []],
            [[1, 2], [0], [1 => 2]],

            [['foo' => 'bar'], 'foo', []],
            [['foo' => 'bar'], ['foo'], []],
            [['foo' => 'bar', 'baz' => 'quux'], ['foo'], ['baz' => 'quux']],
            
            [['foo' => ['bar' => 'baz']], ['foo', 'bar'], ['foo' => []]],
            [['foo' => ['bar' => ['baz']]], ['foo', 'bar', 0], ['foo' => ['bar' => []]]],
        ];
    }

    public function testItIsIterable(): void
    {
        $arr = new CompositeKeyArray($initial = [
            'foo' => ['bar' => ['baz']],
            'quux' => 'foobar',
        ]);

        foreach ($arr as $key => $value) {
            $this->assertEquals($initial[$key], $value);
        }

        $this->assertEquals($initial, $arr->toArray());
    }
}