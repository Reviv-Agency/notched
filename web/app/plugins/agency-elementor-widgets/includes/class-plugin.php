<?php
/**
 * Main plugin bootstrap.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin singleton.
 */
final class Plugin {

	private static ?self $instance = null;

	/**
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		Cpt_Testimonial::init();
		Settings::init();
		Widgets_Loader::init();

		Widget_Assets::register_defaults();

		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_assets' ] );
	}

	/**
	 * @return void
	 */
	public function register_assets(): void {
		Widget_Assets::register_handles();
	}

	/**
	 * @return void
	 */
	public function enqueue_frontend_assets(): void {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		$this->enqueue_google_fonts();

		wp_enqueue_style(
			'aew-tokens',
			AEW_PLUGIN_URL . 'assets/css/tokens.css',
			[],
			AEW_VERSION
		);

		wp_add_inline_style( 'aew-tokens', Design_Tokens::inline_root_css() );
	}

	/**
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$this->enqueue_google_fonts();

		wp_enqueue_style(
			'aew-tokens-editor',
			AEW_PLUGIN_URL . 'assets/css/tokens.css',
			[],
			AEW_VERSION
		);

		wp_add_inline_style( 'aew-tokens-editor', Design_Tokens::inline_root_css() );
	}

	/**
	 * @return void
	 */
	private function enqueue_google_fonts(): void {
		$families = Design_Tokens::google_font_families();
		if ( empty( $families ) ) {
			return;
		}

		$query = [];
		foreach ( $families as $family ) {
			$query[] = 'family=' . rawurlencode( $family ) . ':wght@400;600;700';
		}

		$url = 'https://fonts.googleapis.com/css2?' . implode( '&', $query ) . '&display=swap';

		wp_enqueue_style( 'aew-google-fonts', $url, [], AEW_VERSION );
	}
}
