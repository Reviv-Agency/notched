<?php
/**
 * Design tokens: defaults, settings merge, CSS variables.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Design token registry.
 */
final class Design_Tokens {

	public const OPTION_KEY = 'aew_design_tokens';

	/**
	 * @return array<string, string>
	 */
	public static function defaults(): array {
		return [
			'color_blue_dark'   => '#252F37',
			'color_blue'        => '#305B83',
			'color_blue_light'  => '#5796BB',
			'color_yellow'      => '#EBC543',
			'color_cream'       => '#F8F5F1',
			'color_white'       => '#FFFFFF',
			'color_gray'        => '#E1DEDA',
			'font_heading'      => 'DM Serif Display',
			'font_body'         => 'Poppins',
			'text_h1_size'      => '80px',
			'text_h1_lh'        => '1',
			'text_h2_size'      => '64px',
			'text_h2_lh'        => '1',
			'text_h3_size'      => '48px',
			'text_h3_lh'        => '1',
			'text_h4_size'      => '32px',
			'text_h4_lh'        => '1',
			'text_h5_size'      => '24px',
			'text_h5_lh'        => '1',
			'text_subhead_size' => '20px',
			'text_subhead_lh'   => '1',
			'text_p_size'       => '18px',
			'text_p_lh'         => '1.4',
			'text_p2_size'      => '14px',
			'text_p2_lh'        => '1.4',
		];
	}

	/**
	 * @return array<string, string>
	 */
	public static function get(): array {
		$saved = get_option( self::OPTION_KEY, [] );
		if ( ! is_array( $saved ) ) {
			$saved = [];
		}

		$tokens = array_merge( self::defaults(), array_filter( $saved, 'is_string' ) );

		/**
		 * Filter design tokens before output.
		 *
		 * @param array<string, string> $tokens
		 */
		return apply_filters( 'aew_design_tokens', $tokens );
	}

	/**
	 * @return void
	 */
	public static function seed_defaults(): void {
		if ( false !== get_option( self::OPTION_KEY, false ) ) {
			return;
		}
		update_option( self::OPTION_KEY, self::defaults(), false );
	}

	/**
	 * @return array<string, string> CSS custom properties for :root.
	 */
	public static function css_variables(): array {
		$t = self::get();

		$heading = self::font_stack( $t['font_heading'] ?? 'DM Serif Display', 'serif' );
		$body    = self::font_stack( $t['font_body'] ?? 'Poppins', 'sans-serif' );

		return [
			'--aew-color-blue-dark'   => $t['color_blue_dark'],
			'--aew-color-blue'        => $t['color_blue'],
			'--aew-color-blue-light'  => $t['color_blue_light'],
			'--aew-color-yellow'      => $t['color_yellow'],
			'--aew-color-cream'       => $t['color_cream'],
			'--aew-color-white'       => $t['color_white'],
			'--aew-color-gray'        => $t['color_gray'],
			'--aew-font-heading'      => $heading,
			'--aew-font-body'         => $body,
			'--aew-text-h1'           => $t['text_h1_size'],
			'--aew-text-h1-lh'        => $t['text_h1_lh'],
			'--aew-text-h2'           => $t['text_h2_size'],
			'--aew-text-h2-lh'        => $t['text_h2_lh'],
			'--aew-text-h3'           => $t['text_h3_size'],
			'--aew-text-h3-lh'        => $t['text_h3_lh'],
			'--aew-text-h4'           => $t['text_h4_size'],
			'--aew-text-h4-lh'        => $t['text_h4_lh'],
			'--aew-text-h5'           => $t['text_h5_size'],
			'--aew-text-h5-lh'        => $t['text_h5_lh'],
			'--aew-text-subhead'      => $t['text_subhead_size'],
			'--aew-text-subhead-lh'   => $t['text_subhead_lh'],
			'--aew-text-p'            => $t['text_p_size'],
			'--aew-text-p-lh'         => $t['text_p_lh'],
			'--aew-text-p2'           => $t['text_p2_size'],
			'--aew-text-p2-lh'        => $t['text_p2_lh'],
		];
	}

	/**
	 * @param string $family Font family name.
	 * @param string $fallback Generic fallback.
	 * @return string
	 */
	public static function font_stack( string $family, string $fallback ): string {
		$family = trim( $family );
		if ( str_contains( $family, ',' ) ) {
			return $family;
		}

		return '"' . $family . '", ' . $fallback;
	}

	/**
	 * @return string Inline :root CSS.
	 */
	public static function inline_root_css(): string {
		$vars = self::css_variables();
		$rules = [];
		foreach ( $vars as $name => $value ) {
			$rules[] = sprintf( '%s:%s', $name, $value );
		}

		return ':root{' . implode( ';', $rules ) . '}';
	}

	/**
	 * Google Fonts families to load.
	 *
	 * @return array<int, string>
	 */
	public static function google_font_families(): array {
		$t = self::get();
		$families = [];

		foreach ( [ $t['font_heading'] ?? '', $t['font_body'] ?? '' ] as $font ) {
			$font = trim( explode( ',', $font )[0], " \t\"'" );
			if ( $font !== '' ) {
				$families[] = $font;
			}
		}

		return array_values( array_unique( $families ) );
	}
}
