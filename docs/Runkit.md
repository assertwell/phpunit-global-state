# PHP's runkit and runkit7 extensions

> For all those things you&hellip; probably shouldn't have been doing anyway&hellip; but surely do!

In the PHP 5.x days, we had [the runkit extension](http://pecl.php.net/package/runkit) for dynamically redefining things that _shouldn't_ normally be redefined within the PHP runtime.

For example, if you needed to change the value of a constant, your options were slim-to-nil before runkit came along. With the extension installed, however, you could now redefine that which was never meant to be redefined.

With the release of PHP 7.x, [Tyson Andre](https://github.com/TysonAndre) forked runkit to create [runkit7](https://github.com/runkit7/runkit7), a PHP 7.x-compatible version of the extension.


## You really shouldn't be using runkit&hellip;

The runkit(7) extension is an immensely-powerful tool, but if you're not careful it can be the source of a lot of pain within your codebase.

Generally speaking, **if you're using runkit in production code, you're probably approaching the problem in the wrong way.**

That being said, runkit can be _amazing_ for automated tests, as we can dynamically change configurations and behaviors to emulate certain situations. If you're testing older or poorly-architected applications, runkit can mean the difference between a comprehensive test suite and one that leaves a lot of paths uncovered.

Using runkit should probably never be your first approach, but in certain situations it's by-far the cleanest.

Remember: **with great power comes great responsibility!!**


## Installation

Both runkit and runkit7 can be installed in your environment via [PECL](https://pecl.php.net/):

```sh
# For PHP 5.x
pecl install runkit

# For PHP 7.x
pecl install runkit7
```

Depending on your environment, you may also need to take additional steps to load the extension after installation, which will be detailed in the shell output from `pecl install`.

If you'd like to automate this process further, you may also consider [installing stevegrunwell/runkit7-installer](https://github.com/stevegrunwell/runkit7-installer#installation) as a Composer dependency in your project.


## Using runkit and runkit7 in the same test suite

More recent versions of runkit7 have introduced `runkit7_`-prefixed functions, and their `runkit_` counterparts are aliased to the newer versions.

For example, `runkit_function_redefine()` is an alias for `runkit7_function_redefine()`.

However, static code analysis tools like [PHPStan](https://phpstan.org/) will often throw warnings about the `runkit_` versions of the functions being undefined, and the corresponding pages are being removed from [php.net](https://php.net).

To get around these issues, this library includes the `AssertWell\PHPUnitGlobalState\Support\Runkit` class, which proxies static method calls to runkit based what's available:

```php
use AssertWell\PHPUnitGlobalState\Support\Runkit;

/*
 * Use runkit7_constant_redefine() on PHP 7.x,
 * runkit_constant_redefine() for PHP 5.x.
 */
Runkit::constant_redefine('SOME_CONSTANT', 'some value');
```
