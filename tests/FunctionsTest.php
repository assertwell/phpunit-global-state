<?php

namespace Tests;

use AssertWell\PHPUnitGlobalState\Exceptions\FunctionExistsException;
use AssertWell\PHPUnitGlobalState\Support\Runkit;

use function Tests\Stubs\sum_three_numbers;

/**
 * @covers AssertWell\PHPUnitGlobalState\Functions
 *
 * @group Functions
 */
class FunctionsTest extends TestCase
{
    /**
     * @before
     */
    protected function verifyRunkitIsAvailable()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }
    }

    /**
     * @test
     * @testdox defineFunction() should be able to define a new function
     */
    public function defineFunction_should_be_able_to_define_a_new_function()
    {
        $this->assertFalse(function_exists('my_custom_function'));

        $this->defineFunction('my_custom_function', function ($return) {
            return $return;
        });

        $this->assertSame(123, my_custom_function(123));

        $this->restoreFunctions();
        $this->assertFalse(function_exists('my_custom_function'), 'The new function should have been undefined.');
    }

    /**
     * @test
     * @testdox defineFunction() should throw a warning if the function already exists
     */
    public function defineFunction_should_throw_a_warning_if_the_function_already_exists()
    {
        $this->assertTrue(function_exists('Tests\\Stubs\\sum_three_numbers'));
        $signature = (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers'));

        $this->expectException(FunctionExistsException::class);
        $this->defineFunction('Tests\\Stubs\\sum_three_numbers', function ($return) {
            return $return;
        });

        $this->assertSame(
            $signature,
            (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers')),
            'The original function should have been left untouched.'
        );
    }

    /**
     * @test
     * @testdox redefineFunction() should be able to redefine an existing function
     */
    public function redefineFunction_should_be_able_to_redefine_existing_functions()
    {
        $this->assertTrue(function_exists('Tests\\Stubs\\sum_three_numbers'));
        $signature = (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers'));

        $this->redefineFunction('Tests\\Stubs\\sum_three_numbers', function () {
            return 123;
        });

        $this->assertSame(123, sum_three_numbers(1, 2, 3));

        $this->restoreFunctions();
        $this->assertTrue(function_exists('Tests\\Stubs\\sum_three_numbers'));
        $this->assertSame(
            $signature,
            (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers')),
            'The original function definition should have been restored.'
        );
    }

    /**
     * @test
     * @testdox redefineFunction() should be able to redefine an existing function
     */
    public function redefineFunction_should_be_able_to_redefine_newly_defined_functions()
    {
        $this->defineFunction('my_test_function', function () {
            return 'abc';
        });
        $this->redefineFunction('my_test_function', function () {
            return 'xyz';
        });

        $this->assertSame('xyz', my_test_function());

        $this->restoreFunctions();
        $this->assertFalse(
            function_exists('my_test_function'),
            'The newly-created function should still be removed.'
        );
    }

    /**
     * @test
     * @testdox redefineFunction() should be able to redefine an existing function multiple times
     * @depends redefineFunction_should_be_able_to_redefine_existing_functions
     */
    public function redefineFunction_should_be_able_to_redefine_existing_functions_multiple_times()
    {
        $this->assertTrue(function_exists('Tests\\Stubs\\sum_three_numbers'));
        $signature = (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers'));

        $this->redefineFunction('Tests\\Stubs\\sum_three_numbers', function () {
            return 'first';
        });
        $this->redefineFunction('Tests\\Stubs\\sum_three_numbers', function () {
            return 'second';
        });
        $this->redefineFunction('Tests\\Stubs\\sum_three_numbers', function () {
            return 'third';
        });

        $this->assertSame(
            'third',
            sum_three_numbers(1, 2, 3),
            'Expected the latest re-definition to be used.'
        );

        $this->restoreFunctions();
        $this->assertSame(
            $signature,
            (string) (new \ReflectionFunction('Tests\\Stubs\\sum_three_numbers')),
            'The original function definition should have been restored.'
        );
    }

    /**
     * @test
     * @testdox redefineFunction() should define functions if they do not exist
     * @depends defineFunction_should_be_able_to_define_a_new_function
     */
    public function redefineFunction_should_define_functions_if_they_do_not_exist()
    {
        $this->assertFalse(function_exists('my_custom_function'));

        $this->redefineFunction('my_custom_function', function ($return) {
            return $return;
        });

        $this->assertSame(123, my_custom_function(123));

        $this->restoreFunctions();
        $this->assertFalse(function_exists('my_custom_function'), 'The new function should have been undefined.');
    }

    /**
     * @test
     * @testdox deleteFunction() should be able to delete functions
     */
    public function deleteFunction_should_be_able_to_delete_functions()
    {
        $this->assertTrue(
            function_exists('Tests\\Stubs\\sum_three_numbers'),
            'Test is predicated on this function existing.'
        );

        $this->deleteFunction('Tests\\Stubs\\sum_three_numbers');
        $this->assertFalse(
            function_exists('Tests\\Stubs\\sum_three_numbers'),
            'The function should have been deleted.'
        );

        $this->restoreFunctions();
        $this->assertTrue(
            function_exists('Tests\\Stubs\\sum_three_numbers'),
            'The function should have been restored.'
        );
    }

    /**
     * @test
     * @testdox deleteFunction() should do nothing if the function does not exist
     */
    public function deleteFunction_should_do_nothing_if_the_function_does_not_exist()
    {
        $this->assertFalse(
            function_exists('Tests\\Stubs\\sum_three_numbers_again'),
            'Test is predicated on this function NOT existing.'
        );

        $this->deleteFunction('Tests\\Stubs\\sum_three_numbers_again');
        $this->assertFalse(
            function_exists('Tests\\Stubs\\sum_three_numbers_again'),
            'Deleting a non-existent function should not do anything.'
        );

        $this->restoreFunctions();
        $this->assertFalse(
            function_exists('Tests\\Stubs\\sum_three_numbers_again'),
            'Nothing should be restored as there was nothing to begin with.'
        );
    }
}
