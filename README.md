# Array Extensions

[![Build Status](https://travis-ci.com/ThoroughPHP/Arrays.svg?branch=master)](https://travis-ci.com/ThoroughPHP/Arrays)
[![Coverage Status](https://coveralls.io/repos/github/ThoroughPHP/Arrays/badge.svg)](https://coveralls.io/github/ThoroughPHP/Arrays)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

This is a collection of array wrappers.

Table of Contents
=================  
* [Composite Key Array](#composite-key-array)  
    - [1. offsetExists](#composite-key-array-offset-exists)
    - [2. offsetGet](#composite-key-array-offset-get)
    - [3. offsetSet](#composite-key-array-offset-set)
    - [4. offsetUnset](#composite-key-array-offset-unset)
* [XPath Key Array](#xpath-key-array)
* [Dotted Key Array](#dotted-key-array)
* [One-off Array](#one-off-array)

<a name="composite-key-array"></a>

## Composite Key Array

Sometimes it is useful to have ability to access nested value with one,
array like, key (some questions were asked about this: [on quora](https://www.quora.com/Learning-PHP-Is-there-a-way-to-get-the-value-of-multi-dimensional-array-by-specifying-the-key-with-a-variable), [on stackoverflow](http://stackoverflow.com/questions/22614817/get-a-value-from-a-multidimensional-array-using-the-dot-syntax)).

`CompositeKeyArray` class gives you the ability to do all basic array operations using array-like (nested) key.
It implemets [`ArrayAccess`](http://php.net/manual/en/class.arrayaccess.php) interface:

<a name="composite-key-array-offset-exists"></a>

### 1. offsetExists:

You can check nested keys for existance.

```php
    $array = new CompositeKeyArray([
        'foo' => [
            'bar' => 'baz'
        ]
    ]);

    var_dump(isset($array[['foo', 'bar']])); // => bool(true)
    var_dump(isset($array[['foo', 'quux']])); // => bool(false)
```

<a name="composite-key-array-offset-get"></a>

### 2. offsetGet:

You can get value by nested key. If nested key is not set the `UndefinedOffsetException` will be thrown.

```php
    $array = new CompositeKeyArray([
        'foo' => [
            'bar' => 'baz'
        ]
    ]);

    var_dump($array[['foo', 'bar']]); // => string(3) "baz"
    var_dump($array[['foo', 'quux']]); // => PHP Fatal error:  Uncaught UndefinedOffsetException: Undefined offset quux.
```

<a name="composite-key-array-offset-set"></a>

### 3. offsetSet:

You can set value for nested key.

```php
    $array = new CompositeKeyArray();

    $array[['foo', 'bar']] = 'baz';

    var_dump($array[['foo', 'bar']]); // => string(3) "baz"
```

There is one pitfall. When you try to do `$array['foo']['bar'] = 'baz'` you get `Indirect modification of overloaded element of CompositeKeyArray has no effect`.
The reason was explained [here](http://stackoverflow.com/questions/20053269/indirect-modification-of-overloaded-element-of-splfixedarray-has-no-effect). So in order to achive the desired result you have to do the following:

```php
    $array = new CompositeKeyArray([
        'foo' => []
    ]);

    $array['foo']['bar'] = 'baz'; // => PHP Notice:  Indirect modification of overloaded element of CompositeKeyArray has no effect

    var_dump($array['foo']); // => array(0) {}

    $array[['foo', 'bar']] = 'baz';

    var_dump($array['foo']); // => array(1) {["bar"] => string(3) "baz"}
```

But there is another edge case left: when you need to append element at the end of an array.

```php
    $array = new CompositeKeyArray([
        'foo' => []
    ]);

    $array[[[]]] = 'bar';
    $array[['foo', []]] = 'baz';
    $array[['foo', []]] = 'qux';

    var_dump($array->toArray());

    // => array(2) {
    //    ["foo"]=>
    //        array(2) {
    //            [0]=>
    //                string(3) "baz"
    //            [1]=>
    //                string(3) "qux"
    //        }
    //    [0]=>
    //        string(3) "bar"
    // }

```

<a name="composite-key-array-offset-unset"></a>

### 4. offsetUnset:

You can unset nested key.

```php
    $array = new CompositeKeyArray([
        'foo' => [
            'bar' => 'baz'
        ]
    ]);

    unset($array[['foo', 'bar']]);

    var_dump($array['foo']); // => array(0) {}
```

After nested manipulations you might want to get back the **real array**. This can be done by calling `$array->toArray()`.

<a name="xpath-key-array"></a>

## XPath Key Array

**This is not a real xpath!** This class instead of array-like key users string of keys delimited with `/`.

```php
    $array = new XPathKeyArray([
        'foo' => [
            'bar' => 'baz'
        ]
    ]);

    var_dump($array['foo/bar']); // => string(3) "baz"
```

This one was inspired by [an old article](http://codeaid.net/php/get-values-of-multi-dimensional-arrays-using-xpath-notation).

Compared to `CompositeKeyArray`, `XPathKeyArray` has some limitations:

1. You cannot use keys with `/` in them.
2. You cannot use `null` as key.

<a name="dotted-key-array"></a>

## Dotted Key Array

This class instead of array-like key users string of keys delimited with `.`.

```php
    $array = new DottedKeyArray([
        'foo' => [
            'bar' => 'baz'
        ]
    ]);

    var_dump($array['foo.bar']); // => string(3) "baz"
```

Compared to `CompositeKeyArray`, `DottedKeyArray` has some limitations:

1. You cannot use keys with `.` in them.
2. You cannot use `null` as key.

<a name="one-off-array"></a>

## One-off Array

Sometimes you want to get value from an array by key and `unset` this key after that. The `OneOffArray` class helps you with this.

Again this class can be used in combination with `CompositeKeyArray` or its descendents: `XPathKeyArray` or `DottedKeyArray`.
Actually, it can be used in combination with any object that implemets `ArrayAccess`.