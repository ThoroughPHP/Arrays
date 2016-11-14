<?php

namespace Sevavietl\Arrays;

class OneOffArray extends BaseArray
{
    public function offsetGet($offset)
    {
        $value = $this->container[$offset];
        unset($this->container[$offset]);

        return $value;
    }
}