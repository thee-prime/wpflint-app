<?php
/**
 * Plugin Name:       {{PLUGIN_NAME}}
 * Plugin URI:        {{PLUGIN_URI}}
 * Description:       {{DESCRIPTION}}
 * Version:           1.0.0
 * Author:            {{AUTHOR}}
 * Author URI:        {{AUTHOR_URI}}
 * Text Domain:       {{TEXT_DOMAIN}}
 * Domain Path:       /languages
 * Requires at least: 5.9
 * Requires PHP:      7.4
 *
 * @package {{NAMESPACE}}
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Composer autoloader — your app classes.
require_once __DIR__ . '/vendor/autoload.php';

// Strauss-prefixed framework — zero conflict with other WPFlint plugins.
require_once __DIR__ . '/vendor-prefixed/autoload.php';

use {{NAMESPACE}}\WPFlint\Application;
use {{NAMESPACE}}\WPFlint\Lifecycle\Lifecycle;

$app = Application::get_instance( __DIR__ );

// Bind plugin path and URL to the container so providers don't need __FILE__ tricks.
$app->instance( 'plugin.dir', plugin_dir_path( __FILE__ ) );
$app->instance( 'plugin.url', plugin_dir_url( __FILE__ ) );

$app->register( {{NAMESPACE}}\Providers\AppServiceProvider::class );
$app->register( {{NAMESPACE}}\Providers\MenuServiceProvider::class );

Lifecycle::for( __FILE__ )
    ->on_activate(
        function () use ( $app ) {
            flush_rewrite_rules();
        }
    )
    ->on_deactivate(
        function () {
            flush_rewrite_rules();
        }
    )
    ->register();

$app->bootstrap();
