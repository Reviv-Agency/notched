<?php
/**
 * Parallax Image V2 — Notched brand.
 *
 * Standalone scroll-parallax image band, extracted from Footer V2's forest
 * hero. As the band scrolls through the viewport the image pans horizontally
 * (right → left). Renders as a clean, self-contained band (no fade mask) so it
 * can be dropped anywhere on a page. Footer V2 is left untouched as a fallback.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Widget_Parallax_Image_V2 extends Widget_Base {

	private const ASSET_SLUG = 'parallax-image-v2';

	public function get_name(): string      { return 'agency-parallax-image-v2'; }
	public function get_title(): string     { return esc_html__( 'Parallax Image V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-image'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'parallax', 'image', 'scroll', 'notched', 'forest' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Leave every default EMPTY so
	 * the stylesheet owns responsive padding (see WIDGET-V2-BUILD-GUIDE §5/§6.5,
	 * gotcha #16). The band carries no inner padding by default, so this only
	 * matters if a user types one in the sidebar.
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-pimg__band' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_image();
		$this->style_band();
	}

	private function controls_image(): void {
		$this->start_controls_section( 's_image', [ 'label' => 'Image' ] );

		$this->add_control( 'image', [
			'label'   => 'Image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/hero-forest.avif' ) ],
		] );

		$this->add_control( 'image_alt', [
			'label'       => 'Alt text',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => 'Describe the image for screen readers.',
		] );

		$this->add_responsive_control( 'band_height', [
			'label'      => 'Height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [
				'px' => [ 'min' => 120, 'max' => 1400 ],
				'vh' => [ 'min' => 20, 'max' => 100 ],
			],
			'default'        => [ 'unit' => 'px', 'size' => 1200 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 455 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 200 ],
			'selectors'      => [ '{{WRAPPER}} .aew-pimg__band' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_band(): void {
		$this->start_controls_section( 'ss_band', [ 'label' => 'Band', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'band_bg', [
			'label'     => 'Backdrop color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'description' => 'Shown behind the image while it loads or if it does not cover the band.',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pimg-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'focus_y', [
			'label'   => 'Vertical focus',
			'type'    => Controls_Manager::SELECT,
			'default' => '75%',
			'options' => [
				'0%'   => 'Top',
				'25%'  => 'Upper',
				'50%'  => 'Center',
				'75%'  => 'Lower',
				'100%' => 'Bottom',
			],
			'description' => 'Which vertical slice of the photo stays in view as it pans.',
			'selectors' => [ '{{WRAPPER}} .aew-pimg__band' => '--aew-pimg-focus-y: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── Bottom trim: pull the next widget up over the empty bottom of the
		// image so there's no big gap between the trees and the block below. ──
		$this->start_controls_section( 'ss_trim', [ 'label' => 'Bottom trim', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'bottom_trim', [
			'label'      => 'Trim bottom',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 600 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 0 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 0 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 0 ],
			'description' => 'Pulls the next block up over the empty space below the trees. The image shifts down to keep the trees in view.',
			'selectors'  => [ '{{WRAPPER}} .aew-pimg__band' => 'margin-bottom: calc(-1 * {{SIZE}}{{UNIT}});' ],
		] );

		$this->end_controls_section();

		// ── Bottom blend: a gradient fade at the bottom of the image that blends
		// it into the colour of the section below (so a trimmed image transitions
		// smoothly instead of a hard edge). ──────────────────────────────────────
		$this->start_controls_section( 'ss_blend', [ 'label' => 'Bottom blend', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'blend_enable', [
			'label'        => 'Enable bottom blend',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => '',
			'description'  => 'Fades the bottom of the image into a colour so it blends into the section below.',
		] );

		// Colour the image fades INTO at the bottom. Assigns to a wrapper CSS var
		// (§6.8) so global-bound picks survive on the live page; render() also
		// inlines the resolved value via Color_Vars::build().
		$this->add_control( 'blend_color', [
			'label'     => 'Blend color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'condition' => [ 'blend_enable' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}' => '--aew-pimg-blend: {{VALUE}};' ],
			'description' => 'Match this to the background of the section directly below.',
		] );

		$this->add_responsive_control( 'blend_height', [
			'label'      => 'Blend height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 20, 'max' => 600 ], '%' => [ 'min' => 5, 'max' => 80 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 160 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 120 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 100 ],
			'condition'  => [ 'blend_enable' => 'yes' ],
			'selectors'  => [ '{{WRAPPER}} .aew-pimg__blend' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s   = $this->get_settings_for_display();
		$img = $s['image'] ?? [];
		$url = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
		$alt = (string) ( $s['image_alt'] ?? '' );

		// Computed media defaults don't backfill onto legacy saved instances
		// (gotcha #17) — fall back to the shipped asset if the control is empty.
		if ( '' === $url ) {
			$url = Widget_Assets::url( self::ASSET_SLUG, 'images/hero-forest.avif' );
		}

		// Resolved colours as inline CSS vars on the wrapper (§6.8) — backdrop +
		// the bottom-blend colour, so global-bound picks survive on the live page.
		$color_vars = Color_Vars::build( $this, $s, [
			'band_bg'     => '--aew-pimg-bg',
			'blend_color' => '--aew-pimg-blend',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$blend_on = 'yes' === ( $s['blend_enable'] ?? '' );

		// Bottom trim → negative margin-bottom on the band, pulling the next block
		// up over the empty space below the trees. Inlined on the band (not just
		// via `selectors`) so it applies even on cached/legacy instances whose
		// generated per-element CSS predates this control (gotcha #17).
		$trim = $s['bottom_trim']['size'] ?? '';
		$band_style = "background-image: url('" . esc_url( $url ) . "');";
		if ( '' !== (string) $trim && (float) $trim > 0 ) {
			$band_style .= ' margin-bottom: -' . (float) $trim . 'px;';
		}

		if ( '' === $url ) {
			return;
		}
		?>
		<div class="aew-pimg" data-aew-parallax-image-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value escaped via esc_attr above ?>>
			<div class="aew-pimg__band"
				role="img"
				aria-label="<?php echo esc_attr( $alt ); ?>"
				style="<?php echo esc_attr( $band_style ); ?>">
				<?php if ( $blend_on ) : ?>
					<div class="aew-pimg__blend" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
