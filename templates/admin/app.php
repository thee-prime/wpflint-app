<?php
/**
 * Admin page template — React app mount point.
 *
 * Loaded by MenuServiceProvider via AdminPage::render().
 * The React app mounts into #{{TEXT_DOMAIN}}-app automatically.
 *
 * @package {{NAMESPACE}}
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div id="{{TEXT_DOMAIN}}-app"></div>
</div>
