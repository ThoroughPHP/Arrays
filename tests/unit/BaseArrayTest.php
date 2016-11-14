<?php

namespace Sevavietl\Arrays\Tests\Unit;

use Sevavietl\Arrays\BaseArray;

class BaseArrayTest extends \TestCase
{
    /**
     * @dataProvider instantiationDataProvider
     */
    public function testInstantiation($argument)
    {
        new BaseArray($argument);
    }

    public function instantiationDataProvider()
    {
        return [
            [[1, 2, 3]],
            [new \ArrayObject],
        ];
    }

    /**
     * @dataProvider instantiationThrowsExceptionDataProvider
     *
     * @expectedException InvalidArgumentException
     */
    public function testInstantiationThrowsException($argument)
    {
        new BaseArray($argument);
    }

    public function instantiationThrowsExceptionDataProvider()
    {
        return [
            [1],
            ['foo'],
            [new \StdClass],
        ];
    }
}