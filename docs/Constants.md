# Managing Constants

Some applications — especially WordPress — will use [PHP constants](https://www.php.net/manual/en/language.constants.php) for configuration that should not be edited directly through the <abbr title="User Interface">UI</abbr>.

Normally, a constant cannot be redefined or removed once defined; however, [the runkit7 extension](https://www.php.net/manual/en/book.runkit7) exposes functions to modify normally immutable constructs.

If runkit functions are unavailable, the `Constants` trait will automatically skip tests that rely on this functionality.

In order to install runkit7 in your development and CI environments, you may use [the installer bundled with this repo](https://github.com/stevegrunwell/runkit7-installer):

```sh
$ sudo ./vendor/bin/install-runkit.sh
```

## Methods

### setConstant()

Define (or re-define) a constant for the duration of the test.

`setConstant(string $key[, mixed $value = null]): self`

This is a wrapper around [PHP's `runkit_constant_redefine()` function](https://www.php.net/manual/en/function.runkit-constant-redefine.php).

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The constant name.</dd>
    <dt>$value</dt>
    <dd>The scalar value to set for the constant. Default is <code>null</code></dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

An `AssertWell\PHPUnitGlobalState\Exceptions\RedefineException` will be thrown if the given constant cannot be (re-)defined.

---

### deleteConstant()

Delete a defined constant for a single test.

`deleteConstant(string $key): self`

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The constant name.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.


## Examples

### Overriding a constant for a single test

WordPress enables site owners to enable or disable its debug mode based on a boolean `WP_DEBUG` constant. Many plugins may find themselves altering their behavior based on the value of this constant, but this is typically difficult to test.

Imagine we're working on a WordPress plugin that has a `debug_comment(string $message)` template function that will print an HTML comment *only if* `WP_DEBUG` is true.

```php
use AssertWell\PHPUnitGlobalState\Constants;
use WP_UnitTestCase;

class MyTestClass extends WP_UnitTestCase
{
    use Constants;

    /**
     * @test
     */
    public function a_debug_comment_should_be_shown_if_WP_DEBUG_is_true()
    {
        $this->setConstant('WP_DEBUG', true);

        $this->assertStringContainsString('Test comment', debug_comment('Test comment'));
    }

    /**
     * @test
     */
    public function a_debug_comment_should_not_be_shown_if_WP_DEBUG_is_false()
    {
        $this->setConstant('WP_DEBUG', false);

        $this->assertEmpty(debug_comment('Test comment'));
    }
}
```

Regardless of whether or not these tests pass, the `WP_DEBUG` constant will be restored to its initial value after each test method completes.
