<?php

namespace Tests;

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * Tests to ensure that state may be set in PHPUnit fixtures.
 *
 * @ticket https://github.com/assertwell/phpunit-global-state/issues/14
 *
 * @covers nothing
 */
class FixtureTest extends TestCase
{
    use SetUpTearDownTrait;

    /**
     * @var array<string>
     */
    protected $backupGlobalsBlacklist = [
        'FIXTURE_BEFORE_GLOBAL',
        'FIXTURE_SETUP_GLOBAL',
    ];

    public function doSetUp()
    {
        parent::setUp();

        $this->setConstant('FIXTURE_SETUP_CONSTANT', true);
        $this->setEnvironmentVariable('FIXTURE_SETUP_ENV', 'abc');
        $this->setGlobalVariable('FIXTURE_SETUP_GLOBAL', true);

        $this->defineFunction('fixture_setup_function', function () {
            return 'abc';
        });
    }

    /**
     * @before
     */
    protected function defineInitialValues()
    {
        $this->setConstant('FIXTURE_BEFORE_CONSTANT', true);
        $this->setEnvironmentVariable('FIXTURE_BEFORE_ENV', 'xyz');
        $this->setGlobalVariable('FIXTURE_BEFORE_GLOBAL', true);

        $this->defineFunction('fixture_before_function', function () {
            return 'xyz';
        });
    }

    /**
     * @test
     * @group Constants
     */
    public function it_should_permit_constants_to_be_set_in_fixtures()
    {
        $this->assertTrue(
            defined('FIXTURE_SETUP_CONSTANT'),
            'The constant should have been defined in the setUp() method.'
        );
        $this->assertTrue(
            defined('FIXTURE_BEFORE_CONSTANT'),
            'The constant should have been defined in the @before method.'
        );

        $this->restoreConstants();
        $this->assertFalse(
            defined('FIXTURE_SETUP_CONSTANT'),
            'The constant should have been undefined by restoreConstants().'
        );
        $this->assertFalse(
            defined('FIXTURE_BEFORE_CONSTANT'),
            'The constant should have been undefined by restoreConstants().'
        );
    }

    /**
     * @test
     * @group EnvironmentVariables
     */
    public function it_should_permit_environment_variables_to_be_set_in_fixtures()
    {
        $this->assertSame(
            'abc',
            getenv('FIXTURE_SETUP_ENV'),
            'The environment variable should have been defined in the setUp() method.'
        );
        $this->assertSame(
            'xyz',
            getenv('FIXTURE_BEFORE_ENV'),
            'The environment variable should have been defined in the @before method.'
        );

        $this->restoreEnvironmentVariables();
        $this->assertFalse(
            getenv('FIXTURE_SETUP_ENV'),
            'The environment variable should have been undefined by restoreGlobalVariables().'
        );
        $this->assertFalse(
            getenv('FIXTURE_BEFORE_ENV'),
            'The environment variable should have been undefined by restoreGlobalVariables().'
        );
    }

    /**
     * @test
     * @group Functions
     */
    public function it_should_permit_functions_to_be_set_in_fixtures()
    {
        $this->assertSame(
            'abc',
            fixture_setup_function(),
            'The function should have been defined in the setUp() method.'
        );
        $this->assertSame(
            'xyz',
            fixture_before_function(),
            'The function should have been defined in the @before method.'
        );

        $this->restoreFunctions();
        $this->assertFalse(
            function_exists('fixture_setup_function'),
            'The function should have been undefined by restoreFunctions().'
        );
        $this->assertFalse(
            function_exists('fixture_before_function'),
            'The function should have been undefined by restoreFunctions().'
        );
    }

    /**
     * @test
     * @group GlobalVariables
     */
    public function it_should_permit_global_variables_to_be_set_in_fixtures()
    {
        $this->assertTrue(
            isset($GLOBALS['FIXTURE_SETUP_GLOBAL']),
            'The global variable should have been defined in the setUp() method.'
        );
        $this->assertTrue(
            isset($GLOBALS['FIXTURE_BEFORE_GLOBAL']),
            'The global variable should have been defined in the @before method.'
        );

        $this->restoreGlobalVariables();
        $this->assertFalse(
            isset($GLOBALS['FIXTURE_SETUP_GLOBAL']),
            'The global variable should have been undefined by restoreGlobalVariables().'
        );
        $this->assertFalse(
            isset($GLOBALS['FIXTURE_BEFORE_GLOBAL']),
            'The global variable should have been undefined by restoreGlobalVariables().'
        );
    }
}
