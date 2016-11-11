# Array Extensions

This is a collection of array wrappers.

## Composite Key Array

Sometimes it is useful to have ability to access nested value with one,
array like, key (some questions were asked about this: [on quora](https://www.quora.com/Learning-PHP-Is-there-a-way-to-get-the-value-of-multi-dimensional-array-by-specifying-the-key-with-a-variable), [on stackoverflow](http://stackoverflow.com/questions/22614817/get-a-value-from-a-multidimensional-array-using-the-dot-syntax)).

`CompositeKeyArray` class gives you the ability to do all basic array operations using array-like (nested) key.
It implemets [`ArrayAccess`](http://php.net/manual/en/class.arrayaccess.php) interface:

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