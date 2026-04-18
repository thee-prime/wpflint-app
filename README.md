# WPFlint Plugin Skeleton

The official project skeleton for [WPFlint](https://github.com/thee-prime/wpflint) — a Laravel-inspired framework for WordPress plugins.

## Quick Start

```bash
composer create-project wpflint/wpflint my-shop-plugin
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
│   ├── Models/
│   ├── Providers/
│   │   └── AppServiceProvider.php   ← start here
│   ├── Events/
│   ├── Listeners/
│   ├── Jobs/
│   └── Rules/
├── config/
│   └── app.php
├── database/
│   └── migrations/
├── templates/
├── vendor-prefixed/                 ← Strauss output (namespace-isolated framework)
├── my-shop.php                      ← main plugin file
└── uninstall.php
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

Activate **My Shop** in the WordPress admin and start building in `app/Providers/AppServiceProvider.php`.

## Framework docs

→ [https://github.com/thee-prime/wpflint](https://github.com/thee-prime/wpflint)

## Requirements

- PHP 7.4+
- WordPress 5.9+
- Composer 2.x

## License

MIT
