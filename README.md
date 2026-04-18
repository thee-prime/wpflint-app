# WPFlint Plugin Skeleton

The official project skeleton for [WPFlint](https://github.com/thee-prime/wpflint) — a Laravel-inspired framework for WordPress plugins.

## Quick Start

```bash
composer create-project thee-prime/wpflint-app my-shop-plugin
```

The setup wizard runs automatically and asks you:

```
Plugin name        : My Shop
Plugin slug        [my-shop]:
PHP namespace      [MyShop]:
Text domain        [my-shop]:
Description        [A WPFlint-powered WordPress plugin.]:
Author             :
```

Then it:
- Generates `my-shop.php` with the correct WordPress plugin header
- Stamps your namespace everywhere (`MyShop\Providers\AppServiceProvider`, etc.)
- Runs [Strauss](https://github.com/BrianHenryIE/strauss) to prefix the framework under your namespace (`MyShop\WPFlint\...`) — **zero conflict** when multiple WPFlint plugins are active
- Optionally sets up the Claude Code MCP server for AI-assisted development

## What you get

```
my-shop-plugin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Events/
│   ├── Jobs/
│   │   └── ExampleJob.php
│   ├── Listeners/
│   ├── Models/
│   ├── Providers/
│   │   └── AppServiceProvider.php   ← start here
│   └── Rules/
├── config/
│   └── app.php
├── database/
│   └── migrations/
├── routes/
│   ├── ajax.php                     ← wp_ajax_* actions
│   └── api.php                      ← REST API endpoints
├── templates/
├── vendor-prefixed/                 ← Strauss output (namespace-isolated framework)
├── my-shop.php                      ← main plugin file
└── uninstall.php
```

## Routes

### AJAX routes — `routes/ajax.php`

```php
use MyShop\Http\Controllers\OrderController;

// Logged-in users only:
$router->ajax( 'my-shop/save-order', [ OrderController::class, 'store' ] )
       ->middleware( [ 'nonce:my_shop_save_order', 'can:edit_posts' ] );

// Public endpoint (guests + logged-in):
$router->ajax( 'my-shop/get-prices', [ OrderController::class, 'prices' ] )
       ->nopriv();
```

### REST API routes — `routes/api.php`

```php
use MyShop\Http\Controllers\OrderController;

$router->rest( 'my-shop/v1', function ( $r ) {
    $r->get(    '/orders',             [ OrderController::class, 'index'   ] );
    $r->post(   '/orders',             [ OrderController::class, 'store'   ] );
    $r->get(    '/orders/(?P<id>\d+)', [ OrderController::class, 'show'    ] );
    $r->put(    '/orders/(?P<id>\d+)', [ OrderController::class, 'update'  ] );
    $r->delete( '/orders/(?P<id>\d+)', [ OrderController::class, 'destroy' ] );
} );
```

## Admin menus

Registered in `AppServiceProvider::register_menus()` via the `AdminPage` fluent builder:

```php
AdminPage::make( __( 'My Shop', 'my-shop' ), 'my-shop' )
    ->icon( 'dashicons-cart' )
    ->position( 56 )
    ->render( function () { include plugin_dir_path( __FILE__ ) . 'templates/main.php'; } )
    ->submenu( __( 'Settings', 'my-shop' ), 'my-shop-settings', function () {
        include plugin_dir_path( __FILE__ ) . 'templates/settings.php';
    } )
    ->register();
```

## Background jobs (queue)

1. Uncomment `QueueServiceProvider` in `AppServiceProvider::register()`.
2. Run `wp wpflint migrate` to create the jobs tables.
3. Dispatch jobs anywhere in your plugin:

```php
use MyShop\Jobs\ExampleJob;

wpflint_dispatch( new ExampleJob( $user_id ) );

// Delayed (runs after 60 s):
wpflint_dispatch( ( new ExampleJob( $user_id ) )->delay( 60 ) );

// Different queue, max 5 attempts:
wpflint_dispatch( ( new ExampleJob( $user_id ) )->on_queue( 'emails' )->tries( 5 ) );
```

## WP-CLI commands

All generator commands are available inside WordPress via WP-CLI:

```bash
wp wpflint make:controller  OrderController
wp wpflint make:controller  OrderController --rest
wp wpflint make:model       Order
wp wpflint make:model       Order --migration
wp wpflint make:migration   create_orders_table
wp wpflint make:event       OrderPlaced
wp wpflint make:listener    SendConfirmation
wp wpflint make:middleware  EnsureStoreIsOpen
wp wpflint make:request     StoreOrderRequest
wp wpflint make:rule        PhoneNumber
wp wpflint make:provider    OrderServiceProvider
wp wpflint make:command     ProcessOrdersCommand
wp wpflint migrate
wp wpflint cache:clear
```

## Why Strauss?

Without Strauss, two plugins both using WPFlint would produce a PHP fatal error (`Cannot redeclare class WPFlint\Application`). Strauss copies the framework into `vendor-prefixed/` and rewrites all namespaces to be scoped under your plugin:

```
WPFlint\Application  →  MyShop\WPFlint\Application
WPFlint\Http\Router  →  MyShop\WPFlint\Http\Router
```

Both plugins load their own isolated copy. No conflict, ever.

## After setup

```bash
cd my-shop-plugin

# Copy to WordPress:
cp -r . /path/to/wp-content/plugins/my-shop/

# Or symlink for development:
ln -s $(pwd) /path/to/wp-content/plugins/my-shop
```

Activate **My Shop** in the WordPress admin and start building.

## Requirements

- PHP 7.4+
- WordPress 5.9+
- Composer 2.x

## License

MIT
