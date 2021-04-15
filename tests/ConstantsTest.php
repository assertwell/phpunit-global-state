<?php

namespace Tests;

use AssertWell\PHPUnitGlobalState\Exceptions\RedefineException;
use AssertWell\PHPUnitGlobalState\Support\Runkit;

/**
 * @covers AssertWell\PHPUnitGlobalState\Constants
 *
 * @group Constants
 */
class ConstantsTest extends TestCase
{
    const EXISTING_CONSTANT = 'some existing value';
    const DELETE_THIS_CONSTANT = 'delete this constant';

    /**
     * @beforeClass
     */
    public static function defineConstants()
    {
        define('EXISTING_CONSTANT', self::EXISTING_CONSTANT);
        define('DELETE_THIS_CONSTANT', self::DELETE_THIS_CONSTANT);
    }

    /**
     * @test
     * @testdox setConstant() should be able to handle newly-defined constants
     */
    public function setConstant_should_be_able_to_handle_newly_defined_constants()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }

        $this->assertFalse(defined('SOME_CONSTANT'));

        $this->setConstant('SOME_CONSTANT', 'some value');
        $this->assertSame('some value', constant('SOME_CONSTANT'));

        $this->restoreConstants();
        $this->assertFalse(defined('SOME_CONSTANT'), 'The new constant should have been undefined.');
    }

    /**
     * @test
     * @testdox setConstant() should be able to redefine existing constants
     */
    public function setConstant_should_be_able_to_redefine_existing_constants()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }

        $this->setConstant('EXISTING_CONSTANT', 'some other value');
        $this->assertSame('some other value', constant('EXISTING_CONSTANT'));

        $this->restoreConstants();
        $this->assertSame(
            self::EXISTING_CONSTANT,
            constant('EXISTING_CONSTANT'),
            'The constant should have been restored to its initial state.'
        );
    }

    /**
     * @test
     * @testdox setConstant() should throw an exception if it cannot redefine a constant
     */
    public function setConstant_should_throw_an_exception_if_it_cannot_redefine_a_constant()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }

        $this->expectException(RedefineException::class);
        $this->setConstant('EXISTING_CONSTANT', (object) ['some' => 'object']);

        $this->assertSame(
            self::EXISTING_CONSTANT,
            constant('EXISTING_CONSTANT'),
            'The constant should have been left unchanged.'
        );
    }

    /**
     * @test
     * @testdox deleteConstant() should be able to remove an existing constant
     */
    public function deleteConstant_should_remove_an_existing_constant()
    {
        if (! Runkit::isAvailable()) {
            $this->markTestSkipped('This test depends on runkit being available.');
        }

        $this->deleteConstant('DELETE_THIS_CONSTANT');
        $this->assertFalse(defined('DELETE_THIS_CONSTANT'));

        $this->restoreConstants();
        $this->assertSame(
            self::DELETE_THIS_CONSTANT,
            constant('DELETE_THIS_CONSTANT'),
            'The constant should have been restored to its initial state.'
        );
    }

    /**
     * @test
     * @testdox deleteConstant() should do nothing if the given constant does not exist
     */
    public function deleteConstant_should_do_nothing_if_the_given_constant_does_not_exist()
    {
        $this->assertFalse(defined('SOME_CONSTANT'));

        $this->assertSame($this, $this->deleteConstant('SOME_CONSTANT'));
        $this->assertFalse(defined('SOME_CONSTANT'), 'Nothing should have happened.');
    }
}
