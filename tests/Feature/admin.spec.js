// @ts-check
import { test, expect } from '@playwright/test';

const slug    = process.env.PLUGIN_SLUG;
const wpUser  = process.env.WP_USER  || 'admin';
const wpPass  = process.env.WP_PASS  || 'password';

async function loginToWordPress( page ) {
    await page.goto( '/wp-login.php' );
    await page.fill( '#user_login', wpUser );
    await page.fill( '#user_pass', wpPass );
    await page.click( '#wp-submit' );
    await page.waitForURL( /wp-admin/ );
}

test.describe( 'Plugin admin page', () => {
    test.beforeEach( async ( { page } ) => {
        await loginToWordPress( page );
    } );

    test( 'plugin menu item is visible', async ( { page } ) => {
        await expect( page.locator( `a[href*="${slug}"]` ).first() ).toBeVisible();
    } );

    test( 'React app mounts on plugin page', async ( { page } ) => {
        await page.goto( `/wp-admin/admin.php?page=${slug}` );
        const app = page.locator( `#${slug}-app` );
        await expect( app ).toBeVisible();
    } );

    test( 'no JavaScript console errors on plugin page', async ( { page } ) => {
        const errors = /** @type {string[]} */ ( [] );
        page.on( 'pageerror', err => errors.push( err.message ) );
        await page.goto( `/wp-admin/admin.php?page=${slug}` );
        await page.waitForTimeout( 500 );
        expect( errors ).toHaveLength( 0 );
    } );
} );
