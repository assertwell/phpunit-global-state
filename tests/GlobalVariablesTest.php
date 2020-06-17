<?php

namespace Tests;

/**
 * @covers AssertWell\PHPUnitGlobalState\GlobalVariables
 *
 * @group GlobalVariables
 */
class GlobalVariablesTest extends TestCase
{
    /**
     * @test
     * @testdox setGlobalVariable() should be able to handle new global variables
     */
    public function setGlobalVariable_should_be_able_to_handle_new_global_variables(): void
    {
        $this->assertArrayNotHasKey('setGlobalVariable', $GLOBALS);
        $this->setGlobalVariable('setGlobalVariable', 'new value');
        $this->assertSame('new value', $GLOBALS['setGlobalVariable']);

        $this->restoreGlobalVariables();
        $this->assertArrayNotHasKey('setGlobalVariable', $GLOBALS);
    }

    /**
     * @test
     * @testdox setGlobalVariable() should be able to handle redefined global variables
     */
    public function setGlobalVariable_should_be_able_to_handle_redefined_global_variables(): void
    {
        $GLOBALS['setGlobalVariable'] = 'old value';
        $this->setGlobalVariable('setGlobalVariable', 'new value');
        $this->assertSame('new value', $GLOBALS['setGlobalVariable']);

        $this->restoreGlobalVariables();
        $this->assertSame('old value', $GLOBALS['setGlobalVariable']);
    }

    /**
     * @test
     * @testdox setGlobalVariable() should be able to handle global variables that are redefined multiple times
     */
    public function setGlobalVariable_should_be_able_to_handle_global_variables_that_are_redefined_multiple_times(): void
    {
        $GLOBALS['setGlobalVariable'] = 'old value';
        $this->setGlobalVariable('setGlobalVariable', 'new value');
        $this->setGlobalVariable('setGlobalVariable', 'newer value');
        $this->assertSame('newer value', $GLOBALS['setGlobalVariable']);

        $this->restoreGlobalVariables();
        $this->assertSame('old value', $GLOBALS['setGlobalVariable']);
    }

    /**
     * @test
     * @testdox setGlobalVariable() should be able to handle global variables that have been unset
     */
    public function setGlobalVariable_should_be_able_to_handle_unset_global_variables(): void
    {
        $GLOBALS['setGlobalVariable'] = 'old value';
        $this->setGlobalVariable('setGlobalVariable', null);
        $this->assertArrayNotHasKey('setGlobalVariable', $GLOBALS);

        $this->restoreGlobalVariables();
        $this->assertSame('old value', $GLOBALS['setGlobalVariable']);
    }
}
