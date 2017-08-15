<?php

namespace Sevavietl\Arrays;

class OneOffArray extends BaseArray
{
    public function offsetGet($offset)
    {
        $value = parent::offsetGet($offset);
        unset($this->container[$offset]);

        return $value;
    }
}