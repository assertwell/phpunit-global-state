# Managing Environment Variables

In situations where apps behave differently based on environment variables (e.g. [`getenv()`](https://www.php.net/manual/en/function.getenv) and [`putenv()`](https://www.php.net/manual/en/function.getenv)), the `EnvironmentVariables` trait creates fixtures to manipulate these variables while resetting them between tests.

## Methods

### setEnvironmentVariable()

Set or update an environment variable for the duration of the test.

`setEnvironmentVariable(string $key[, mixed $value = null]): self`

This is a wrapper around [PHP's `putenv()` function](https://www.php.net/manual/en/function.putenv).

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The environment variable name.</dd>
    <dt>$value</dt>
    <dd>The value to set for the environment variable. Default is <code>null</code></dd>
    <dd>Passing <code>NULL</code> to <code>$value</code> will remove the environment variable.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

---

### deleteEnvironmentVariable()

Delete/unset an environment variable for a single test.

`deleteEnvironmentVariable(string $key): bool`

This is equivalent to calling [`$this->setEnvironmentVariable($key, null)`](#setenvironmentvariable).

#### Parameters

<dl>
    <dt>$key</dt>
    <dd>The environment variable name.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.


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
        $this->setEnvironmentVariable('username', 'Test McTest');

        $this->assertSame('Hello, Test McTest!', greet_user());
    }
}
```

Regardless of whether or not `greetings_should_include_the_username_env_var()` passes, the `username` environment variable will be reset after the test method completes.
