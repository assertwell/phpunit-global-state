<?php

namespace Tests;

/**
 * @covers \AssertWell\PHPUnitGlobalState\GlobalVariablesTest
 * @group GlobalVariables
 */
class GlobalVariablesTest extends TestCase
{
    /**
     * @test
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
