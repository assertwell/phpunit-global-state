# Managing Functions

When testing software, we often find ourselves making use of "stubs", which are objects that will return known values for given methods.

For example, assume we're writing an integration test around how a feature behaves when an external API is unavailable, it's certainly easier to replace the HTTP response than to actually take down the API every time the test is run.

Unfortunately, [PHPUnit's test double tools](https://phpunit.readthedocs.io/en/9.3/test-doubles.html) don't extend to functions, so we have to get creative. Fortunately, [PHP's runkit7 extension](Runkit.md), allows us to dynamically redefine functions at runtime.


## Methods

As all of these methods require [runkit7](Runkit.md), tests that use these methods will automatically be marked as skipped if the extension is unavailable.

---

### defineFunction()

Define a new function for the duration of the test.

`defineFunction(string $name, \Closure $closure): self`

This is a wrapper around [PHP's `runkit_function_define()` function](https://www.php.net/manual/en/function.runkit-function-define.php).

#### Parameters

<dl>
    <dt>$name</dt>
    <dd>The function name.</dd>
    <dt>$closure</dt>
    <dd>The code for the function.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

An `AssertWell\PHPUnitGlobalState\Exceptions\RunkitException` will be thrown if the given function cannot be defined.

---

### redefineFunction()

Redefine an existing function for the duration of the test. If `$name` does not exist, it will be defined.

`redefineFunction(string $name, \Closure $closure): self`

This is a wrapper around [PHP's `runkit_function_redefine()` function](https://www.php.net/manual/en/function.runkit-function-redefine.php).

#### Parameters

<dl>
    <dt>$name</dt>
    <dd>The function name.</dd>
    <dt>$closure</dt>
    <dd>The new code for the function.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.

An `AssertWell\PHPUnitGlobalState\Exceptions\RunkitException` will be thrown if the given function cannot be defined.

---

### deleteFunction()

Delete/undefine a function for the duration of the single test.

`deleteFunction(string $name): self`

#### Parameters

<dl>
    <dt>$name</dt>
    <dd>The function name.</dd>
</dl>

#### Return values

This method will return the calling class, enabling multiple methods to be chained.


## Examples

### Replacing a function for a single test

Imagine that we have two functions: `get_posts()` and `make_api_request()`, which look something like this:

```php
/**
 * Retrieve posts from the API and prepare it for templates.
 *
 * @return Post[] An array of Post objects.
 */
function get_posts()
{
    try {
        $posts = make_api_request('/posts');
    } catch (ApiUnavailableException $e) {
        error_log($e->getMessage(), E_USER_WARNING);
        return [];
    }

    return array_map([Post::class, 'factory'], $posts);
}

/**
 * Send a request to the API.
 *
 * @param string  $path The API path.
 * @param mixed[] $args Arguments to pass with the request.
 *
 * @return array[]
 */
function make_api_request($path, $args = [])
{
    /*
     * A bunch of pre-check conditions, sanitization, merging with default
     * values, etc.
     *
     * Then we'll make the actual request, and finally check the results.
     */
    if ($response_code >= 500) {
        throw new ApiUnavailableException('Received a 5xx error from the API.');
    }

    // More logic before finally returning the response.
}
```

We're trying to write unit tests for `get_posts()`, but the path we want to test is what happens when `make_api_request()` returns throws an `ApiUnavailableException`.

Now, assume that we don't have an easy way to emulate a 5xx status code from the API to cause `make_api_request()` to throw an `ApiUnavailableException`. Furthermore, we don't actually _want_ our tests making external requests, as that would add latency, external dependencies, and potentially cost money if it's a pay-per-usage service.

Instead of weighing down our tests with a ton of code to make `make_api_request()` throw the desired exception, we can simply replace the function:

```php
use AssertWell\PHPUnitGlobalState\Functions;
use PHPUnit\Framework\TestCase;

class MyTestClass extends TestCase
{
    use Functions;

    /**
     * @test
     */
    public function get_posts_should_return_an_empty_array_if_the_API_request_fails()
    {
        $this->redefineFunction('make_api_request', function () {
            throw new ApiUnavailableException('API is unavailable.');
        });

        $this->assertEmpty(get_posts());
    }
}
```
