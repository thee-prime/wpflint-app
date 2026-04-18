<?php

declare(strict_types=1);

namespace {{NAMESPACE}}\Providers;

use {{NAMESPACE}}\WPFlint\Providers\ServiceProvider;

/**
 * Application service provider.
 *
 * This is the main entry point for registering your plugin's
 * services and hooking into WordPress.
 *
 * register() → bind services into the container (no WP calls here).
 * boot()     → register routes, actions, filters (WP is ready).
 *
 * @package {{NAMESPACE}}
 */
class AppServiceProvider extends ServiceProvider {

    /**
     * Bind services into the container.
     *
     * Called before WordPress init. Use $this->app->singleton() / bind() here.
     * Do NOT call any WordPress functions in this method.
     */
    public function register(): void {
        // Example:
        // $this->app->singleton( OrderService::class, fn( $app ) => new OrderService() );
    }

    /**
     * Boot the provider.
     *
     * Called on WordPress 'init'. Register routes, actions, metaboxes,
     * post types, etc. here.
     */
    public function boot(): void {
        // Example:
        // $router = $this->app->make( \{{NAMESPACE}}\WPFlint\Http\Router::class );
        // $router->ajax( '{{TEXT_DOMAIN}}/save', [ OrderController::class, 'store' ] )
        //        ->middleware( [ 'nonce:save_order', 'can:edit_posts' ] );
        // $router->boot();
    }
}
