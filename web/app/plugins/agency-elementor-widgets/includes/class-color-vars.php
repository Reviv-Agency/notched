<?php
/**
 * Resolve Elementor colour-control values (plain hex OR global colour) into a
 * CSS value safe to inline on a widget wrapper as a custom property.
 *
 * Why this exists: Elementor's front-end CSS generator silently DROPS
 * global-bound values when a custom widget control assigns them via `selectors`
 * (whether to a real property or to a CSS variable). The result is the classic
 * "colour shows in the editor, not on the live page" bug. The reliable fix is
 * for each widget's render() to emit the resolved value as an inline CSS var on
 * the wrapper:
 *   - plain hex  → emit the hex verbatim (e.g. `#AA7D44`)
 *   - global     → emit `var(--e-global-color-<id>)`, which the active kit's
 *                  stylesheet defines site-wide (and which Elementor keeps in
 *                  the kit CSS even when it drops the per-widget rule).
 *
 * The matching control `selectors` still assign the same var so the EDITOR live
 * preview updates on every change; this inline value is the LIVE-page guarantee.
 * Inline style wins on the wrapper by specificity, so the two never conflict.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;

/**
 * Build inline CSS-variable strings from colour controls, global-aware.
 */
final class Color_Vars {

	/**
	 * Build a `--var:value;…` string for a wrapper `style` attribute.
	 *
	 * @param Widget_Base          $widget The widget (for raw __globals__ access).
	 * @param array<string, mixed> $settings get_settings_for_display() output.
	 * @param array<string, string> $map      setting_key => css_var_name.
	 * @return string e.g. `--aew-x-bg:var(--e-global-color-notched-cards);--aew-x-text:#FFF;`
	 */
	public static function build( Widget_Base $widget, array $settings, array $map ): string {
		$globals = [];
		// Raw settings carry the __globals__ sub-array (resolved settings do not).
		$raw = method_exists( $widget, 'get_settings' ) ? $widget->get_settings() : [];
		if ( is_array( $raw ) && ! empty( $raw['__globals__'] ) && is_array( $raw['__globals__'] ) ) {
			$globals = $raw['__globals__'];
		}

		$out = '';
		foreach ( $map as $key => $var ) {
			$value = self::resolve( $key, $settings, $globals );
			if ( '' !== $value ) {
				$out .= $var . ':' . $value . ';';
			}
		}
		return $out;
	}

	/**
	 * Resolve a single colour control to a CSS value (global var or hex).
	 *
	 * @param string                $key      Control id.
	 * @param array<string, mixed>  $settings Resolved settings.
	 * @param array<string, string> $globals  Raw __globals__ map.
	 * @return string CSS value, or '' when nothing is set.
	 */
	private static function resolve( string $key, array $settings, array $globals ): string {
		// Global binding wins — extract the colour id and reference the kit var.
		if ( ! empty( $globals[ $key ] ) ) {
			$id = self::global_id( (string) $globals[ $key ] );
			if ( '' !== $id ) {
				return 'var(--e-global-color-' . $id . ')';
			}
		}

		$val = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';
		return $val;
	}

	/**
	 * Pull the colour id out of a global ref like
	 * `globals/colors?id=notched-cards` → `notched-cards`.
	 *
	 * @param string $ref Global reference string.
	 * @return string Sanitised id, or ''.
	 */
	private static function global_id( string $ref ): string {
		if ( false === strpos( $ref, 'id=' ) ) {
			return '';
		}
		$id = substr( $ref, strpos( $ref, 'id=' ) + 3 );
		$id = trim( $id );
		// Ids are slugs (letters, digits, hyphen, underscore). Strip anything else.
		return preg_replace( '/[^A-Za-z0-9_-]/', '', $id ) ?? '';
	}
}
