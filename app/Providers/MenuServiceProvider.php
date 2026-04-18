<?php
/**
 * Registers all admin menu pages and subpages.
 *
 * @package {{NAMESPACE}}\Providers
 */

declare(strict_types=1);

namespace {{NAMESPACE}}\Providers;

use {{NAMESPACE}}\WPFlint\Admin\AdminPage;
use {{NAMESPACE}}\WPFlint\Providers\ServiceProvider;

/**
 * Define every top-level menu page and its subpages here.
 *
 * boot() fires on WordPress 'init'. Menus are hooked on 'admin_menu' inside.
 */
class MenuServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function register(): void {}

    /**
     * Hook admin menus into WordPress.
     *
     * @return void
     */
    public function boot(): void {
        add_action( 'admin_menu', array( $this, 'register_menus' ) );
    }

    /**
     * Build and register all admin menu pages.
     *
     * @return void
     */
    public function register_menus(): void {
        AdminPage::make(
            __( '{{PLUGIN_NAME}}', '{{TEXT_DOMAIN}}' ),
            '{{TEXT_DOMAIN}}'
        )
        ->icon( 'dashicons-admin-generic' )
        ->position( 80 )
        ->render(
            function () {
                echo '<div class="wrap"><h1>'
                    . esc_html( get_admin_page_title() )
                    . '</h1></div>';
            }
        )
        // Uncomment to add subpages:
        // ->submenu(
        //     __( 'Settings', '{{TEXT_DOMAIN}}' ),
        //     '{{TEXT_DOMAIN}}-settings',
        //     function () {
        //         echo '<div class="wrap"><h1>'
        //             . esc_html__( 'Settings', '{{TEXT_DOMAIN}}' )
        //             . '</h1></div>';
        //     }
        // )
        ->register();
    }
}
