<?php

/**
 * Plugin uninstall handler.
 *
 * Runs when the plugin is deleted from the WordPress admin.
 * Remove ALL plugin data: tables, options, transients, user meta.
 *
 * @package {{NAMESPACE}}
 */

// Bail if not called by WordPress uninstall process.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// ── Remove plugin options ──────────────────────────────────────────────────
// delete_option( '{{TEXT_DOMAIN}}_settings' );

// ── Remove transients ─────────────────────────────────────────────────────
// delete_transient( '{{TEXT_DOMAIN}}_cache' );

// ── Drop custom tables ────────────────────────────────────────────────────
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{{TEXT_DOMAIN}}_orders" );

// ── Remove user meta ──────────────────────────────────────────────────────
// delete_metadata( 'user', 0, '{{TEXT_DOMAIN}}_preference', '', true );
