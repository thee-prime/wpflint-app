<?php
/**
 * PHPUnit bootstrap — initialises Brain\Monkey and defines WordPress constants.
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
require_once dirname( __DIR__ ) . '/vendor-prefixed/autoload.php';

defined( 'ABSPATH' )           || define( 'ABSPATH', '/tmp/wordpress/' );
defined( 'WPINC' )             || define( 'WPINC', 'wp-includes' );
defined( 'MINUTE_IN_SECONDS' ) || define( 'MINUTE_IN_SECONDS', 60 );
defined( 'HOUR_IN_SECONDS' )   || define( 'HOUR_IN_SECONDS', 3600 );
defined( 'DAY_IN_SECONDS' )    || define( 'DAY_IN_SECONDS', 86400 );
defined( 'WEEK_IN_SECONDS' )   || define( 'WEEK_IN_SECONDS', 604800 );
