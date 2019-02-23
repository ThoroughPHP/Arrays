<?php

namespace Sevavietl\Arrays\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sevavietl\Arrays\DottedKeyArray;
use Sevavietl\Arrays\UndefinedOffsetException;
use Sevavietl\Arrays\InvalidOffsetTypeException;

class DottedKeyArrayTest extends TestCase
{
    /**
     * @dataProvider arrayDataProviderForIssetTesting
     */
    public function testOffsetExists($array, $offset, $exists): void
    {
        $array = new DottedKeyArray($array);

        $this->assertEquals($exists, isset($array[$offset]));
    }

    public function arrayDataProviderForIssetTesting(): array
    {
        return [
            [[1], 0, true],
            [[1], 1, false],
            [[1], '', false],

            [['foo' => 'bar'], 'foo', true],
            [['foo' => 'bar'], 'bar', false],

            [['foo' => ['bar' => 'baz']], 'foo.bar', true],
            [['foo' => ['bar' => 'baz']], 'foo.baz', false],
        ];  
    }

    /**
     * @dataProvider arrayDataProviderForGetTesting
     */
    public function testOffsetGet($array, $offset, $value): void
    {
        $array = new DottedKeyArray($array);

        $this->assertEquals($value, $array[$offset]);
    }

    public function arrayDataProviderForGetTesting(): array
    {
        return [
            [[1], 0, 1],
            [[1 => [2 => 3]], '1.2', 3],

            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => ['bar' => 'baz']], 'foo.bar', 'baz'],
        ];
    }

    public function testOffsetGetThrowsUndefinedOffsetException(): void
    {
        $this->expectException(UndefinedOffsetException::class);

        $array = new DottedKeyArray();

        $value = $array['foo.bar'];
    }

    public function testOffsetGetThrowsInvalidOffsetTypeException(): void
    {
        $this->expectException(InvalidOffsetTypeException::class);

        $array = new DottedKeyArray();

        $value = $array[['foo', 'bar']];
    }

    /**
     * @dataProvider arrayDataProviderForSetTesting
     */
    public function testOffsetSet($array, $offset, $value): void
    {
        $array = new DottedKeyArray($array);

        $array[$offset] = $value;

        $this->assertEquals($value, $array[$offset]);
    }

    public function arrayDataProviderForSetTesting(): array
    {
        return [
            [[], 0, 1],
            [[], '0.1', 2],
        ];
    }

    public function testOffsetSetEdgeCases(): void
    {
        $array = new DottedKeyArray();
        $array['[]'] = 'foo';

        $this->assertEquals('foo', $array[0]);

        $array = new DottedKeyArray();
        $array['1.[].2'] = 3;

        $this->assertEquals(3, $array['1.0.2']);

        $array = new DottedKeyArray();
        $array['1.[].[].[].2'] = 3;

        $this->assertEquals(3, $array['1.0.0.0.2']);
    }

    /**
     * @dataProvider arrayDataProviderForUnsetTesting
     */
    public function testOffsetUnset($array, $offset, $arrayAfterUnset): void
    {
        $array = new DottedKeyArray($array);

        unset($array[$offset]);

        $this->assertEquals($arrayAfterUnset, $array->toArray());
    }

    public function arrayDataProviderForUnsetTesting(): array
    {
        return [
            [[1], 0, []],
            [[1, 2], 0, [1 => 2]],

            [['foo' => 'bar'], 'foo', []],
            [['foo' => 'bar', 'baz' => 'quux'], 'foo', ['baz' => 'quux']],
            
            [['foo' => ['bar' => 'baz']], 'foo.bar', ['foo' => []]],
            [['foo' => ['bar' => ['baz']]], 'foo.bar.0', ['foo' => ['bar' => []]]],
        ];
    }
}