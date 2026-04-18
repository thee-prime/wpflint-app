<?php

/**
 * Application configuration.
 *
 * Access values via:
 *   wpflint_config('app.name')
 *   or $app->make( \{{NAMESPACE}}\WPFlint\Config\Repository::class )->get('app.name')
 *
 * @package {{NAMESPACE}}
 */

return array(
    'name'    => '{{PLUGIN_NAME}}',
    'slug'    => '{{TEXT_DOMAIN}}',
    'version' => '1.0.0',
    'debug'   => defined( 'WP_DEBUG' ) && WP_DEBUG,
);
