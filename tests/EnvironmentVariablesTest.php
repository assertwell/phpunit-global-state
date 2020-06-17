<?php

namespace Tests;

/**
 * @covers AssertWell\PHPUnitGlobalState\EnvironmentVariables
 *
 * @group EnvironmentVariables
 */
class EnvironmentVariablesTest extends TestCase
{
    /**
     * @test
     */
    public function setEnvironmentVariable_should_be_able_to_handle_new_environment_variables(): void
    {
        $this->setEnvironmentVariable('TEST_VAR', 'first');
        $this->assertSame('first', getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertFalse(getenv('TEST_VAR'), 'The TEST_VAR environment variable should have been deleted.');
    }

    /**
     * @test
     */
    public function setEnvironmentVariable_should_be_able_to_handle_new_environment_variables_even_if_redefined(): void
    {
        $this->setEnvironmentVariable('TEST_VAR', 'first');
        $this->setEnvironmentVariable('TEST_VAR', 'second');
        $this->assertSame('second', getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertFalse(getenv('TEST_VAR'), 'The TEST_VAR environment variable should have been deleted.');
    }

    /**
     * @test
     */
    public function setEnvironmentVariable_should_be_able_to_update_existing_environment_variables(): void
    {
        putenv('TEST_VAR=first');

        $this->setEnvironmentVariable('TEST_VAR', 'second');
        $this->assertSame('second', getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertSame('first', getenv('TEST_VAR'), 'The previous value of TEST_VAR should have been restored.');
    }

    /**
     * @test
     */
    public function setEnvironmentVariable_should_be_able_to_update_existing_environment_variables_multiple_times(): void
    {
        putenv('TEST_VAR=first');

        $this->setEnvironmentVariable('TEST_VAR', 'second');
        $this->setEnvironmentVariable('TEST_VAR', 'third');
        $this->assertSame('third', getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertSame('first', getenv('TEST_VAR'), 'The initial value of TEST_VAR should have been restored.');
    }

    /**
     * @test
     */
    public function deleteEnvironmentVariable_should_be_able_to_delete_existing_environment_variables(): void
    {
        putenv('TEST_VAR=first');

        $this->deleteEnvironmentVariable('TEST_VAR');
        $this->assertFalse(getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertSame('first', getenv('TEST_VAR'), 'The initial value of TEST_VAR should have been restored.');
    }

    /**
     * @test
     * @testdox deleteEnvironmentVariable() should catch deleted — then re-defined — environment variables
     */
    public function deleteEnvironmentVariable_should_catch_deleted_then_redefined_environment_variables(): void
    {
        putenv('TEST_VAR=first');

        $this->deleteEnvironmentVariable('TEST_VAR');
        $this->setEnvironmentVariable('TEST_VAR', 'second');
        $this->assertSame('second', getenv('TEST_VAR'));

        $this->resetEnvironmentVariables();
        $this->assertSame('first', getenv('TEST_VAR'), 'The initial value of TEST_VAR should have been restored.');
    }
}
