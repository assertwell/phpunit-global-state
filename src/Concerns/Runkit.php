<?php

namespace AssertWell\PHPUnitGlobalState\Concerns;

use PHPUnit\Framework\SkippedTestError;

trait Runkit
{
    /**
     * Mark a test as skipped if Runkit is not available.
     *
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected function requiresRunkit(string $message = '')
    {
        if ($this->isRunkitAvailable()) {
            return;
        }

        throw new SkippedTestError($message ?: 'This test requires Runkit, skipping.');
    }

    /**
     * Determine whether or not Runkit is available in the current environment.
     */
    protected function isRunkitAvailable(): bool
    {
        return function_exists('runkit_constant_redefine');
    }
}
