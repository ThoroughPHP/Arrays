<?php

namespace Sevavietl\Arrays;

class BaseArray implements \ArrayAccess
{
    protected $container;
    protected $undefinedOffsetAction;

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

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }

        return $this->undefinedOffsetAction($this->container, $offset);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
}