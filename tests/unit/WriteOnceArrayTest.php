<?php

namespace ThoroughPHP\Arrays\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ThoroughPHP\Arrays\IllegalOffsetException;
use ThoroughPHP\Arrays\IllegalOffsetUnsetMethodCallException;
use ThoroughPHP\Arrays\WriteOnceArray;

final class WriteOnceArrayTest extends TestCase
{
    public function testOffsetSet(): void
    {
        $this->expectException(IllegalOffsetException::class);

        $array = new WriteOnceArray();

        $array['foo'] = 'bar';
        $array['foo'] = 'baz';
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(IllegalOffsetUnsetMethodCallException::class);

        $array = new WriteOnceArray([
            'foo' => 'bar',
        ]);

        unset($array['foo']);
    }
}