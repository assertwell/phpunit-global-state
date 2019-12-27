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

## Available Traits

The library offers a number of traits, based on the type of global state that might need to be manipulated.

* [Environment Variables](docs/EnvironmentVariables.md)
