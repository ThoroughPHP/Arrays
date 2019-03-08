<?php

namespace ThoroughPHP\Arrays;

final class WriteOnceArray extends BaseArray
{
    public function offsetSet($offset, $value): void
    {
        if ($this->offsetExists($offset)) {
            throw new IllegalOffsetException("Offset {$offset} was already written");
        }

        parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        throw new IllegalOffsetUnsetMethodCallException("Cannot unset offset {$offset}. Array is write-once");
    }
}