<?php

namespace ThoroughPHP\Arrays;

abstract class SeparatedKeyArray extends CompositeKeyArray
{
    protected function setOffsets($offsets)
    {
        if ($this->notIntegerOrString($offsets)) {
            throw new InvalidOffsetTypeException("Invalid offset type: " . gettype($offsets) . ".");
        }

        parent::setOffsets(array_map(function ($offset) {
            return $offset === '[]' ? [] : $offset;
        }, explode($this->getSeparator(), $offsets)));
    }

    protected function notIntegerOrString($value)
    {
        return !$this->integerOrString($value);
    }

    protected function integerOrString($value)
    {
        return is_integer($value) || is_string($value);
    }

    abstract protected function getSeparator();
}