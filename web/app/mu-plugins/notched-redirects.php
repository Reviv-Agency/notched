<?php
/**
 * Plugin Name: Notched Redirects
 * Description: Site-specific 301 redirects (single-file mu-plugin, always loaded).
 *
 * @package Notched
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redirect the default WooCommerce shop archive to the custom /shop-kits page.
 *
 * The default Shop PAGE was removed (we use the Elementor /shop-kits page as the
 * real shop), but WooCommerce still registers a product archive at /shop. Send
 * that — and any bare /shop request — to /shop-kits with a 301.
 *
 * Single product URLs (/shop-kits/<product> per the product base, or wherever
 * Woo resolves them) are left untouched; only the /shop archive root redirects.
 */
add_action(
	'template_redirect',
	static function (): void {
		// Match the WooCommerce product archive (the /shop route) only.
		$is_shop_archive = function_exists( 'is_shop' ) && is_shop();

		// Also catch a literal /shop (or /shop/) request path as a fallback.
		$path           = trim( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?? '', '/' );
		$is_shop_path   = ( 'shop' === $path );

		if ( $is_shop_archive || $is_shop_path ) {
			wp_safe_redirect( home_url( '/shop-kits/' ), 301 );
			exit;
		}
	},
	1
);
