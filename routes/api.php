<?php
/**
 * REST API routes.
 *
 * $router is injected by AppServiceProvider — add REST endpoints here.
 * Endpoints are registered under /wp-json/{namespace}/...
 *
 * @package {{NAMESPACE}}
 *
 * Examples:
 *
 *     use {{NAMESPACE}}\Http\Controllers\OrderController;
 *
 *     $router->rest( '{{TEXT_DOMAIN}}/v1', function ( $r ) {
 *         $r->get(    '/orders',              [ OrderController::class, 'index'   ] );
 *         $r->post(   '/orders',              [ OrderController::class, 'store'   ] );
 *         $r->get(    '/orders/(?P<id>\d+)',  [ OrderController::class, 'show'    ] );
 *         $r->put(    '/orders/(?P<id>\d+)',  [ OrderController::class, 'update'  ] );
 *         $r->delete( '/orders/(?P<id>\d+)',  [ OrderController::class, 'destroy' ] );
 *     } );
 */

declare(strict_types=1);
