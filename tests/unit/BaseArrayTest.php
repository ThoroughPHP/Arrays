<?php

namespace Sevavietl\Arrays\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sevavietl\Arrays\BaseArray;
use Sevavietl\Arrays\UndefinedOffsetException;

class BaseArrayTest extends TestCase
{
    /**
     * @dataProvider instantiationDataProvider
     */
    public function testInstantiation($argument): void
    {
        new BaseArray($argument);
    }

    public function instantiationDataProvider(): array
    {
        return [
            [[1, 2, 3]],
            [new \ArrayObject],
        ];
    }

    /**
     * @dataProvider instantiationThrowsExceptionDataProvider
     */
    public function testInstantiationThrowsException($argument): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new BaseArray($argument);
    }

    public function instantiationThrowsExceptionDataProvider(): array
    {
        return [
            [1],
            ['foo'],
            [new \stdClass],
        ];
    }

    public function testThrowsExceptionOnUndefinedOffset(): void
    {
        $this->expectException(UndefinedOffsetException::class);

        $arr = new BaseArray([1, 2, 3]);
        $arr[3];
    }

    public function testItIsIterable(): void
    {
        $arr = new BaseArray($initial = [1, 2, 3]);

        foreach ($arr as $key => $value) {
            $this->assertEquals($initial[$key], $value);
        }

        $this->assertEquals($initial, $arr->toArray());
    }
}