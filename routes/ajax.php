<?php
/**
 * AJAX routes.
 *
 * $router is injected by AppServiceProvider — add wp_ajax_* actions here.
 * Call ->nopriv() to also allow non-logged-in users.
 *
 * @package {{NAMESPACE}}
 *
 * Examples:
 *
 *     use {{NAMESPACE}}\Http\Controllers\OrderController;
 *
 *     // Logged-in users only:
 *     $router->ajax( '{{TEXT_DOMAIN}}/save-order', [ OrderController::class, 'store' ] )
 *            ->middleware( [ 'nonce:{{TEXT_DOMAIN}}_save_order', 'can:edit_posts' ] );
 *
 *     // Public endpoint (logged-in + guests):
 *     $router->ajax( '{{TEXT_DOMAIN}}/get-prices', [ OrderController::class, 'prices' ] )
 *            ->nopriv();
 *
 *     // Rate-limited (60 requests per 1 minute):
 *     $router->ajax( '{{TEXT_DOMAIN}}/search', [ OrderController::class, 'search' ] )
 *            ->nopriv()
 *            ->middleware( [ 'throttle:60,1' ] );
 */

declare(strict_types=1);
