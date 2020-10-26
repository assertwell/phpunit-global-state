<?php

namespace AssertWell\PHPUnitGlobalState\Concerns;

use PHPUnit\Framework\SkippedTestError;

trait Runkit
{
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
}
