<?php
/**
 * Sanitize and render Elementor WYSIWYG field output.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers for rich text (WYSIWYG) widget fields.
 */
final class Rich_Text {

	/**
	 * @param string $html Raw HTML from Elementor WYSIWYG control.
	 * @return string Sanitized HTML safe for front-end output.
	 */
	public static function sanitize( string $html ): string {
		return wp_kses_post( $html );
	}

	/**
	 * @param string $html Raw HTML from Elementor WYSIWYG control.
	 * @return bool True when there is no visible text content.
	 */
	public static function is_empty( string $html ): bool {
		return '' === trim( wp_strip_all_tags( $html ) );
	}

	/**
	 * @param string $html Raw HTML from Elementor WYSIWYG control.
	 * @return void
	 */
	public static function echo_html( string $html ): void {
		$html = self::sanitize( $html );
		if ( self::is_empty( $html ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized via wp_kses_post.
		echo $html;
	}
}
