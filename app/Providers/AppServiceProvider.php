<?php
/**
 * Application service provider — main entry point for your plugin.
 *
 * @package {{NAMESPACE}}\Providers
 */

declare(strict_types=1);

namespace {{NAMESPACE}}\Providers;

use {{NAMESPACE}}\WPFlint\Assets\ViteAssets;
use {{NAMESPACE}}\WPFlint\Http\Router;
use {{NAMESPACE}}\WPFlint\Providers\ServiceProvider;
use {{NAMESPACE}}\WPFlint\View\View;

/**
 * Register services and boot routes, assets, and WordPress hooks.
 *
 * register() — bind to the container. No WordPress calls here; WP is not ready.
 * boot()     — register routes, actions, filters. Called on 'init'.
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

        // Bind Vite assets helper.
        $this->app->singleton(
            ViteAssets::class,
            fn( $app ) => new ViteAssets(
                $app->make( 'plugin.dir' ),
                $app->make( 'plugin.url' )
            )
        );
    }

    /**
     * Boot the provider — load routes, register assets, and WordPress hooks.
     *
     * @return void
     */
    public function boot(): void {
        View::set_base_path( $this->app->make( 'plugin.dir' ) . 'templates' );
        $this->register_routes();
        $this->register_assets();
    }

    // ---------------------------------------------------------------
    // Routes
    // ---------------------------------------------------------------

    /**
     * Load AJAX and REST API routes.
     *
     * @return void
     */
    private function register_routes(): void {
        $router = $this->app->make( Router::class );
        $root   = dirname( dirname( __DIR__ ) );

        require $root . '/routes/ajax.php';
        require $root . '/routes/api.php';

        $router->boot();
    }

    // ---------------------------------------------------------------
    // Assets
    // ---------------------------------------------------------------

    /**
     * Enqueue the React app and register all CSS files in resources/css/.
     *
     * Script data is passed through a filter — add extra fields from anywhere:
     *
     *     add_filter( '{{TEXT_DOMAIN}}_script_data', function ( $data ) {
     *         $data['userId'] = get_current_user_id();
     *         return $data;
     *     } );
     *
     * @return void
     */
    private function register_assets(): void {
        add_action(
            'admin_enqueue_scripts',
            function () {
                $vite = $this->app->make( ViteAssets::class );

                // Enqueue the React app entry point.
                $vite->enqueue( '{{TEXT_DOMAIN}}-app', 'resources/js/app.jsx' );

                // Register every *.css in resources/css/ as '{{TEXT_DOMAIN}}-{filename}'.
                // Call wp_enqueue_style( '{{TEXT_DOMAIN}}-app' ) in your page template.
                $vite->register_css_dir( '{{TEXT_DOMAIN}}-' );

                // Pass data to the React app — filterable from anywhere in the plugin.
                $data = apply_filters(
                    '{{TEXT_DOMAIN}}_script_data',
                    array(
                        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                        'nonce'     => wp_create_nonce( '{{TEXT_DOMAIN}}' ),
                        'restUrl'   => rest_url(),
                        'pluginUrl' => $this->app->make( 'plugin.url' ),
                    )
                );

                wp_localize_script( '{{TEXT_DOMAIN}}-app', 'wpData', $data );
            }
        );
    }
}
