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

// Register WP-CLI generator and utility commands (dev use only).
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    $ns = '{{NAMESPACE}}\\WPFlint\\Console\\';
    \WP_CLI::add_command( 'wpflint make:command',    $ns . 'MakeCommandCommand' );
    \WP_CLI::add_command( 'wpflint make:controller', $ns . 'MakeControllerCommand' );
    \WP_CLI::add_command( 'wpflint make:event',      $ns . 'MakeEventCommand' );
    \WP_CLI::add_command( 'wpflint make:facade',     $ns . 'MakeFacadeCommand' );
    \WP_CLI::add_command( 'wpflint make:helper',     $ns . 'MakeHelperCommand' );
    \WP_CLI::add_command( 'wpflint make:listener',   $ns . 'MakeListenerCommand' );
    \WP_CLI::add_command( 'wpflint make:middleware', $ns . 'MakeMiddlewareCommand' );
    \WP_CLI::add_command( 'wpflint make:migration',  $ns . 'MakeMigrationCommand' );
    \WP_CLI::add_command( 'wpflint make:model',      $ns . 'MakeModelCommand' );
    \WP_CLI::add_command( 'wpflint make:provider',   $ns . 'MakeProviderCommand' );
    \WP_CLI::add_command( 'wpflint make:request',    $ns . 'MakeRequestCommand' );
    \WP_CLI::add_command( 'wpflint make:rule',       $ns . 'MakeRuleCommand' );
    \WP_CLI::add_command( 'wpflint migrate',         $ns . 'MigrateCommand' );
    \WP_CLI::add_command( 'wpflint cache:clear',     $ns . 'CacheClearCommand' );
}

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
