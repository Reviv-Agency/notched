<?php
/**
 * CTA Banner V2 — Notched brand.
 *
 * A centered call-to-action banner inside a large rounded card sitting on the
 * light page background: eyebrow + Teko headline + description + single CTA
 * button (with optional arrow icon).
 *
 * Mirrors the Hero V2 / Footer V2 conventions (see WIDGET-V2-BUILD-GUIDE.md):
 * full-bleed outer block, one inner wrapper for max-width + responsive X
 * padding, the §6.8 colour-var + render() pattern for every colour control.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Cta_Banner_V2 extends Widget_Base {

	private const ASSET_SLUG = 'cta-banner-v2';

	public function get_name(): string      { return 'agency-cta-banner-v2'; }
	public function get_title(): string     { return esc_html__( 'CTA Banner V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-call-to-action'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'cta', 'banner', 'call to action', 'notched', 'consultation' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background while sidebar padding controls
	 * behave as expected. Defaults are left EMPTY on purpose — a non-empty default
	 * emits a single non-responsive rule that clobbers the stylesheet at every
	 * breakpoint (see guide §5 / gotcha #16). The stylesheet owns responsive X
	 * padding (§6.5).
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-ctab__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_button();

		$this->style_card();
		$this->style_eyebrow();
		$this->style_headline();
		$this->style_description();
		$this->style_button();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'eyebrow', [
			'label'   => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'WHY WAIT?', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'headline', [
			'label'   => esc_html__( 'Headline', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Build Your Dream Space Today!', 'agency-elementor-widgets' ),
			'rows'    => 2,
		] );

		$this->add_control( 'headline_tag', [
			'label'   => esc_html__( 'Headline HTML tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
			],
		] );

		$this->add_control( 'description', [
			'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Unlock your dream space with a free consultation and personalized design assistance from our experts.', 'agency-elementor-widgets' ),
			'rows'    => 3,
		] );

		$this->end_controls_section();
	}

	private function controls_button(): void {
		$this->start_controls_section( 's_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'button_text', [
			'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Schedule a Free Consultation', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'button_link', [
			'label'   => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'button_arrow', [
			'label'   => esc_html__( 'Show arrow icon', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ──────────────────────────────────────────────────────

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'page_bg', [
			'label'     => esc_html__( 'Page background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-page-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#E9E0D9',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-card-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'card_radius', [
			'label'      => esc_html__( 'Card border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 64 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 32 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-ctab__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'card_padding', [
			'label'      => esc_html__( 'Card padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'        => [ 'top' => '96', 'right' => '40', 'bottom' => '96', 'left' => '40', 'unit' => 'px', 'isLinked' => false ],
			'tablet_default' => [ 'top' => '72', 'right' => '32', 'bottom' => '72', 'left' => '32', 'unit' => 'px', 'isLinked' => false ],
			'mobile_default' => [ 'top' => '48', 'right' => '24', 'bottom' => '48', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .aew-ctab__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'content_gap', [
			'label'      => esc_html__( 'Spacing between items', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 4, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-ctab__content' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'content_max_w', [
			'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [ 'min' => 360, 'max' => 1000 ],
				'%'  => [ 'min' => 40, 'max' => 100 ],
			],
			'default'    => [ 'unit' => 'px', 'size' => 640 ],
			'selectors'  => [ '{{WRAPPER}} .aew-ctab__content' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_eyebrow(): void {
		$this->start_controls_section( 'ss_eyebrow', [ 'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-eyebrow: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'eyebrow_typo',
			'selector' => '{{WRAPPER}} .aew-ctab__eyebrow',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Playfair Display' ],
				'font_weight'    => [ 'default' => '700' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_headline(): void {
		$this->start_controls_section( 'ss_headline', [ 'label' => esc_html__( 'Headline', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'headline_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-headline: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'headline_typo',
			'selector' => '{{WRAPPER}} .aew-ctab__headline',
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

	private function style_description(): void {
		$this->start_controls_section( 'ss_description', [ 'label' => esc_html__( 'Description', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-description: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typo',
			'selector' => '{{WRAPPER}} .aew-ctab__description',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.4 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_button(): void {
		$this->start_controls_section( 'ss_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Colour controls assign to CSS vars on the wrapper (NOT direct
		// properties). The selector rule drives the editor's live preview;
		// render() ALSO emits the resolved value as an inline wrapper var so
		// global-bound colours survive on the front end (gotcha #19). The
		// stylesheet consumes each var with a design-system fallback.
		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-btn-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-btn-text: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-btn-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-ctab-btn-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'label'     => esc_html__( 'Button typography', 'agency-elementor-widgets' ),
			'selector'  => '{{WRAPPER}} .aew-ctab__btn',
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
			'selectors'  => [ '{{WRAPPER}} .aew-ctab__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$eyebrow     = (string) ( $s['eyebrow'] ?? '' );
		$headline    = (string) ( $s['headline'] ?? '' );
		$description = (string) ( $s['description'] ?? '' );

		$allowed_tags = [ 'h1', 'h2', 'h3' ];
		$tag          = in_array( $s['headline_tag'] ?? 'h2', $allowed_tags, true ) ? $s['headline_tag'] : 'h2';

		$button_text = (string) ( $s['button_text'] ?? '' );
		$button      = $this->parse_link( $s['button_link'] ?? [] );
		$show_arrow  = 'yes' === ( $s['button_arrow'] ?? 'yes' );

		$has_button = '' !== $button_text && '' !== $button['url'];

		// Nothing to show — bail so an empty card doesn't render.
		if ( '' === $eyebrow && '' === $headline && '' === $description && ! $has_button ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-ctab' );
		$this->add_render_attribute( 'wrapper', 'data-aew-cta-banner-v2', '' );

		// Emit resolved colours as inline CSS vars on the wrapper (live-page
		// guarantee for global-bound picks — see guide §6.8 / gotcha #19).
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'page_bg'           => '--aew-ctab-page-bg',
				'card_bg'           => '--aew-ctab-card-bg',
				'eyebrow_color'     => '--aew-ctab-eyebrow',
				'headline_color'    => '--aew-ctab-headline',
				'description_color' => '--aew-ctab-description',
				'btn_bg'            => '--aew-ctab-btn-bg',
				'btn_text'          => '--aew-ctab-btn-text',
				'btn_bg_hover'      => '--aew-ctab-btn-bg-hover',
				'btn_text_hover'    => '--aew-ctab-btn-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-ctab__inner">
				<div class="aew-ctab__card">
					<div class="aew-ctab__content">

						<?php if ( '' !== $eyebrow ) : ?>
							<?php
							$this->add_render_attribute( 'eyebrow', 'class', 'aew-ctab__eyebrow' );
							$this->add_inline_editing_attributes( 'eyebrow', 'none' );
							?>
							<p <?php $this->print_render_attribute_string( 'eyebrow' ); ?>><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $headline ) : ?>
							<?php
							$this->add_render_attribute( 'headline', 'class', 'aew-ctab__headline' );
							$this->add_inline_editing_attributes( 'headline', 'none' );
							?>
							<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>

						<?php if ( '' !== $description ) : ?>
							<?php
							$this->add_render_attribute( 'description', 'class', 'aew-ctab__description' );
							$this->add_inline_editing_attributes( 'description', 'none' );
							?>
							<p <?php $this->print_render_attribute_string( 'description' ); ?>><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>

						<?php if ( $has_button ) : ?>
							<div class="aew-ctab__actions">
								<a class="aew-ctab__btn"
									href="<?php echo esc_url( $button['url'] ); ?>"
									<?php echo $button['target'] ? 'target="' . esc_attr( $button['target'] ) . '"' : ''; ?>
									<?php echo $button['rel'] ? 'rel="' . esc_attr( $button['rel'] ) . '"' : ''; ?>>
									<span class="aew-ctab__btn-label"><?php echo esc_html( $button_text ); ?></span>
									<?php if ( $show_arrow ) : ?>
										<svg class="aew-ctab__btn-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
											<line x1="4" y1="12" x2="19" y2="12"></line>
											<polyline points="13 6 19 12 13 18"></polyline>
										</svg>
									<?php endif; ?>
								</a>
							</div>
						<?php endif; ?>

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
