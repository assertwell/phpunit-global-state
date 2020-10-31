<?php

namespace Tests\Concerns;

use AssertWell\PHPUnitGlobalState\Concerns\Runkit;
use PHPUnit\Framework\SkippedTestError;
use Tests\TestCase;

/**
 * @covers AssertWell\PHPUnitGlobalState\Concerns\Runkit
 *
 * @group Concerns
 * @group Runkit
 */
class RunkitTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $instance;

    /**
     * @before
     */
    public function mockRunkitTrait()
    {
        $this->instance = $this->getMockForTrait(Runkit::class, [], '', true, true, true, [
            'isRunkitAvailable',
        ]);
    }

    /**
     * @test
     */
    public function it_should_permit_tests_to_run_if_runkit_is_available()
    {
        $this->instance->expects($this->once())
            ->method('isRunkitAvailable')
            ->willReturn(true);

        $method = new \ReflectionMethod($this->instance, 'requiresRunkit');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($this->instance));
    }

    /**
     * @test
     */
    public function it_should_skip_tests_that_require_runkit_if_it_is_unavailable()
    {
        $this->instance->expects($this->once())
            ->method('isRunkitAvailable')
            ->willReturn(false);

        $method = new \ReflectionMethod($this->instance, 'requiresRunkit');
        $method->setAccessible(true);

        // Older versions of PHPUnit will actually try to mark this as skipped.
        try {
            $method->invoke($this->instance);
        } catch (SkippedTestError $e) {
            $this->assertInstanceOf(SkippedTestError::class, $e);
            return;
        }

        $this->fail('Did not catch the expected SkippedTestError.');
    }

    /**
     * @test
     */
    public function it_should_be_able_to_namespace_values()
    {
        $namespace = new \ReflectionMethod($this->instance, 'getRunkitNamespace');
        $namespace->setAccessible(true);
        $namespace = $namespace->invoke($this->instance);

        $method = new \ReflectionMethod($this->instance, 'runkitNamespace');
        $method->setAccessible(true);

        $this->assertSame(
            $namespace . 'some_global_function',
            $method->invoke($this->instance, 'some_global_function'),
            'The global namespace should be eligible.'
        );
        $this->assertSame(
            $namespace . 'Some\\Namespaced\\function_to_move',
            $method->invoke($this->instance, 'Some\\Namespaced\\function_to_move'),
            'Namespaces should be preserved.'
        );
        $this->assertSame(
            $namespace . 'Some\\Namespaced\\function_with_leading_slashes',
            $method->invoke($this->instance, '\\Some\\Namespaced\\function_with_leading_slashes'),
            'Leading slashes should be stripped.'
        );
    }
}
