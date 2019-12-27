<?php

namespace Tests;

/**
 * @covers \AssertWell\PHPUnitGlobalState\EnvironmentVariables
 * @group EnvironmentVariables
 */
class EnvironmentVariablesTest extends TestCase
{
    /**
     * @test
     * @testdox setEnv() should inject the value into the environment
     */
    public function setEnv_should_inject_the_value_into_the_environment(): void
    {
        $this->assertFalse(getenv('somevar'));

        $this->setEnv('somevar', 'someval');

        $this->assertSame('someval', getenv('somevar'));
    }

    /**
     * @test
     * @depends setEnv_should_inject_the_value_into_the_environment
     */
    public function new_environment_variables_should_be_cleared_after_each_test(): void
    {
        $this->assertFalse(getenv('somevar'), 'Expected "somevar" to have been reset.');
    }

    /**
     * @test
     * @testdox setEnv() should be able to overwrite existing environment variables
     */
    public function setEnv_should_be_able_to_overwrite_existing_environment_variables(): void
    {
        putenv('EXISTING_VAR=existing value');

        $this->setEnv('EXISTING_VAR', 'new value');

        $this->assertSame('new value', getenv('EXISTING_VAR'));
    }

    /**
     * @test
     * @depends setEnv_should_be_able_to_overwrite_existing_environment_variables
     */
    public function overwritten_values_should_be_restored_after_each_test(): void
    {
        $this->assertSame('existing value', getenv('EXISTING_VAR'));
    }

    /**
     * @test
     */
    public function an_environment_variable_may_be_defined_multiple_times(): void
    {
        $this->setEnv('somevar', 'someval');
        $this->setEnv('somevar', 'some other val');
        $this->setEnv('somevar', 'some third val');

        $this->assertSame('some third val', getenv('somevar'));
    }

    /**
     * Ensure calling setEnv() multiple times doesn't cause the variable to stick around.
     *
     * @test
     * @depends an_environment_variable_may_be_defined_multiple_times
     */
    public function environment_variables_that_did_not_exist_before_should_be_removed(): void
    {
        $this->assertFalse(getenv('somevar'));
    }

    /**
     * @test
     */
    public function existing_environment_variables_can_be_deleted(): void
    {
        putenv('WILL_BE_DELETED=abc123');
        $this->setEnv('somevar', 'value');

        $this->deleteEnv('WILL_BE_DELETED');
        $this->deleteEnv('somevar');

        $this->assertFalse(getenv('WILL_BE_DELETED'));
        $this->assertFalse(getenv('somevar'));
    }

    /**
     * @test
     * @depends existing_environment_variables_can_be_deleted
     */
    public function deleted_environment_variables_will_be_restored_after_each_test()
    {
        $this->assertSame('abc123', getenv('WILL_BE_DELETED'));
        $this->assertFalse(getenv('somevar'));
    }

    /**
     * @test
     * @testdox getEnvironmentVariables() should return registered environment variables
     */
    public function getEnvironmentVariables_should_return_registered_environment_variables(): void
    {
        putenv('EXPECTED_TO_STAY_IN_ENVIRONMENT=abc123');
        $this->setEnv('bar', 'baz');

        $this->assertSame([
            'bar' => false,
        ], $this->getEnvironmentVariables());
    }

    /**
     * @test
     * @testdox getEnvironmentVariables() should only capture globals registered via setEnv()
     * @depends getEnvironmentVariables_should_return_registered_environment_variables
     */
    public function getEnvironmentVariables_should_only_worry_about_globals_registered_via_setEnv(): void
    {
        $this->assertSame('abc123', getenv('EXPECTED_TO_STAY_IN_ENVIRONMENT'));
        $this->assertEmpty($this->getEnvironmentVariables());
    }
}
