<?php

namespace Sevavietl\Arrays\Tests\Unit;

use Sevavietl\Arrays\XPathKeyArray;
use Sevavietl\Arrays\UndefinedOffsetException;
use Sevavietl\Arrays\InvalidOffsetTypeException;

class XPathKeyArrayTest extends \TestCase
{
    /**
     * @dataProvider arrayDataProviderForIssetTesting
     */
    public function testOffsetExists($array, $offset, $exists)
    {
        $array = new XPathKeyArray($array);

        $this->assertEquals(
            $exists,
            isset($array[$offset])
        );
    }

    public function arrayDataProviderForIssetTesting()
    {
        return [
            [[1], 0, true],
            [[1], 1, false],
            [[1], '', false],

            [['foo' => 'bar'], 'foo', true],
            [['foo' => 'bar'], 'bar', false],

            [['foo' => ['bar' => 'baz']], 'foo/bar', true],
            [['foo' => ['bar' => 'baz']], 'foo/baz', false],
        ];  
    }

    /**
     * @dataProvider arrayDataProviderForGetTesting
     */
    public function testOffsetGet($array, $offset, $value)
    {
        $array = new XPathKeyArray($array);

        $this->assertEquals(
            $value,
            $array[$offset]
        );
    }

    public function arrayDataProviderForGetTesting()
    {
        return [
            [[1], 0, 1],
            [[1 => [2 => 3]], '1/2', 3],

            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => ['bar' => 'baz']], 'foo/bar', 'baz'],
        ];
    }

    /**
     * @expectedException Sevavietl\Arrays\UndefinedOffsetException
     */
    public function testOffsetGetThrowsUndefinedOffsetException()
    {
        $array = new XPathKeyArray();

        $value = $array['foo/bar'];
    }

    /**
     * @expectedException Sevavietl\Arrays\InvalidOffsetTypeException
     */
    public function testOffsetGetThrowsInvalidOffsetTypeException()
    {
        $array = new XPathKeyArray();

        $value = $array[['foo', 'bar']];
    }

    /**
     * @dataProvider arrayDataProviderForSetTesting
     */
    public function testOffsetSet($array, $offset, $value)
    {
        $array = new XPathKeyArray($array);

        $array[$offset] = $value;

        $this->assertEquals(
            $value,
            $array[$offset]
        );
    }

    public function arrayDataProviderForSetTesting()
    {
        return [
            [[], 0, 1],
            [[], '0/1', 2],
        ];
    }

    public function testOffsetSetEdgeCases()
    {
        $array = new XPathKeyArray();

        $array['[]'] = 'foo';

        $this->assertEquals(
            'foo',
            $array[0]
        );

        $array = new XPathKeyArray();

        $array['1/[]/2'] = 3;

        $this->assertEquals(
            3,
            $array['1/0/2']
        );

        $array = new XPathKeyArray();

        $array['1/[]/[]/[]/2'] = 3;

        $this->assertEquals(
            3,
            $array['1/0/0/0/2']
        );
    }

    /**
     * @dataProvider arrayDataProviderForUnsetTesting
     */
    public function testOffsetUnset($array, $offset, $arrayAfterUnset)
    {
        $array = new XPathKeyArray($array);

        unset($array[$offset]);

        $this->assertEquals(
            $arrayAfterUnset,
            $array->toArray()
        );
    }

    public function arrayDataProviderForUnsetTesting()
    {
        return [
            [[1], 0, []],
            [[1, 2], 0, [1 => 2]],

            [['foo' => 'bar'], 'foo', []],
            [['foo' => 'bar', 'baz' => 'quux'], 'foo', ['baz' => 'quux']],
            
            [['foo' => ['bar' => 'baz']], 'foo/bar', ['foo' => []]],
            [['foo' => ['bar' => ['baz']]], 'foo/bar/0', ['foo' => ['bar' => []]]],
        ];
    }
}