<?php

namespace ThoroughPHP\Arrays;

class DottedKeyArray extends SeparatedKeyArray
{
    protected function getSeparator()
    {
        return '.';
    }
}