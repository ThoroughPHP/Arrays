<?php

namespace Sevavietl\Arrays;

class XPathKeyArray extends SeparatedKeyArray
{
    protected function getSeparator()
    {
        return '/';
    }
}