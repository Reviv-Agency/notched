<?php
/**
 * Icon Cards — Notched brand.
 *
 * A row of dark-green cards, each with a check (or custom) icon above a bold
 * Teko statement. Desktop = equal-width row; mobile = stacked. Repeater-driven
 * and fully editable in Elementor.
 *
 * Mirrors the Header V2 / Footer V2 conventions (see WIDGET-V2-BUILD-GUIDE.md).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Icon_Cards extends Widget_Base {

	private const ASSET_SLUG = 'icon-cards';

	public function get_name(): string      { return 'agency-icon-cards'; }
	public function get_title(): string     { return esc_html__( 'Icon Cards (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-checkbox'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'cards', 'icon', 'check', 'features', 'notched' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to the inner wrapper so the
	 * outer block keeps its full-bleed background while sidebar padding behaves.
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-iccr__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			// Leave the override empty so the stylesheet owns the responsive X
			// padding (40px mobile/tablet → 80px desktop). A non-empty default
			// here is emitted by Elementor WITHOUT media queries and would
			// clobber the stylesheet at every breakpoint.
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
		$this->controls_cards();

		$this->style_section();
		$this->style_card();
		$this->style_icon();
		$this->style_text();
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function default_cards(): array {
		return [
			[ 'text' => esc_html__( 'Real timber. Traditional joinery.', 'agency-elementor-widgets' ) ],
			[ 'text' => esc_html__( 'Installed in as little as one day', 'agency-elementor-widgets' ) ],
			[ 'text' => esc_html__( 'Designed to last for generations', 'agency-elementor-widgets' ) ],
		];
	}

	private function controls_cards(): void {
		$this->start_controls_section( 's_cards', [ 'label' => esc_html__( 'Cards', 'agency-elementor-widgets' ) ] );

		$rep = new Repeater();

		$rep->add_control( 'text', [
			'label'   => esc_html__( 'Text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Real timber. Traditional joinery.', 'agency-elementor-widgets' ),
		] );

		$rep->add_control( 'custom_icon', [
			'label'       => esc_html__( 'Custom icon (optional)', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => [ 'image', 'svg' ],
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Leave empty to use the built-in check mark.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'cards', [
			'label'       => esc_html__( 'Cards', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'default'     => $this->default_cards(),
			'title_field' => '{{{ text }}}',
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ──────────────────────────────────────────────────────

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}} .aew-iccr' => 'background-color: {{VALUE}};' ],
		] );


		$this->add_responsive_control( 'pad_y', [
			'label'      => esc_html__( 'Vertical padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 160 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 64 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 32 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 24 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}} .aew-iccr__card' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'card_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 24 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'card_min_h', [
			'label'      => esc_html__( 'Min height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 120, 'max' => 520 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 320 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 220 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__card' => 'min-height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'card_pad', [
			'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '40', 'right' => '32', 'bottom' => '40', 'left' => '32', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_icon(): void {
		$this->start_controls_section( 'ss_icon', [ 'label' => esc_html__( 'Icon', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Color (built-in icon)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#7D958D',
			'selectors' => [ '{{WRAPPER}} .aew-iccr__icon svg' => 'fill: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'icon_size', [
			'label'      => esc_html__( 'Size', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 24, 'max' => 160 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 72 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 56 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'icon_gap', [
			'label'      => esc_html__( 'Gap below icon', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 28 ],
			'selectors'  => [ '{{WRAPPER}} .aew-iccr__card' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_text(): void {
		$this->start_controls_section( 'ss_text', [ 'label' => esc_html__( 'Text', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}} .aew-iccr__text' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'text_typo',
			'selector' => '{{WRAPPER}} .aew-iccr__text',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 40 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$cards = $s['cards'] ?? [];
		if ( empty( $cards ) || ! is_array( $cards ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-iccr' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-iccr__inner">
				<div class="aew-iccr__grid">
					<?php foreach ( $cards as $index => $card ) : ?>
						<?php
						$icon     = $card['custom_icon'] ?? [];
						$icon_url = is_array( $icon ) ? ( $icon['url'] ?? '' ) : '';
						$text     = (string) ( $card['text'] ?? '' );
						$text_key = $this->get_repeater_setting_key( 'text', 'cards', $index );
						$this->add_render_attribute( $text_key, 'class', 'aew-iccr__text' );
						$this->add_inline_editing_attributes( $text_key, 'none' );
						?>
						<article class="aew-iccr__card">
							<span class="aew-iccr__icon">
								<?php if ( $icon_url ) : ?>
									<img src="<?php echo esc_url( $icon_url ); ?>" alt="" decoding="async" loading="lazy" width="48" height="48" />
								<?php else : ?>
									<?php $this->render_check_icon(); ?>
								<?php endif; ?>
							</span>
							<?php if ( '' !== $text ) : ?>
								<p <?php $this->print_render_attribute_string( $text_key ); ?>><?php echo esc_html( $text ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	private function render_check_icon(): void {
		?>
		<svg class="aew-iccr__check" preserveAspectRatio="xMidYMid meet" viewBox="19.999 21.951 160.091 156.171" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="<?php echo esc_attr__( 'Check mark', 'agency-elementor-widgets' ); ?>">
			<g>
				<path d="m19.999 101.133 33.332 65.173 30.492 11.816 79.282-125.935 16.985-26.194-5.294-3.031-2.119-.003v-1.008L118.82 76.989l-46.512 68.837-30.529-37.044z"></path>
			</g>
		</svg>
		<?php
	}
}
