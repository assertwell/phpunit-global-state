# PHPUnit Global State

This library provides a number of traits meant to help test applications that depend on global state.

## Installation

You may install the package [via Composer](https://getcomposer.org):

```sh
$ composer require --dev assertwell/phpunit-global-state
```

Once installed, add the desired traits into your PHPUnit test classes:

```php
<?php

namespace Tests;

use AssertWell\PHPUnitGlobalState\EnvironmentVariables;
use PHPUnit\Framework\TestCase;

class MyTestClass extends TestCase
{
    use EnvironmentVariables;

    // Your test logic goes here.
}
```

### Introduction to Runkit

Some of the traits will rely on [Runkit7](https://www.php.net/runkit7), a port of PHP's runkit designed to work in PHP 7.x, to rewrite code at runtime (a.k.a. "monkey-patching").

For example, once a PHP constant is defined, it will normally have that value until the PHP process ends. Under normal circumstances, that's great: it prevents the value from being accidentally overwritten and/or tampered with.

When it comes to testing, however, constants can become a bit of a pain; since PHPUnit will run multiple tests in the same process, a constant defined in an earlier test may cause unintended side-effects in a later test.

In some circumstances, this can be mitigated by [telling PHPUnit to run a given test in a separate process](https://phpunit.readthedocs.io/en/9.2/annotations.html#runtestsinseparateprocesses), but some applications (WordPress comes to mind) break in pretty spectacular ways when run in isolation.

Runkit lets us get around this by redefining constants, functions, classes, et al at runtime:

```php
define('SOME_CONSTANT', 'some value');
var_dump(SOME_CONSTANT)
#=> string(10) "some value"

// Now, re-define the constant.
runkit_constant_redefine('SOME_CONSTANT', 'some other value');
var_dump(SOME_CONSTANT)
#=> string(16) "some other value"
```

Of course, we might want a constant's original value to be restored after our test that redefined it, so that's where PHPUnit Global State comes in: the traits defined in PHPUnit Global State are designed to make sure those changes are automatically reset for you at the conclusion of each test.

## Available Traits

The library offers a number of traits, based on the type of global state that might need to be manipulated.

* [Constants](docs/Constants.md) (requires Runkit7)
* [Environment Variables](docs/EnvironmentVariables.md)
* [Global Variables](docs/GlobalVariables.md)
