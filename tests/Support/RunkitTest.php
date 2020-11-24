<?php

namespace Tests\Support;

use AssertWell\PHPUnitGlobalState\Support\Runkit;
use Tests\TestCase;

/**
 * @covers AssertWell\PHPUnitGlobalState\Support\Runkit
 *
 * @group Runkit
 * @group Support
 */
class RunkitTest extends TestCase
{
    /**
     * @test
     */
    public function the_magic_method_should_throw_an_exception_if_the_runkit_function_does_not_exist()
    {
        $this->expectException(\BadFunctionCallException::class);

        Runkit::a_function_that_does_not_exist();
    }

    /**
     * @test
     */
    public function getNamespace_should_return_the_same_value_on_subsequent_calls()
    {
        $namespace = Runkit::getNamespace();

        $this->assertSame(Runkit::getNamespace(), $namespace);
    }

    /**
     * @test
     */
    public function makeNamespaced_should_return_the_given_reference_with_a_prefixed_namespace()
    {
        $namespace = Runkit::getNamespace();

        $this->assertSame(
            $namespace . 'some_global_function',
            Runkit::makeNamespaced('some_global_function'),
            'The global namespace should be eligible.'
        );
        $this->assertSame(
            $namespace . 'Some\\Namespaced\\function_to_move',
            Runkit::makeNamespaced('Some\\Namespaced\\function_to_move'),
            'Namespaces should be preserved.'
        );
        $this->assertSame(
            $namespace . 'Some\\Namespaced\\function_with_leading_slashes',
            Runkit::makeNamespaced('\\Some\\Namespaced\\function_with_leading_slashes'),
            'Leading slashes should be stripped.'
        );
    }

    /**
     * @test
     */
    public function makePrefixed_should_return_the_given_reference_with_a_prefix()
    {
        $prefix = Runkit::getPrefix();

        $this->assertSame(
            $prefix . 'some_method',
            Runkit::makePrefixed('some_method')
        );
        $this->assertSame(
            $prefix . 'Some_Namespaced_function_to_move',
            Runkit::makePrefixed('Some\\Namespaced\\function_to_move'),
            'Namespaces should be preserved.'
        );
    }
}
