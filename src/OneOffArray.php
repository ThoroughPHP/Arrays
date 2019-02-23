<?php

namespace Sevavietl\Arrays;

class OneOffArray extends BaseArray
{
    /** @var int|string|null */
    private $key;

    public function offsetGet($offset)
    {
        $value = parent::offsetGet($offset);
        unset($this->container[$offset]);

        return $value;
    }

    public function next()
    {
        $this->iteration += 1;
        $this->key = \key($this->container);
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return 0 !== \count($this->container);
    }

    public function rewind()
    {
        $this->key = \key($this->container);
    }
}