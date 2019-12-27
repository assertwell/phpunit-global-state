# Managing Environment Variables

In situations where apps behave differently based on environment variables (e.g. [`getenv()`](https://www.php.net/manual/en/function.getenv) and [`putenv()`](https://www.php.net/manual/en/function.getenv)), the `EnvironmentVariables` trait creates fixtures to manipulate these variables while resetting them between tests.

## Methods

### setEnv

Set or update an environment variable for the duration of the test.

`setEnv(string $key[, mixed $value = null]): bool`

This is a wrapper around [PHP's `putenv()` function](https://www.php.net/manual/en/function.putenv).

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The environment variable name.</dd>
    <dt>$value</dt>
    <dd>The value to set for the environment variable. Default is <code>null</code></dd>
    <dd>Passing <code>false</code> to <code>$value</code> will remove the environment variable.</dd>
</dl>

#### Return values

This method will return `true` if the environment variable was set/updated successfully, `false` otherwise.

### deleteEnv

Delete/unset an environment variable for a single test.

`deleteEnv(string $key): bool`

This is equivalent to calling [`$this->setEnv($key, false)`](#putenv).

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The environment variable name.</dd>
</dl>

#### Return values

This method will return `true` if the environment variable was removed successfully, `false` otherwise.


### getEnvironmentVariables

Retrieve all environment variables that have been (re-)defined or deleted within this test.

`getEnvironmentVariables(): array`

#### Return values

This method will return an array with any environment variables that have been modified via [`setEnv()`](#putenv) or [`deleteEnv()`](#deleteenv).

The returned array will be in the form of `{variable name} => {initial value}`.

If no environment variables have been modified, this method will return an empty array.


## Examples

### Setting an environment variable in a test

Imagine we have a function, `greet_user()`, which tries to read the user's name from the `username` environment variable:

```php
use AssertWell\PHPUnitGlobalState\EnvironmentVariables;
use PHPUnit\Framework\TestCase;

class MyTestClass extends TestCase
{
    use EnvironmentVariables;

    /**
     * @test
     */
    public function greetings_should_include_the_username_env_var()
    {
        $this->setEnv('username', 'Test McTest');

        $this->assertSame('Hello, Test McTest!', greet_user());
    }
}
```

Regardless of whether or not `greetings_should_include_the_username_env_var()` passes, the `username` environment variable will be reset after the test method completes.
