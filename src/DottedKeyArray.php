<?php

namespace Sevavietl\Arrays;

class DottedKeyArray extends SeparatedKeyArray
{
    protected function getSeparator()
    {
        return '.';
    }
}