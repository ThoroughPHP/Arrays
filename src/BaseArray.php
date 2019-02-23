<?php

namespace ThoroughPHP\Arrays;

class BaseArray implements \ArrayAccess, \Iterator
{
    protected $container;
    protected $undefinedOffsetAction;

    protected $iteration = 0;

    public function __construct($array = [])
    {
        $this->container = $array;

        $this->validateContainerArrayOrArrayAccessObject();

        $this->setUndefinedOffsetAction(function ($array, $offset) {
            $offset = json_encode($offset);

            throw new UndefinedOffsetException(
                "Undefined offset $offset."
            );
        });
    }

    protected function validateContainerArrayOrArrayAccessObject()
    {
        if (is_array($this->container)) {
            return;
        }

        if (
            is_object($this->container)
            && $this->container instanceof \ArrayAccess
        ) {
            return;
        }

        throw new \InvalidArgumentException;
    }

    public function setUndefinedOffsetAction(callable $callback)
    {
        $this->undefinedOffsetAction = $callback;

        return $this;
    }

    public function toArray()
    {
        return $this->container;
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }

        $undefinedOffsetAction = $this->undefinedOffsetAction;

        return $undefinedOffsetAction($this->container, $offset);
    }

    public function offsetSet($offset, $value) {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function current()
    {
        return $this->offsetGet($this->key());
    }

    public function next()
    {
        $this->iteration += 1;
        next($this->container);
    }

    public function key()
    {
        return \key($this->container);
    }

    public function valid()
    {
        return $this->iteration < \count($this->container);
    }

    public function rewind()
    {
        $this->iteration = 0;
        reset($this->container);
    }
}