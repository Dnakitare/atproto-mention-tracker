<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Clean up the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clear all mocks
        \Mockery::close();
    }
}
