<?php
/**
 * PHPUnit bootstrap — mirrors the WPFlint framework test setup.
 *
 * Initialises WP_Mock and defines WordPress constants so unit tests
 * can run without a live WordPress installation.
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
require_once dirname( __DIR__ ) . '/vendor-prefixed/autoload.php';

// Core WordPress constants used by the framework.
defined( 'ABSPATH' )           || define( 'ABSPATH', '/tmp/wordpress/' );
defined( 'WPINC' )             || define( 'WPINC', 'wp-includes' );
defined( 'MINUTE_IN_SECONDS' ) || define( 'MINUTE_IN_SECONDS', 60 );
defined( 'HOUR_IN_SECONDS' )   || define( 'HOUR_IN_SECONDS', 3600 );
defined( 'DAY_IN_SECONDS' )    || define( 'DAY_IN_SECONDS', 86400 );
defined( 'WEEK_IN_SECONDS' )   || define( 'WEEK_IN_SECONDS', 604800 );

WP_Mock::bootstrap();
