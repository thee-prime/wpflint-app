#!/usr/bin/env php
<?php

/**
 * WPFlint Plugin Setup Wizard.
 *
 * Runs automatically after: composer create-project wpflint/wpflint my-plugin
 * Can also be re-run manually: php install.php
 */

declare(strict_types=1);

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Print a line to stdout.
 */
function wf_out( string $line = '' ): void {
    echo $line . PHP_EOL;
}

/**
 * Print a coloured status line.
 * Colours are stripped automatically if the terminal does not support them.
 */
function wf_ok( string $msg ): void {
    $tick = wf_supports_colour() ? "\033[32m✔\033[0m" : '[ok]';
    echo '  ' . $tick . '  ' . $msg . PHP_EOL;
}

function wf_warn( string $msg ): void {
    $icon = wf_supports_colour() ? "\033[33m!\033[0m" : '[!]';
    echo '  ' . $icon . '  ' . $msg . PHP_EOL;
}

function wf_error( string $msg ): void {
    $icon = wf_supports_colour() ? "\033[31m✘\033[0m" : '[error]';
    echo '  ' . $icon . '  ' . $msg . PHP_EOL;
}

function wf_supports_colour(): bool {
    if ( DIRECTORY_SEPARATOR === '\\' ) {
        return false !== getenv( 'ANSICON' )
            || 'ON' === getenv( 'ConEmuANSI' )
            || 'xterm' === getenv( 'TERM' );
    }
    return function_exists( 'posix_isatty' ) && posix_isatty( STDOUT );
}

/**
 * Prompt the user for input, returning the trimmed answer.
 * Falls back to $default when running non-interactively (e.g. CI).
 */
function wf_ask( string $prompt, string $default = '' ): string {
    $interactive = function_exists( 'posix_isatty' ) && posix_isatty( STDIN );

    if ( $default !== '' ) {
        echo '  ' . $prompt . ' [' . $default . ']: ';
    } else {
        echo '  ' . $prompt . ': ';
    }

    if ( ! $interactive ) {
        echo $default . PHP_EOL;
        return $default;
    }

    $answer = trim( (string) fgets( STDIN ) );
    return $answer !== '' ? $answer : $default;
}

/**
 * Prompt for a yes/no answer. Returns true for yes.
 */
function wf_confirm( string $prompt, bool $default = true ): bool {
    $hint = $default ? 'Y/n' : 'y/N';
    echo '  ' . $prompt . ' [' . $hint . ']: ';

    $interactive = function_exists( 'posix_isatty' ) && posix_isatty( STDIN );
    if ( ! $interactive ) {
        echo ( $default ? 'y' : 'n' ) . PHP_EOL;
        return $default;
    }

    $answer = strtolower( trim( (string) fgets( STDIN ) ) );
    if ( $answer === '' ) {
        return $default;
    }
    return in_array( $answer, array( 'y', 'yes' ), true );
}

/**
 * Convert a string to a plugin slug (lowercase, hyphens).
 * "My Shop Plugin" → "my-shop-plugin"
 */
function wf_to_slug( string $name ): string {
    $slug = strtolower( $name );
    $slug = preg_replace( '/[^a-z0-9]+/', '-', $slug );
    $slug = trim( (string) $slug, '-' );
    return $slug;
}

/**
 * Convert a string to a PHP namespace (PascalCase, letters/digits only).
 * "My Shop Plugin" → "MyShopPlugin"
 */
function wf_to_namespace( string $name ): string {
    // Title-case each word, then strip everything that is not a letter or digit.
    $ns = str_replace( ' ', '', ucwords( $name ) );
    $ns = preg_replace( '/[^A-Za-z0-9]/', '', (string) $ns );

    // Namespace must not start with a digit.
    if ( preg_match( '/^\d/', (string) $ns ) ) {
        $ns = 'Plugin' . $ns;
    }

    return (string) $ns;
}

/**
 * Replace all {{TOKEN}} placeholders in a string.
 *
 * @param string               $content
 * @param array<string,string> $tokens
 */
function wf_replace( string $content, array $tokens ): string {
    foreach ( $tokens as $token => $value ) {
        $content = str_replace( '{{' . $token . '}}', $value, $content );
    }
    return $content;
}

/**
 * Read, replace tokens, write a file.
 *
 * @param array<string,string> $tokens
 */
function wf_process_file( string $path, array $tokens ): void {
    if ( ! file_exists( $path ) ) {
        return;
    }
    $content = (string) file_get_contents( $path );
    file_put_contents( $path, wf_replace( $content, $tokens ) );
}

/**
 * Run a shell command, capturing output.
 * Returns true on success.
 */
function wf_run( string $cmd ): bool {
    exec( $cmd . ' 2>&1', $output, $code );
    return $code === 0;
}

// ── Banner ────────────────────────────────────────────────────────────────────

$width = 58;
$border = str_repeat( '═', $width );

wf_out();
if ( wf_supports_colour() ) {
    wf_out( "\033[36m  ╔" . $border . "╗\033[0m" );
    wf_out( "\033[36m  ║\033[0m" . str_pad( '  WPFlint — Plugin Setup Wizard', $width + 2, ' ', STR_PAD_BOTH ) . "\033[36m║\033[0m" );
    wf_out( "\033[36m  ╚" . $border . "╝\033[0m" );
} else {
    wf_out( '  +' . str_repeat( '-', $width ) . '+' );
    wf_out( '  |' . str_pad( '  WPFlint — Plugin Setup Wizard', $width, ' ', STR_PAD_BOTH ) . '|' );
    wf_out( '  +' . str_repeat( '-', $width ) . '+' );
}
wf_out();

// ── Detect project root ───────────────────────────────────────────────────────

$root = dirname( __FILE__ );

// Guess a sensible default plugin name from the folder name.
$dir_name     = basename( $root );
$default_name = ucwords( str_replace( array( '-', '_' ), ' ', $dir_name ) );
if ( $default_name === '' ) {
    $default_name = 'My Plugin';
}

// ── Collect inputs ────────────────────────────────────────────────────────────

wf_out( '  Enter your plugin details. Press Enter to accept the default.' );
wf_out();

$plugin_name = wf_ask( 'Plugin name', $default_name );
if ( $plugin_name === '' ) {
    $plugin_name = $default_name;
}

$default_slug      = wf_to_slug( $plugin_name );
$default_namespace = wf_to_namespace( $plugin_name );

$plugin_slug = wf_ask( 'Plugin slug', $default_slug );
if ( $plugin_slug === '' ) {
    $plugin_slug = $default_slug;
}

$namespace   = wf_ask( 'PHP namespace', $default_namespace );
$text_domain = wf_ask( 'Text domain', $plugin_slug );
$description  = wf_ask( 'Description', 'A WPFlint-powered WordPress plugin.' );
$author       = wf_ask( 'Author', '' );
$author_uri   = wf_ask( 'Author URI', '' );
$plugin_uri   = wf_ask( 'Plugin URI', '' );

// ── Validate namespace ────────────────────────────────────────────────────────

if ( ! preg_match( '/^[A-Za-z][A-Za-z0-9]*$/', $namespace ) ) {
    wf_warn( 'Namespace "' . $namespace . '" contains invalid characters. Using "' . $default_namespace . '" instead.' );
    $namespace = $default_namespace;
}

// ── Token map ─────────────────────────────────────────────────────────────────

/** @var array<string,string> $tokens */
$tokens = array(
    'PLUGIN_NAME' => $plugin_name,
    'PLUGIN_URI'  => $plugin_uri,
    'DESCRIPTION' => $description,
    'AUTHOR'      => $author,
    'AUTHOR_URI'  => $author_uri,
    'TEXT_DOMAIN' => $text_domain,
    'NAMESPACE'   => $namespace,
);

// ── Step 1: Generate main plugin file ────────────────────────────────────────

wf_out();
wf_out( '  ' . str_repeat( '─', $width ) );

$plugin_template = $root . '/plugin.php';
$plugin_file     = $root . '/' . $plugin_slug . '.php';

if ( ! file_exists( $plugin_template ) ) {
    wf_error( 'plugin.php template not found. Aborting.' );
    exit( 1 );
}

$content = (string) file_get_contents( $plugin_template );
$content = wf_replace( $content, $tokens );
file_put_contents( $plugin_file, $content );
unlink( $plugin_template );

wf_ok( 'Created  ' . $plugin_slug . '.php' );

// ── Step 2: AppServiceProvider.php ───────────────────────────────────────────

wf_process_file( $root . '/app/Providers/AppServiceProvider.php', $tokens );
wf_ok( 'Created  app/Providers/AppServiceProvider.php' );

wf_process_file( $root . '/app/Providers/MenuServiceProvider.php', $tokens );
wf_ok( 'Created  app/Providers/MenuServiceProvider.php' );

// ── Step 3: Routes ────────────────────────────────────────────────────────────

wf_process_file( $root . '/routes/ajax.php', $tokens );
wf_ok( 'Created  routes/ajax.php' );

wf_process_file( $root . '/routes/api.php', $tokens );
wf_ok( 'Created  routes/api.php' );

// ── Step 4: Jobs ──────────────────────────────────────────────────────────────

wf_process_file( $root . '/app/Jobs/ExampleJob.php', $tokens );
wf_ok( 'Created  app/Jobs/ExampleJob.php' );

// ── Step 4b: Frontend templates ───────────────────────────────────────────────

wf_process_file( $root . '/resources/js/app.jsx', $tokens );
wf_ok( 'Created  resources/js/app.jsx' );

wf_process_file( $root . '/resources/js/components/App.jsx', $tokens );
wf_ok( 'Created  resources/js/components/App.jsx' );

wf_process_file( $root . '/resources/css/app.css', $tokens );
wf_ok( 'Created  resources/css/app.css' );

wf_process_file( $root . '/templates/admin/app.php', $tokens );
wf_ok( 'Created  templates/admin/app.php' );

wf_process_file( $root . '/vite.config.js', $tokens );
wf_ok( 'Updated  vite.config.js' );

wf_process_file( $root . '/package.json', $tokens );
wf_ok( 'Updated  package.json' );

// ── Step 4c: Tests ────────────────────────────────────────────────────────────

wf_process_file( $root . '/tests/Unit/ExampleJobTest.php', $tokens );
wf_ok( 'Created  tests/Unit/ExampleJobTest.php' );

wf_process_file( $root . '/tests/bootstrap.php', $tokens );
wf_ok( 'Updated  tests/bootstrap.php' );

wf_process_file( $root . '/.env.testing.example', $tokens );
wf_ok( 'Created  .env.testing.example' );

// ── Step 4d: Tailwind ─────────────────────────────────────────────────────────

wf_out();
wf_out( '  ' . str_repeat( '─', $width ) );

$use_tailwind = wf_confirm( 'Add Tailwind CSS for React styling?', false );

if ( $use_tailwind ) {
    // Inject Tailwind v4 CSS imports into resources/css/app.css.
    // v4 uses @import, NOT the old @tailwind directives.
    // Preflight (CSS reset) is disabled by omitting its import — safe for WP admin.
    $css_path    = $root . '/resources/css/app.css';
    $css_content = file_exists( $css_path ) ? (string) file_get_contents( $css_path ) : '';
    $tailwind_directives = "/* Tailwind v4 — preflight omitted so WP admin styles are unaffected. */\n@import \"tailwindcss/theme\" layer(theme);\n@import \"tailwindcss/utilities\" layer(utilities);\n\n";
    file_put_contents( $css_path, $tailwind_directives . $css_content );

    // Write tailwind.config.js — preflight disabled for WP admin compatibility.
    // Tailwind v4 config: content paths only. Preflight is disabled via CSS imports.
    $tailwind_cfg = <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/js/**/*.{js,jsx}',
        './templates/**/*.php',
    ],
};
JS;
    file_put_contents( $root . '/tailwind.config.js', $tailwind_cfg . PHP_EOL );

    // Replace the placeholder in vite.config.js with the actual Tailwind plugin import.
    $vite_path    = $root . '/vite.config.js';
    $vite_content = file_exists( $vite_path ) ? (string) file_get_contents( $vite_path ) : '';
    $vite_content = str_replace(
        "import react from '@vitejs/plugin-react';",
        "import react from '@vitejs/plugin-react';\nimport tailwindcss from '@tailwindcss/vite';",
        $vite_content
    );
    $vite_content = str_replace(
        '// __TAILWIND_PLUGIN__',
        'tailwindcss(),',
        $vite_content
    );
    file_put_contents( $vite_path, $vite_content );

    // Add Tailwind packages to package.json.
    $pkg_path = $root . '/package.json';
    $pkg_raw  = (string) file_get_contents( $pkg_path );
    $pkg      = json_decode( $pkg_raw, true );
    if ( is_array( $pkg ) ) {
        $pkg['devDependencies']['tailwindcss']       = '^4.0.0';
        $pkg['devDependencies']['@tailwindcss/vite'] = '^4.0.0';
        file_put_contents( $pkg_path, json_encode( $pkg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL );
    }

    wf_ok( 'Tailwind configured  (preflight disabled for WordPress admin)' );
}

// ── Step 4e: npm install ──────────────────────────────────────────────────────

if ( file_exists( $root . '/package.json' ) ) {
    echo '  ...  Installing frontend Node dependencies (npm install)' . PHP_EOL;
    $npm_ok = wf_run( 'npm install --prefix ' . escapeshellarg( $root ) . ' --silent 2>&1' );
    if ( $npm_ok ) {
        wf_ok( 'npm      node_modules installed' );
    } else {
        wf_warn( 'npm install failed. Run "npm install" manually after setup.' );
    }
}

// ── Step 5: config/app.php ────────────────────────────────────────────────────

wf_process_file( $root . '/config/app.php', $tokens );
wf_ok( 'Updated  config/app.php' );

// ── Step 6: uninstall.php ────────────────────────────────────────────────────

wf_process_file( $root . '/uninstall.php', $tokens );
wf_ok( 'Updated  uninstall.php' );

// ── Step 7: Rewrite composer.json ────────────────────────────────────────────

$composer_path = $root . '/composer.json';
$composer_raw  = (string) file_get_contents( $composer_path );
$composer      = json_decode( $composer_raw, true );

if ( ! is_array( $composer ) ) {
    wf_error( 'Could not parse composer.json. Aborting.' );
    exit( 1 );
}

// Autoload: replace App\ with real namespace.
$composer['autoload']['psr-4'] = array(
    $namespace . '\\' => 'app/',
);

// Strauss: update namespace_prefix and classmap_prefix.
$composer['extra']['strauss']['namespace_prefix'] = $namespace . '\\';
$composer['extra']['strauss']['classmap_prefix']  = $namespace . '_';

// Scripts: add Strauss to post-install-cmd and post-update-cmd.
$composer['scripts']['post-install-cmd'] = array( '@php vendor/bin/strauss' );
$composer['scripts']['post-update-cmd']  = array( '@php vendor/bin/strauss' );

// Set the package name to something project-specific.
$vendor_slug             = wf_to_slug( $author !== '' ? $author : 'vendor' );
$composer['name']        = $vendor_slug . '/' . $plugin_slug;
$composer['description'] = $description;

// Remove the post-create-project-cmd — only needed once.
unset( $composer['scripts']['post-create-project-cmd'] );

$new_json = json_encode( $composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
file_put_contents( $composer_path, $new_json );

wf_ok( 'Updated  composer.json  (namespace → ' . $namespace . '\\)' );

// ── Step 8: Run Strauss ───────────────────────────────────────────────────────

$strauss_bin = $root . '/vendor/bin/strauss';

if ( file_exists( $strauss_bin ) ) {
    echo '  ...  Running Strauss (prefixing framework namespace)' . PHP_EOL;
    // Strauss reads composer.json from CWD — must be project root.
    chdir( $root );
    $ok = wf_run( 'php ' . escapeshellarg( $strauss_bin ) );
    if ( $ok ) {
        wf_ok( 'Strauss  vendor-prefixed/ created  (' . $namespace . '\\WPFlint\\...)' );
    } else {
        wf_warn( 'Strauss failed. Run "composer strauss" manually after setup.' );
    }
} else {
    wf_warn( 'Strauss binary not found. Run "composer install" then "composer strauss".' );
}

// ── Step 9: composer dump-autoload ───────────────────────────────────────────

echo '  ...  Running composer dump-autoload' . PHP_EOL;
wf_run( 'composer dump-autoload --quiet --working-dir=' . escapeshellarg( $root ) );
wf_ok( 'Autoloader regenerated' );

// ── Step 10: MCP server ──────────────────────────────────────────────────────

wf_out();
wf_out( '  ' . str_repeat( '─', $width ) );

$setup_mcp = wf_confirm( 'Set up the AI/MCP server for Claude Code?', true );

if ( $setup_mcp ) {
    $mcp_source = $root . '/vendor/thee-prime/wpflint/mcp-server/index.js';
    $mcp_dir    = $root . '/mcp-server';
    $claude_dir = $root . '/.claude';

    // Copy mcp-server/ from vendor into project root so it stays
    // accessible even if vendor/ is excluded from version control.
    if ( is_dir( $root . '/vendor/thee-prime/wpflint/mcp-server' ) ) {
        if ( ! is_dir( $mcp_dir ) ) {
            mkdir( $mcp_dir, 0755, true );
        }

        // Copy all files from vendor mcp-server/ to project mcp-server/.
        $files = glob( $root . '/vendor/thee-prime/wpflint/mcp-server/*' );
        if ( is_array( $files ) ) {
            foreach ( $files as $file ) {
                if ( is_file( $file ) ) {
                    copy( $file, $mcp_dir . '/' . basename( $file ) );
                }
            }
        }
        wf_ok( 'Copied   mcp-server/ to project root' );

        // Install Node.js dependencies (node_modules not tracked in git).
        if ( file_exists( $mcp_dir . '/package.json' ) ) {
            echo '  ...  Installing MCP server Node dependencies (npm install)' . PHP_EOL;
            $npm_ok = wf_run( 'npm install --prefix ' . escapeshellarg( $mcp_dir ) . ' --silent 2>&1' );
            if ( $npm_ok ) {
                wf_ok( 'npm      node_modules installed in mcp-server/' );
            } else {
                wf_warn( 'npm install failed. Run "npm install" inside mcp-server/ manually.' );
            }
        }
    } elseif ( file_exists( $mcp_source ) ) {
        // Fallback: just reference the vendor path.
        $mcp_dir = dirname( $mcp_source );
        wf_ok( 'MCP server found at vendor/thee-prime/wpflint/mcp-server/' );
    } else {
        wf_warn( 'MCP server not found in vendor. Skipping MCP setup.' );
        $setup_mcp = false;
    }

    if ( $setup_mcp ) {
        // Write .claude/settings.json pointing to the copied mcp-server/.
        if ( ! is_dir( $claude_dir ) ) {
            mkdir( $claude_dir, 0755, true );
        }

        $mcp_index   = $mcp_dir . '/index.js';
        $mcp_abs     = realpath( $mcp_index ) ?: $mcp_index;
        $settings    = array(
            'mcpServers' => array(
                'wpflint' => array(
                    'command' => 'node',
                    'args'    => array( $mcp_abs ),
                ),
            ),
        );
        $settings_json = json_encode( $settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
        file_put_contents( $claude_dir . '/settings.json', $settings_json );

        wf_ok( 'Created  .claude/settings.json' );
        wf_out();
        wf_out( '  MCP is ready. In Claude Code, run:' );
        wf_out( '    wpflint_init("' . $root . '")' );
        wf_out( '  ...and all generators will use namespace: ' . $namespace );
    }
}

// ── Step 11: Self-delete ─────────────────────────────────────────────────────

// Remove install.php — it has done its job.
// We do this last so the file is present if anything above fails mid-way.
@unlink( __FILE__ );

// ── Done ──────────────────────────────────────────────────────────────────────

wf_out();
wf_out( '  ' . str_repeat( '═', $width ) );
if ( wf_supports_colour() ) {
    wf_out( "\033[32m  WPFlint is ready!\033[0m" );
} else {
    wf_out( '  WPFlint is ready!' );
}
wf_out();
wf_out( '  Next steps:' );
wf_out();
wf_out( '    1. Copy this folder into wp-content/plugins/' );
wf_out( '    2. Activate "' . $plugin_name . '" in WordPress admin' );
wf_out( '    3. Register routes in routes/ajax.php and routes/api.php' );
wf_out( '    4. Add admin menus in app/Providers/MenuServiceProvider.php' );
wf_out( '    5. Frontend (React + Vite):' );
wf_out();
wf_out( '         npm run dev        # hot-reload dev server' );
wf_out( '         npm run build      # production build → dist/' );
wf_out( '         npm run lint       # ESLint' );
wf_out();
wf_out( '    6. PHP quality:' );
wf_out();
wf_out( '         composer test      # PHPUnit' );
wf_out( '         composer lint      # PHPCS' );
wf_out( '         composer lint:fix  # PHPCBF auto-fix' );
wf_out();
wf_out( '    7. Browser (E2E) tests:' );
wf_out();
wf_out( '         cp .env.testing.example .env.testing  # fill in WP credentials' );
wf_out( '         npx playwright install chromium' );
wf_out( '         npx playwright test' );
wf_out();
wf_out( '    8. Generate classes with WP-CLI (from your plugin folder):' );
wf_out();
wf_out( '         wp wpflint make:controller OrderController' );
wf_out( '         wp wpflint make:controller OrderController --rest' );
wf_out( '         wp wpflint make:model      Order' );
wf_out( '         wp wpflint make:model      Order --migration' );
wf_out( '         wp wpflint make:migration  create_orders_table' );
wf_out( '         wp wpflint make:event      OrderPlaced' );
wf_out( '         wp wpflint make:listener   SendConfirmation' );
wf_out( '         wp wpflint make:middleware EnsureStoreIsOpen' );
wf_out( '         wp wpflint make:request    StoreOrderRequest' );
wf_out( '         wp wpflint migrate' );
wf_out( '         wp wpflint cache:clear' );
wf_out();
wf_out( '  Docs → https://github.com/thee-prime/wpflint' );
wf_out( '  ' . str_repeat( '═', $width ) );
wf_out();
