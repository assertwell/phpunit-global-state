# Managing Methods

When writing tests, we often make use of [test doubles](https://en.wikipedia.org/wiki/Test_double) to better control how our code will behave. For instance, we don't want to make calls to remote APIs every time we run our tests, as these dependencies can make our tests fragile and slow.

If your software is written using proper [Dependency Injection](https://phptherightway.com/#dependency_injection), it's usually pretty easy to [create test doubles with PHPUnit](https://jmauerhan.wordpress.com/2018/10/04/the-5-types-of-test-doubles-and-how-to-create-them-in-phpunit/) and inject them into the objects we create in our tests.

What happens when the software we're working with isn't coded so nicely, though?

Most of the time, we can get around this using [Reflection](https://www.php.net/intro.reflection), but _sometimes_ we need a sledgehammer to break through. That's where the `AssertWell\PHPUnitGlobalState\Methods` trait (powered by [PHP's runkit7 extension](Runkit.md)) comes in handy.


## Methods

As all of these methods require [runkit7](Runkit.md), tests that use these methods will automatically be marked as skipped if the extension is unavailable.

---

### defineMethod()

Define a new method for the duration of the test.

`defineMethod(string $class, string $name, \Closure $closure, string $visibility = 'public', bool $static = false): self`

This is a wrapper around [PHP's `runkit7_method_define()` function](https://www.php.net/manual/en/function.runkit7-method-define.php).

#### Parameters

<dl>
    <dt>$class</dt>
    <dd>The class name.</dd>
    <dt>$name</dt>
    <dd>The method name.</dd>
    <dt>$closure</dt>
    <dd>The code for the method.</dd>
    <dt>$visibility</dt>
    <dd>Optional. The method visibility, one of "public", "protected", or "private".</dd>
    <dt>$static</dt>
    <dd>Optional. Whether or not the method should be static. Default is false.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

An `AssertWell\PHPUnitGlobalState\Exceptions\MethodExistsException` exception will be thrown if the given `$method` already exists. An `AssertWell\PHPUnitGlobalState\Exceptions\RunkitException` will be thrown if the given method cannot be defined.

---

### redefineMethod()

Redefine an existing method for the duration of the test. If `$name` does not exist, it will be defined.

`redefineMethod(string $class, string $name, ?\Closure $closure, ?string $visibility = null, ?bool $static = null): self`

This is a wrapper around [PHP's `runkit7_method_redefine()` function](https://www.php.net/manual/en/function.runkit7-method-redefine.php).

#### Parameters

<dl>
    <dt>$class</dt>
    <dd>The class name.</dd>
    <dt>$name</dt>
    <dd>The method name.</dd>
    <dt>$closure</dt>
    <dd>The new code for the method.</dd>
    <dd>If <code>null</code> is passed, the existing method body will be copied.</dd>
    <dt>$visibility</dt>
    <dd>Optional. The method visibility, one of "public", "protected", or "private".</dd>
    <dd>If <code>null</code> is passed, the existing visibility will be preserved.</dd>
    <dt>$static</dt>
    <dd>Optional. Whether or not the method should be static. Default is false.</dd>
    <dd>If <code>null</code> is passed, the existing state will be used.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

An `AssertWell\PHPUnitGlobalState\Exceptions\RunkitException` will be thrown if the given method cannot be (re)defined.

---

### deleteMethod()

Delete/undefine a method for the duration of the single test.

`deleteMethod(string $class, string $name): self`

#### Parameters

<dl>
    <dt>$class</dt>
    <dd>The class name.</dd>
    <dt>$name</dt>
    <dd>The method name.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.
