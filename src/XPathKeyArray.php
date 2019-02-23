<?php

namespace ThoroughPHP\Arrays;

class XPathKeyArray extends SeparatedKeyArray
{
    protected function getSeparator()
    {
        return '/';
    }
}