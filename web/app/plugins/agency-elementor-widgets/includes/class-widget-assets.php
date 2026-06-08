<?php
/**
 * Per-widget asset registration and path helpers.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Registers CSS/JS per widget under assets/widgets/{slug}/.
 */
final class Widget_Assets {

	/**
	 * @var array<string, array{style?: string, script?: string, style_deps?: array<int, string>, script_deps?: array<int, string>}>
	 */
	private static array $widgets = [];

	/**
	 * @param string               $slug   Widget folder slug (e.g. header).
	 * @param array<string, mixed> $config style, script, style_deps, script_deps paths relative to widget folder.
	 * @return void
	 */
	public static function register_widget( string $slug, array $config ): void {
		self::$widgets[ $slug ] = $config;
	}

	/**
	 * @return void
	 */
	public static function register_defaults(): void {
		self::register_widget(
			'header',
			[
				'style'      => 'css/header.css',
				'script'     => 'js/header.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'header-v2',
			[
				'style'      => 'css/header-v2.css',
				'script'     => 'js/header-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'hero',
			[
				'style'      => 'css/hero.css',
				'script'     => 'js/hero.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'hero-v2',
			[
				'style'      => 'css/hero-v2.css',
				'script'     => 'js/hero-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'icon-cards',
			[
				'style'      => 'css/icon-cards.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'heading-band',
			[
				'style'      => 'css/heading-band.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'feature-rows',
			[
				'style'      => 'css/feature-rows.css',
				'script'     => 'js/feature-rows.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'split-media',
			[
				'style'      => 'css/split-media.css',
				'script'     => 'js/split-media.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'faq-accordion',
			[
				'style'      => 'css/faq-accordion.css',
				'script'     => 'js/faq-accordion.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'card-row',
			[
				'style'      => 'css/card-row.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'cta-band',
			[
				'style'      => 'css/cta-band.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'testimonial-grid',
			[
				'style'      => 'css/testimonial-grid.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'featured-image',
			[
				'style'      => 'css/featured-image.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'process-steps',
			[
				'style'      => 'css/process-steps.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'media-cta',
			[
				'style'      => 'css/media-cta.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'overlay-banner',
			[
				'style'      => 'css/overlay-banner.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'footer',
			[
				'style'      => 'css/footer.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'footer-v2',
			[
				'style'      => 'css/footer-v2.css',
				'script'     => 'js/footer-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'sticky-image',
			[
				'style'      => 'css/sticky-image.css',
				'script'     => 'js/sticky-image.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'products-slider-v2',
			[
				'style'      => 'css/products-slider-v2.css',
				'script'     => 'js/products-slider-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'benefits-v2',
			[
				'style'      => 'css/benefits-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'region-cards-v2',
			[
				'style'      => 'css/region-cards-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'booking-cards-v2',
			[
				'style'      => 'css/booking-cards-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'crew-collage-v2',
			[
				'style'      => 'css/crew-collage-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'contact-regions-v2',
			[
				'style'      => 'css/contact-regions-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'team-grid-v2',
			[
				'style'      => 'css/team-grid-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'image-cta-band-v2',
			[
				'style'      => 'css/image-cta-band-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'values-grid-v2',
			[
				'style'      => 'css/values-grid-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'testimonials-v2',
			[
				'style'      => 'css/testimonials-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'consultation-form-v2',
			[
				'style'      => 'css/consultation-form-v2.css',
				'script'     => 'js/consultation-form-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'parallax-image-v2',
			[
				'style'      => 'css/parallax-image-v2.css',
				'script'     => 'js/parallax-image-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'welcome-v2',
			[
				'style'      => 'css/welcome-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'feature-rows-v2',
			[
				'style'      => 'css/feature-rows-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'cta-banner-v2',
			[
				'style'      => 'css/cta-banner-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'faq-v2',
			[
				'style'      => 'css/faq-v2.css',
				'script'     => 'js/faq-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'post-archive-v2',
			[
				'style'      => 'css/post-archive-v2.css',
				'script'     => 'js/post-archive-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'single-post-v2',
			[
				'style'      => 'css/single-post-v2.css',
				'script'     => 'js/single-post-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'recent-posts-v2',
			[
				'style'      => 'css/recent-posts-v2.css',
				'script'     => 'js/recent-posts-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'comments-v2',
			[
				'style'       => 'css/comments-v2.css',
				'script'      => 'js/comments-v2.js',
				'style_deps'  => [ 'aew-tokens' ],
				'script_deps' => [ 'comment-reply' ],
			]
		);

		self::register_widget(
			'gallery-v2',
			[
				'style'      => 'css/gallery-v2.css',
				'script'     => 'js/gallery-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'banner-hero-v2',
			[
				'style'      => 'css/banner-hero-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'info-columns-v2',
			[
				'style'      => 'css/info-columns-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'icon-grid-v2',
			[
				'style'      => 'css/icon-grid-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'feature-band-v2',
			[
				'style'      => 'css/feature-band-v2.css',
				'script'     => 'js/feature-band-v2.js',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'numbered-features-v2',
			[
				'style'      => 'css/numbered-features-v2.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'benefits-card',
			[
				'style'      => 'css/benefits-card.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);

		self::register_widget(
			'job-listings',
			[
				'style'      => 'css/job-listings.css',
				'style_deps' => [ 'aew-tokens' ],
			]
		);
	}

	/**
	 * @return void
	 */
	public static function register_handles(): void {
		foreach ( self::$widgets as $slug => $config ) {
			$handle = self::handle( $slug );
			$base_url = self::base_url( $slug );
			$base_dir = self::base_dir( $slug );

			if ( ! empty( $config['style'] ) ) {
				$style_file = $base_dir . $config['style'];
				if ( is_readable( $style_file ) ) {
					wp_register_style(
						$handle,
						$base_url . $config['style'],
						$config['style_deps'] ?? [ 'aew-tokens' ],
						AEW_VERSION
					);
				}
			}

			if ( ! empty( $config['script'] ) ) {
				$script_file = $base_dir . $config['script'];
				if ( is_readable( $script_file ) ) {
					wp_register_script(
						$handle,
						$base_url . $config['script'],
						$config['script_deps'] ?? [],
						AEW_VERSION,
						true
					);
				}
			}
		}
	}

	/**
	 * @param string $slug Widget folder slug.
	 * @return string Style/script handle.
	 */
	public static function handle( string $slug ): string {
		return 'aew-widget-' . $slug;
	}

	/**
	 * @param string $slug     Widget folder slug.
	 * @param string $relative Path inside the widget assets folder.
	 * @return string Public URL.
	 */
	public static function url( string $slug, string $relative = '' ): string {
		$relative = ltrim( $relative, '/' );

		return self::base_url( $slug ) . $relative;
	}

	/**
	 * @param string $slug     Widget folder slug.
	 * @param string $relative Path inside the widget assets folder.
	 * @return string Filesystem path.
	 */
	public static function path( string $slug, string $relative = '' ): string {
		$relative = ltrim( $relative, '/' );

		return self::base_dir( $slug ) . $relative;
	}

	/**
	 * @param string $slug Widget folder slug.
	 * @return string
	 */
	private static function base_url( string $slug ): string {
		return AEW_PLUGIN_URL . 'assets/widgets/' . $slug . '/';
	}

	/**
	 * @param string $slug Widget folder slug.
	 * @return string
	 */
	private static function base_dir( string $slug ): string {
		return AEW_PLUGIN_DIR . 'assets/widgets/' . $slug . '/';
	}
}
