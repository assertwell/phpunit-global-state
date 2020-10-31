<?php

namespace AssertWell\PHPUnitGlobalState\Concerns;

use PHPUnit\Framework\SkippedTestError;

trait Runkit
{
    /**
     * A namespace used to move things out of the way for the duration of a test.
     *
     * @var string
     */
    private $runkitNamespace;

    /**
     * Mark a test as skipped if Runkit is not available.
     *
     * @throws \PHPUnit\Framework\SkippedTestError
     *
     * @param string $message Optional. A message to include if the SkippedTestError exception
     *                        is thrown. Default is empty.
     *
     * @return void
     */
    protected function requiresRunkit($message = '')
    {
        if ($this->isRunkitAvailable()) {
            return;
        }

        throw new SkippedTestError($message ?: 'This test requires Runkit, skipping.');
    }

    /**
     * Determine whether or not Runkit is available in the current environment.
     *
     * @return bool
     */
    protected function isRunkitAvailable()
    {
        return function_exists('runkit7_constant_redefine')
            || function_exists('runkit_constant_redefine');
    }

    /**
     * Get the current runkit namespace.
     *
     * If the property is currently empty, one will be created.
     *
     * @return string The namespace (with trailing backslash) where we're moving functions,
     *                constants, etc. during tests.
     */
    protected function getRunkitNamespace()
    {
        if (empty($this->runkitNamespace)) {
            $this->runkitNamespace = uniqid(__NAMESPACE__ . '\\runkit_') . '\\';
        }

        return $this->runkitNamespace;
    }

    /**
     * Namespace the given reference.
     *
     * @param string $var The item to be moved into the temporary test namespace.
     *
     * @return string The newly-namespaced item.
     */
    protected function runkitNamespace($var)
    {
        // Strip leading backslashes.
        if (0 === mb_strpos($var, '\\')) {
            $var = mb_substr($var, 1);
        }

        return $this->getRunkitNamespace() . $var;
    }
}
