<?php
/**
 * Sticky Image — Notched brand.
 *
 * A single image pinned to the page. Choose a position mode (Sticky /
 * Fixed / Absolute), a corner anchor, X/Y offsets and a size. Mobile gets
 * its own anchor / offset / size overrides (or can inherit desktop).
 *
 * Defaults to the 25-year structural warranty badge.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Widget_Sticky_Image extends Widget_Base {

	private const ASSET_SLUG = 'sticky-image';

	public function get_name(): string      { return 'agency-sticky-image'; }
	public function get_title(): string     { return esc_html__( 'Sticky Image (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-image'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'sticky', 'fixed', 'image', 'badge', 'float', 'warranty', 'notched' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_image();
		$this->controls_position();

		$this->style_image();
	}

	private function controls_image(): void {
		$this->start_controls_section( 's_image', [ 'label' => 'Image' ] );

		$this->add_control( 'image', [
			'label'   => 'Image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/warranty-badge.png' ) ],
		] );

		$this->add_control( 'alt', [
			'label'       => 'Alt text',
			'type'        => Controls_Manager::TEXT,
			'default'     => '25 Year Structural Warranty',
			'description' => 'Describe the image for screen readers.',
		] );

		$this->add_control( 'link', [
			'label'   => 'Link (optional)',
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '' ],
		] );

		$this->end_controls_section();
	}

	private function controls_position(): void {
		$this->start_controls_section( 's_position', [ 'label' => 'Position & visibility' ] );

		$this->add_control( 'pos_note', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => 'The badge is pinned to the screen and stays put while scrolling: <b>bottom-left on desktop</b>, <b>bottom-right on tablet &amp; mobile</b>. Use the offsets below to nudge it.',
			'content_classes' => 'elementor-descriptor',
		] );

		// Desktop edge offsets — written live via `selectors` so the editor
		// updates as you drag. Maps to bottom + left (desktop is bottom-left).
		$this->add_control( 'offset_x', [
			'label'      => 'Horizontal offset (desktop)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vw' ],
			'range'      => [ 'px' => [ 'min' => -400, 'max' => 400 ], 'vw' => [ 'min' => -50, 'max' => 50 ] ],
			'default'    => [ 'unit' => 'px', 'size' => -160 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-offx: {{SIZE}}{{UNIT}};' ],
			'description' => 'Distance from the left edge of the hero. Negative = overhang past the edge.',
		] );

		$this->add_control( 'offset_y', [
			'label'      => 'Vertical offset (desktop)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => -400, 'max' => 400 ], 'vh' => [ 'min' => -90, 'max' => 90 ] ],
			'default'    => [ 'unit' => 'px', 'size' => -60 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-offy: {{SIZE}}{{UNIT}};' ],
			'description' => 'Distance from the bottom edge of the hero. Negative = overhang below.',
		] );

		$this->add_control( 'mobile_offset_x', [
			'label'      => 'Horizontal offset (tablet/mobile)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vw' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ], 'vw' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-m-offx: {{SIZE}}{{UNIT}};' ],
			'description' => 'Distance from the right edge.',
		] );

		$this->add_control( 'mobile_offset_y', [
			'label'      => 'Vertical offset (tablet/mobile)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 300 ], 'vh' => [ 'min' => 0, 'max' => 90 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-m-offy: {{SIZE}}{{UNIT}};' ],
			'description' => 'Distance from the bottom edge.',
		] );

		$this->add_control( 'mobile_show', [
			'label'        => 'Show on tablet & mobile',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'separator'    => 'before',
		] );

		$this->add_control( 'z_index', [
			'label'   => 'Z-index (stacking)',
			'type'    => Controls_Manager::NUMBER,
			'default' => 100,
			'min'     => 0,
			'max'     => 99999,
			'selectors' => [ '{{WRAPPER}} .aew-stim' => 'z-index: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// ── STYLE ──────────────────────────────────────────────────────────────────

	private function style_image(): void {
		$this->start_controls_section( 'ss_image', [ 'label' => 'Size & appearance', 'tab' => Controls_Manager::TAB_STYLE ] );

		// Width is written as a CSS VAR on .aew-stim (not a `.aew-stim__img`
		// width rule). The badge is re-parented OUT of {{WRAPPER}} into the hero
		// on the frontend, so a `{{WRAPPER}} .aew-stim__img` rule would stop
		// matching it. The var is set here for live editor preview AND by
		// render() inline-style for the published frontend.
		$this->add_control( 'width', [
			'label'      => 'Width (desktop)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vw' ],
			'range'      => [ 'px' => [ 'min' => 40, 'max' => 800 ], 'vw' => [ 'min' => 5, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 440 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-w: {{SIZE}}{{UNIT}};' ],
			'description' => 'Drag to resize the badge. Updates live.',
		] );

		$this->add_control( 'width_mobile', [
			'label'      => 'Width (mobile)',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vw' ],
			'range'      => [ 'px' => [ 'min' => 32, 'max' => 480 ], 'vw' => [ 'min' => 10, 'max' => 70 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 260 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim' => '--aew-stim-w-mobile: {{SIZE}}{{UNIT}};' ],
			'description' => 'Applied below the tablet breakpoint (768px).',
		] );

		$this->add_control( 'opacity', [
			'label'      => 'Opacity',
			'type'       => Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'    => [ 'size' => 1 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim__img' => 'opacity: {{SIZE}};' ],
		] );

		$this->add_control( 'radius', [
			'label'      => 'Corner radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ], '%' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 0 ],
			'selectors'  => [ '{{WRAPPER}} .aew-stim__img' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'shadow', [
			'label'        => 'Drop shadow',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => '',
			'return_value' => 'yes',
			'selectors'    => [ '{{WRAPPER}} .aew-stim__img' => 'filter: drop-shadow(0 8px 24px rgba(20,28,25,.35));' ],
		] );

		$this->add_control( 'spin_heading', [
			'label'     => 'Spin on scroll',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'spin', [
			'label'        => 'Rotate the image as the page scrolls',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => '',
			'return_value' => 'yes',
			'description'  => 'Disabled automatically for visitors who prefer reduced motion.',
		] );

		$this->add_control( 'spin_speed', [
			'label'       => 'Spin speed',
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ '' ],
			'range'       => [ '' => [ 'min' => 0.1, 'max' => 3, 'step' => 0.1 ] ],
			'default'     => [ 'size' => 0.6 ],
			'description' => 'Degrees of rotation per pixel scrolled. Higher = faster spin.',
			'condition'   => [ 'spin' => 'yes' ],
		] );

		$this->add_control( 'spin_direction', [
			'label'     => 'Direction',
			'type'      => Controls_Manager::SELECT,
			'default'   => 'cw',
			'options'   => [ 'cw' => 'Clockwise', 'ccw' => 'Counter-clockwise' ],
			'condition' => [ 'spin' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$img      = $s['image'] ?? [];
		$img_url  = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
		// Fall back to the bundled warranty badge. Older saved instances may omit
		// the `image` key entirely (the computed control default is not always
		// re-merged onto legacy data), which would otherwise render nothing and
		// leave a collapsed strip on the page.
		if ( ! $img_url ) {
			$img_url = Widget_Assets::url( self::ASSET_SLUG, 'images/warranty-badge.png' );
		}
		$alt      = (string) ( $s['alt'] ?? '' );
		$link     = $this->parse_link( $s['link'] ?? [] );

		// Position is fixed: pinned to the viewport, stays put while scrolling.
		// Desktop = bottom-left, tablet/mobile = bottom-right (handled in CSS).
		// Offsets are written live by the controls' `selectors`; these are the
		// server-side fallback for the published page.
		$vars = [
			'--aew-stim-offx'    => $this->dim( $s['offset_x'] ?? [], '-160px' ),
			'--aew-stim-offy'    => $this->dim( $s['offset_y'] ?? [], '-60px' ),
			'--aew-stim-m-offx'  => $this->dim( $s['mobile_offset_x'] ?? [], '16px' ),
			'--aew-stim-m-offy'  => $this->dim( $s['mobile_offset_y'] ?? [], '16px' ),
			'--aew-stim-w'       => $this->dim( $s['width'] ?? [], '440px' ),
			'--aew-stim-w-mobile' => $this->dim( $s['width_mobile'] ?? [], '260px' ),
		];

		$mobile_show = ( 'yes' === ( $s['mobile_show'] ?? 'yes' ) );

		$style_attr = '';
		foreach ( $vars as $k => $v ) {
			$style_attr .= $k . ':' . $v . ';';
		}

		$classes = [ 'aew-stim' ];
		if ( ! $mobile_show ) {
			$classes[] = 'aew-stim--m-hidden';
		}

		$img_tag = sprintf(
			'<img class="aew-stim__img" src="%s" alt="%s" decoding="async" loading="lazy" />',
			esc_url( $img_url ),
			esc_attr( $alt )
		);

		if ( $link['url'] ) {
			$inner = sprintf(
				'<a class="aew-stim__link" href="%s"%s%s aria-label="%s">%s</a>',
				esc_url( $link['url'] ),
				$link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : '',
				$link['rel'] ? ' rel="' . esc_attr( $link['rel'] ) . '"' : '',
				esc_attr( $alt ),
				$img_tag
			);
		} else {
			$inner = $img_tag;
		}

		// Spin-on-scroll config (read by the JS).
		$spin_attr = '';
		if ( 'yes' === ( $s['spin'] ?? '' ) ) {
			$speed     = isset( $s['spin_speed']['size'] ) && '' !== $s['spin_speed']['size'] ? (float) $s['spin_speed']['size'] : 0.6;
			$direction = ( 'ccw' === ( $s['spin_direction'] ?? 'cw' ) ) ? '-1' : '1';
			$spin_attr = sprintf(
				' data-aew-stim-spin="1" data-aew-stim-spin-speed="%s" data-aew-stim-spin-dir="%s"',
				esc_attr( (string) $speed ),
				esc_attr( $direction )
			);
		}

		printf(
			'<div class="%s" data-aew-sticky-image%s style="%s">%s</div>',
			esc_attr( implode( ' ', $classes ) ),
			$spin_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_attr above.
			esc_attr( $style_attr ),
			$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url/esc_attr above.
		);
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Normalize an Elementor slider value to a CSS dimension string.
	 *
	 * @param mixed  $value    Array with 'size' and 'unit'.
	 * @param string $fallback Used when size is empty.
	 * @return string
	 */
	private function dim( $value, string $fallback ): string {
		if ( ! is_array( $value ) || '' === ( $value['size'] ?? '' ) || null === ( $value['size'] ?? null ) ) {
			return $fallback;
		}
		$unit = $value['unit'] ?? 'px';
		return $value['size'] . $unit;
	}

	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) {
			$r .= ' nofollow';
		}
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}
}
