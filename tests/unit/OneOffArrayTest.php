<?php

namespace Sevavietl\Arrays\Tests\Unit;

use Sevavietl\Arrays\OneOffArray;
use Sevavietl\Arrays\CompositeKeyArray;

class OneOffArrayTest extends \TestCase
{
    /**
     * @dataProvider arrayDataProviderForGetTesting
     */
    public function testOffsetGet($array, $offset, $value)
    {
        $array = new OneOffArray($array);

        $this->assertEquals(
            $value,
            $array[$offset]
        );

        $this->assertFalse(isset($array[$offset]));
    }

    public function arrayDataProviderForGetTesting()
    {
        return [
            [[1], 0, 1],
            [new CompositeKeyArray([1 => [2 => 3]]), ['1', '2'], 3],

            [['foo' => 'bar'], 'foo', 'bar'],
            [new CompositeKeyArray(['foo' => ['bar' => 'baz']]), ['foo', 'bar'], 'baz'],
        ];
    }

    public function testItIsIterable()
    {
        $arr = new OneOffArray($initial = [1, 2, 3]);

        foreach ($arr as $key => $value) {
            $this->assertEquals($initial[$key], $value);
        }

        $this->assertEmpty($arr->toArray());
    }
}