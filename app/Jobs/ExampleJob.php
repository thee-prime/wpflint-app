<?php
/**
 * Example background job.
 *
 * Dispatch from anywhere in your plugin:
 *
 *     wpflint_dispatch( new ExampleJob( $user_id ) );
 *
 * Delayed dispatch (runs after 60 s):
 *
 *     wpflint_dispatch( ( new ExampleJob( $user_id ) )->delay( 60 ) );
 *
 * Custom queue and max attempts:
 *
 *     wpflint_dispatch( ( new ExampleJob( $user_id ) )->on_queue( 'emails' )->tries( 5 ) );
 *
 * Jobs require QueueServiceProvider — uncomment it in AppServiceProvider::register().
 *
 * @package {{NAMESPACE}}\Jobs
 */

declare(strict_types=1);

namespace {{NAMESPACE}}\Jobs;

use {{NAMESPACE}}\WPFlint\Queue\Job;

class ExampleJob extends Job {

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @param int $user_id WordPress user ID.
     */
    public function __construct( int $user_id ) {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void {
        // TODO: implement your background task here.
        // Example: send a welcome email, process an import, generate a report.
        $user = get_userdata( $this->user_id );
        if ( ! $user ) {
            return;
        }
        // wp_mail( $user->user_email, __( 'Hello!', '{{TEXT_DOMAIN}}' ), '...' );
    }

    /**
     * Called when all attempts are exhausted.
     *
     * @param \Throwable $exception Last exception from handle().
     * @return void
     */
    public function failed( \Throwable $exception ): void {
        // Log, notify, or compensate here.
    }
}
