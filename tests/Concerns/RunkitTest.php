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
     * @test
     */
    public function it_should_permit_tests_to_run_if_runkit_is_available()
    {
        $instance = $this->getMockBuilder(Runkit::class)
            ->setMethods(['isRunkitAvailable'])
            ->getMockForTrait();
        $instance->expects($this->once())
            ->method('isRunkitAvailable')
            ->willReturn(true);

        $method = new \ReflectionMethod($instance, 'requiresRunkit');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($instance));
    }

    /**
     * @test
     */
    public function it_should_skip_tests_that_require_runkit_if_it_is_unavailable()
    {
        $instance = $this->getMockBuilder(Runkit::class)
            ->setMethods(['isRunkitAvailable'])
            ->getMockForTrait();
        $instance->expects($this->once())
            ->method('isRunkitAvailable')
            ->willReturn(false);

        $method = new \ReflectionMethod($instance, 'requiresRunkit');
        $method->setAccessible(true);

        $this->expectException(SkippedTestError::class);

        $method->invoke($instance);
    }
}
