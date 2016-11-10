<?php

namespace Sevavietl\Arrays;

class CompositeKeyArray implements \ArrayAccess
{
    protected $array;
    protected $undefinedOffsetAction;

    public function __construct(array $array = [])
    {
        $this->array = $array;

        $this->undefinedOffsetAction = function ($offset) {
            throw new UndefinedOffsetException(
                "Undefined offset $offset."
            );
        };
    }

    public function setUndefinedOffsetAction(Callable $undefinedOffsetAction)
    {
        $this->undefinedOffsetAction = $undefinedOffsetAction;

        return $this;
    }

    public function toArray()
    {
        return $this->array;
    }
    
    protected $offsets;

    public function offsetExists($offset)
    {
        $this->setOffsets($offset);

        return $this->walkThroughOffsets(
            $this->array,
            function ($array, $offset) {
                return isset($array[$offset]);
            },
            function () {
                return false;
            }
        );
    }

    public function offsetGet($offset)
    {
        $this->setOffsets($offset);

        return $this->walkThroughOffsets(
            $this->array,
            function &($array, $offset) {
                return $array[$offset];
            },
            $this->undefinedOffsetAction
        );
    }

    public function offsetSet($offset, $value)
    {
        $this->value = $value;

        $this->setOffsets($offset);

        $baseCaseAction = function (&$array, $offset) {
            $array[$offset] = $this->value;
        };

        $offsetNotExistsAction = function (&$array, $offset) use (
            $baseCaseAction,
            &$offsetNotExistsAction
        ) {
            $value = empty($this->offsets) ? $this->value : [];

            if (!empty($offset)) {
                $array[$offset] = $value;
            } else {
                $array[] = $value;
            }

            if (empty($this->offsets)) {
                return;
            }

            return $this->walkThroughOffsets(
                $array,
                $baseCaseAction,
                $offsetNotExistsAction
            );
        };

        return $this->walkThroughOffsets(
            $this->array,
            $baseCaseAction,
            $offsetNotExistsAction
        );
    }

    public function offsetUnset($offset)
    {
        $this->setOffsets($offset);

        return $this->walkThroughOffsets(
            $this->array,
            function (&$array, $offset) {
                unset($array[$offset]);
            },
            $this->undefinedOffsetAction
        );
    }

    protected function setOffsets($offsets)
    {
        $this->offsets = is_array($offsets) ? $offsets : [$offsets];
    }

    protected function walkThroughOffsets(
        &$array,
        Callable $baseCaseAction,
        Callable $offsetNotExistsAction
    ) {
        $offset = array_shift($this->offsets);

        if (isset($array[$offset])) {
            if (empty($this->offsets)) {
                return $baseCaseAction($array, $offset);
            }

            return $this->walkThroughOffsets(
                $array[$offset],
                $baseCaseAction,
                $offsetNotExistsAction
            );
        }

        return $offsetNotExistsAction($array, $offset);
    }
}