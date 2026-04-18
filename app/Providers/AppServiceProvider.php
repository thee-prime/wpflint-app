<?php
/**
 * Application service provider — main entry point for your plugin.
 *
 * @package {{NAMESPACE}}\Providers
 */

declare(strict_types=1);

namespace {{NAMESPACE}}\Providers;

use {{NAMESPACE}}\WPFlint\Http\Router;
use {{NAMESPACE}}\WPFlint\Providers\ServiceProvider;

/**
 * Register services and boot routes and WordPress hooks.
 *
 * register() — bind to the container. No WordPress calls here; WP is not ready.
 * boot()     — register routes and actions. Called on 'init'.
 */
class AppServiceProvider extends ServiceProvider {

    /**
     * Bind services into the container.
     *
     * @return void
     */
    public function register(): void {
        // Enable background jobs (DB-backed queue via WP-Cron):
        // $this->app->register( \{{NAMESPACE}}\WPFlint\Queue\QueueServiceProvider::class );

        // Bind your own services:
        // $this->app->singleton( OrderService::class, fn( $app ) => new OrderService() );
    }

    /**
     * Boot the provider — load routes and register WordPress hooks.
     *
     * @return void
     */
    public function boot(): void {
        $router = $this->app->make( Router::class );
        $root   = dirname( dirname( __DIR__ ) );

        require $root . '/routes/ajax.php';
        require $root . '/routes/api.php';

        $router->boot();
    }
}
