<?php
/**
 * Example unit test — tests the ExampleJob stub shipped with the skeleton.
 *
 * Replace or extend this once you add your own jobs.
 *
 * @package {{NAMESPACE}}\Tests\Unit
 */

declare(strict_types=1);

namespace {{NAMESPACE}}\Tests\Unit;

use {{NAMESPACE}}\Jobs\ExampleJob;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class ExampleJobTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_handle_returns_early_when_user_not_found(): void {
        Functions\when( 'get_userdata' )->justReturn( false );

        $job = new ExampleJob( 999 );
        $job->handle(); // must not throw

        $this->assertTrue( true );
    }

    public function test_default_queue_is_default(): void {
        $job = new ExampleJob( 1 );
        $this->assertSame( 'default', $job->get_queue() );
    }

    public function test_max_attempts_is_three(): void {
        $job = new ExampleJob( 1 );
        $this->assertSame( 3, $job->get_max_attempts() );
    }

    public function test_delay_is_zero_by_default(): void {
        $job = new ExampleJob( 1 );
        $this->assertSame( 0, $job->get_delay() );
    }

    public function test_on_queue_changes_queue(): void {
        $job = new ExampleJob( 1 );
        $job->on_queue( 'emails' );
        $this->assertSame( 'emails', $job->get_queue() );
    }

    public function test_delay_sets_delay_seconds(): void {
        $job = new ExampleJob( 1 );
        $job->delay( 120 );
        $this->assertSame( 120, $job->get_delay() );
    }
}
