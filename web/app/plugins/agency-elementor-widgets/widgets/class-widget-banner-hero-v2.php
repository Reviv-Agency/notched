<?php
/**
 * Banner Hero V2 — Notched brand.
 *
 * A boxed page-top banner (NOT full-bleed): a large rounded banner image inside
 * a padded inner wrapper, with a centered floating card overlaying it. The card
 * holds an eyebrow/subtext line, a big Teko heading, a sub-line paragraph and a
 * row of two buttons (primary solid + secondary outline with optional phone
 * icon).
 *
 * Follows the V2 conventions (see WIDGET-V2-BUILD-GUIDE.md): one inner wrapper
 * for max-width + responsive X padding, the §6.8 colour-var + render() pattern
 * for every colour control, _padding re-pointed to the inner wrapper.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Banner_Hero_V2 extends Widget_Base {

	private const ASSET_SLUG = 'banner-hero-v2';

	public function get_name(): string      { return 'agency-banner-hero-v2'; }
	public function get_title(): string     { return esc_html__( 'Banner Hero V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-image-box'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'banner', 'hero', 'shop', 'notched' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its page background while sidebar padding controls behave
	 * as expected. Defaults are left EMPTY on purpose so the stylesheet owns the
	 * responsive X padding (§6.5 / gotcha #16).
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-bhero__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_content();
		$this->controls_buttons();

		$this->style_frame();
		$this->style_card();
		$this->style_heading();
		$this->style_subtext();
		$this->style_buttons();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'image', [
			'label'   => esc_html__( 'Banner image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$this->add_control( 'subtext_position', [
			'label'   => esc_html__( 'Subtext position', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'below',
			'options' => [
				'above' => esc_html__( 'Above heading', 'agency-elementor-widgets' ),
				'below' => esc_html__( 'Below heading', 'agency-elementor-widgets' ),
			],
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'SHOP KITS', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading HTML tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h1',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
			],
		] );

		$this->add_control( 'subtext', [
			'label'   => esc_html__( 'Subtext', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'If you can dream it, Notched can do it.', 'agency-elementor-widgets' ),
			'rows'    => 3,
		] );

		$this->end_controls_section();
	}

	private function controls_buttons(): void {
		$this->start_controls_section( 's_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'primary_text', [
			'label'   => esc_html__( 'Primary button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Get a Custom Quote', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'primary_link', [
			'label'   => esc_html__( 'Primary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'show_secondary', [
			'label'        => esc_html__( 'Show secondary button', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'separator'    => 'before',
			'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
			'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
			'return_value' => 'yes',
		] );

		$this->add_control( 'secondary_text', [
			'label'     => esc_html__( 'Secondary button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( '801.410.4255', 'agency-elementor-widgets' ),
			'condition' => [ 'show_secondary' => 'yes' ],
		] );

		$this->add_control( 'secondary_link', [
			'label'     => esc_html__( 'Secondary button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => 'tel:8014104255' ],
			'condition' => [ 'show_secondary' => 'yes' ],
		] );

		$this->add_control( 'secondary_icon', [
			'label'        => esc_html__( 'Show phone icon', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'condition'    => [ 'show_secondary' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ──────────────────────────────────────────────────────

	private function style_frame(): void {
		$this->start_controls_section( 'ss_frame', [ 'label' => esc_html__( 'Frame', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'page_bg', [
			'label'     => esc_html__( 'Page background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-page-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'frame_min_height', [
			'label'      => esc_html__( 'Frame min height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [
				'px' => [ 'min' => 280, 'max' => 1000 ],
				'vh' => [ 'min' => 30, 'max' => 100 ],
			],
			'default'        => [ 'unit' => 'px', 'size' => 640 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 420 ],
			'selectors'      => [ '{{WRAPPER}} .aew-bhero__frame' => 'min-height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'frame_radius', [
			'label'      => esc_html__( 'Frame border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 64 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bhero__frame' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-card-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'card_radius', [
			'label'      => esc_html__( 'Card border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 20 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bhero__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'card_max_width', [
			'label'      => esc_html__( 'Card max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 360, 'max' => 800 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 560 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bhero__card' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-bhero__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 64 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_subtext(): void {
		$this->start_controls_section( 'ss_subtext', [ 'label' => esc_html__( 'Subtext', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'subtext_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-subtext: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtext_typo',
			'selector' => '{{WRAPPER}} .aew-bhero__subtext',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.4 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_buttons(): void {
		$this->start_controls_section( 'ss_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Primary (solid) colour controls — assign to wrapper CSS vars; render()
		// re-emits the resolved value inline so global-bound colours survive on
		// the live page (gotcha #19). The stylesheet consumes each with a token
		// fallback.
		$this->add_control( 'pri_heading', [
			'label' => esc_html__( 'Primary button', 'agency-elementor-widgets' ),
			'type'  => Controls_Manager::HEADING,
		] );
		$this->add_control( 'pri_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-pri-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_text', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-pri-text: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-pri-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-pri-text-hover: {{VALUE}};' ],
		] );

		// Secondary (outline) colour controls.
		$this->add_control( 'sec_heading', [
			'label'     => esc_html__( 'Secondary button (outline)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'sec_color', [
			'label'     => esc_html__( 'Border & text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-sec-color: {{VALUE}};' ],
		] );
		$this->add_control( 'sec_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-sec-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'sec_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bhero-sec-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'label'     => esc_html__( 'Button typography', 'agency-elementor-widgets' ),
			'selector'  => '{{WRAPPER}} .aew-bhero__btn',
			'separator' => 'before',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_control( 'btn_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 32 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bhero__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$heading   = (string) ( $s['heading'] ?? '' );
		$subtext   = (string) ( $s['subtext'] ?? '' );
		$image_url = isset( $s['image']['url'] ) ? (string) $s['image']['url'] : '';

		// Bail only when there is nothing visual to show.
		if ( '' === $heading && '' === $image_url ) {
			return;
		}

		$allowed_tags = [ 'h1', 'h2' ];
		$tag          = in_array( $s['heading_tag'] ?? 'h1', $allowed_tags, true ) ? $s['heading_tag'] : 'h1';

		$sub_above = 'above' === ( $s['subtext_position'] ?? 'below' );

		$primary_text = (string) ( $s['primary_text'] ?? '' );
		$primary      = $this->parse_link( $s['primary_link'] ?? [] );
		$has_primary  = '' !== $primary_text && '' !== $primary['url'];

		$show_secondary = 'yes' === ( $s['show_secondary'] ?? 'yes' );
		$secondary_text = (string) ( $s['secondary_text'] ?? '' );
		$secondary      = $this->parse_link( $s['secondary_link'] ?? [] );
		$has_secondary  = $show_secondary && '' !== $secondary_text && '' !== $secondary['url'];
		$sec_icon       = 'yes' === ( $s['secondary_icon'] ?? 'yes' );

		$image_alt = isset( $s['image']['alt'] ) ? (string) $s['image']['alt'] : '';
		$image_id  = isset( $s['image']['id'] ) ? (int) $s['image']['id'] : 0;
		if ( '' === $image_alt && $image_id > 0 ) {
			$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-bhero' );
		$this->add_render_attribute( 'wrapper', 'data-aew-banner-hero-v2', '' );

		// Emit resolved colours as inline CSS vars on the wrapper (live-page
		// guarantee for global-bound picks — see guide §6.8 / gotcha #19).
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'page_bg'        => '--aew-bhero-page-bg',
				'card_bg'        => '--aew-bhero-card-bg',
				'heading_color'  => '--aew-bhero-heading',
				'subtext_color'  => '--aew-bhero-subtext',
				'pri_bg'         => '--aew-bhero-pri-bg',
				'pri_text'       => '--aew-bhero-pri-text',
				'pri_bg_hover'   => '--aew-bhero-pri-bg-hover',
				'pri_text_hover' => '--aew-bhero-pri-text-hover',
				'sec_color'      => '--aew-bhero-sec-color',
				'sec_bg_hover'   => '--aew-bhero-sec-bg-hover',
				'sec_text_hover' => '--aew-bhero-sec-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-bhero__inner">
				<div class="aew-bhero__frame">

					<?php if ( '' !== $image_url ) : ?>
						<img class="aew-bhero__image"
							src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $image_alt ); ?>"
							decoding="async"
							loading="eager" />
					<?php endif; ?>

					<div class="aew-bhero__card">
						<div class="aew-bhero__content">

							<?php if ( $sub_above && '' !== $subtext ) : ?>
								<p class="aew-bhero__subtext aew-bhero__subtext--above"><?php echo esc_html( $subtext ); ?></p>
							<?php endif; ?>

							<?php if ( '' !== $heading ) : ?>
								<<?php echo esc_html( $tag ); ?> class="aew-bhero__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
							<?php endif; ?>

							<?php if ( ! $sub_above && '' !== $subtext ) : ?>
								<p class="aew-bhero__subtext aew-bhero__subtext--below"><?php echo esc_html( $subtext ); ?></p>
							<?php endif; ?>

							<?php if ( $has_primary || $has_secondary ) : ?>
								<div class="aew-bhero__buttons">

									<?php if ( $has_primary ) : ?>
										<a class="aew-bhero__btn aew-bhero__btn--primary"
											href="<?php echo esc_url( $primary['url'] ); ?>"
											<?php echo $primary['target'] ? 'target="' . esc_attr( $primary['target'] ) . '"' : ''; ?>
											<?php echo $primary['rel'] ? 'rel="' . esc_attr( $primary['rel'] ) . '"' : ''; ?>>
											<span class="aew-bhero__btn-label"><?php echo esc_html( $primary_text ); ?></span>
										</a>
									<?php endif; ?>

									<?php if ( $has_secondary ) : ?>
										<a class="aew-bhero__btn aew-bhero__btn--secondary"
											href="<?php echo esc_url( $secondary['url'] ); ?>"
											<?php echo $secondary['target'] ? 'target="' . esc_attr( $secondary['target'] ) . '"' : ''; ?>
											<?php echo $secondary['rel'] ? 'rel="' . esc_attr( $secondary['rel'] ) . '"' : ''; ?>>
											<?php if ( $sec_icon ) : ?>
												<svg class="aew-bhero__btn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
													<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path>
												</svg>
											<?php endif; ?>
											<span class="aew-bhero__btn-label"><?php echo esc_html( $secondary_text ); ?></span>
										</a>
									<?php endif; ?>

								</div>
							<?php endif; ?>

						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * @param array<string, mixed>|string $d URL control value.
	 * @return array{url: string, target: string, rel: string}
	 */
	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) return [ 'url' => '', 'target' => '', 'rel' => '' ];
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) $r .= ' nofollow';
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}
}
