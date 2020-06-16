<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use AssertWell\PHPUnitGlobalState\EnvironmentVariables;
use AssertWell\PHPUnitGlobalState\GlobalVariables;

/**
 * Since this test suite is testing a series of traits meant to aid in testing other codebases
 * (very meta, I know), we'll apply all of the traits to this base TestCase class to ensure we
 * don't have conflicts when multiple traits are used at once.
 *
 * @coversNothing
 */
abstract class TestCase extends BaseTestCase
{
    use EnvironmentVariables;
    use GlobalVariables;
}
