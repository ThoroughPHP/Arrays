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
}