<?php
/**
 * Founder Quote V2 Elementor widget (portrait + quote + name/role + two CTAs).
 *
 * A single founder-quote band: a portrait image on one side, and on the other a
 * large quote, an attribution name, a role/title line, and up to two CTA buttons
 * (e.g. DONATE / VOLUNTEER). Built for the Utah Metropolitan Ballet homepage
 * founder block, generic enough for any "portrait + quote + name + role + CTAs".
 *
 * Brand-free: every colour reads an `aew-*` Elementor global with a neutral-grey
 * fallback; render() resolves global-bound picks via Color_Vars (§6.8 / #19).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Founder quote — portrait + quote + name + role + two CTA buttons.
 */
class Widget_Founder_Quote_V2 extends Widget_Base {

	private const ASSET_SLUG = 'founder-quote-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-founder-quote-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Founder Quote V2', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-blockquote';
	}

	/**
	 * @return array<int, string>
	 */
	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_keywords(): array {
		return [ 'founder', 'quote', 'testimonial', 'director', 'cta' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY (guide §5 /
	 * gotcha #16) — the stylesheet owns responsive X padding.
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-fqv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_content();
		$this->controls_buttons();
		$this->style_section();
		$this->style_quote();
		$this->style_attribution();
		$this->style_buttons();
	}

	/**
	 * CONTENT tab — portrait, quote, attribution.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section(
			's_content',
			[ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'portrait',
			[
				'label' => esc_html__( 'Portrait image', 'agency-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			'image_side',
			[
				'label'     => esc_html__( 'Portrait side (desktop)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => [
					'left'  => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-h-align-left' ],
					'right' => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-h-align-right' ],
				],
			]
		);

		$this->add_control(
			'quote',
			[
				'label'   => esc_html__( 'Quote', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Exploring the arts in various capacities, inspires our minds, touches our hearts, and enriches our lives.', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'name',
			[
				'label'   => esc_html__( 'Attribution name', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'JACQUELINE P. COLLEDGE', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'role',
			[
				'label'   => esc_html__( 'Role / title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'UMB Founder & Artistic Director', 'agency-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — the two CTA buttons.
	 *
	 * @return void
	 */
	private function controls_buttons(): void {
		$this->start_controls_section(
			's_buttons',
			[ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'btn1_text',
			[
				'label'   => esc_html__( 'Primary button label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'DONATE', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'btn1_link',
			[
				'label'       => esc_html__( 'Primary button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'btn2_show',
			[
				'label'        => esc_html__( 'Show second button', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'btn2_text',
			[
				'label'     => esc_html__( 'Secondary button label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'VOLUNTEER', 'agency-elementor-widgets' ),
				'condition' => [ 'btn2_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn2_link',
			[
				'label'       => esc_html__( 'Secondary button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
				'condition'   => [ 'btn2_show' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background + portrait.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section(
			's_style_section',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_bg',
			[
				'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-section-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'          => esc_html__( 'Portrait corner radius', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 24 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-fqv2__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — quote colour + typography.
	 *
	 * @return void
	 */
	private function style_quote(): void {
		$this->start_controls_section(
			's_style_quote',
			[
				'label' => esc_html__( 'Quote', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'quote_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-quote: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'quote_typography',
				'selector'       => '{{WRAPPER}} .aew-fqv2__quote',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Playfair Display' ],
					'font_weight' => [ 'default' => '700' ],
					'font_style'  => [ 'default' => 'italic' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 36 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 125 ] ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — name + role colours + typography.
	 *
	 * @return void
	 */
	private function style_attribution(): void {
		$this->start_controls_section(
			's_style_attr',
			[
				'label' => esc_html__( 'Attribution', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Name color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-name: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'name_typography',
				'selector'       => '{{WRAPPER}} .aew-fqv2__name',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 28 ] ],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				],
			]
		);

		$this->add_control(
			'role_color',
			[
				'label'     => esc_html__( 'Role color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-role: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'role_typography',
				'selector'       => '{{WRAPPER}} .aew-fqv2__role',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 18 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 14 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — CTA button colours + typography.
	 *
	 * @return void
	 */
	private function style_buttons(): void {
		$this->start_controls_section(
			's_style_buttons',
			[
				'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Primary background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_hover',
			[
				'label'     => esc_html__( 'Primary background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => esc_html__( 'Primary text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-btn-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn2_color',
			[
				'label'     => esc_html__( 'Secondary (outline) color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-fqv2-btn2: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'btn_typography',
				'selector'       => '{{WRAPPER}} .aew-fqv2__btn',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 20 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'btn_radius',
			[
				'label'      => esc_html__( 'Corner radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 8 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-fqv2__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Parse an Elementor URL control value into url/target/rel.
	 *
	 * @param mixed $d URL control value.
	 * @return array{url:string,target:string,rel:string}
	 */
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

	/**
	 * Echo a CTA button anchor.
	 *
	 * @param string                                    $label    Button text.
	 * @param array{url:string,target:string,rel:string} $link    Parsed link.
	 * @param string                                    $modifier BEM modifier (primary|secondary).
	 * @return void
	 */
	private function render_button( string $label, array $link, string $modifier ): void {
		if ( '' === trim( $label ) ) {
			return;
		}
		?>
		<a class="aew-fqv2__btn aew-fqv2__btn--<?php echo esc_attr( $modifier ); ?>"
			href="<?php echo esc_url( $link['url'] ?: '#' ); ?>"
			<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
			<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
			<?php echo esc_html( $label ); ?>
		</a>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'aew-fqv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-founder-quote-v2', '' );

		/*
		 * Resolve colour controls → inline CSS vars on the wrapper so global-bound
		 * picks survive on the front end (§6.8 / gotcha #19). The matching
		 * `selectors` on each control drive the editor preview; this inline value
		 * wins on live.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'section_bg'     => '--aew-fqv2-section-bg',
				'quote_color'    => '--aew-fqv2-quote',
				'name_color'     => '--aew-fqv2-name',
				'role_color'     => '--aew-fqv2-role',
				'btn_bg'         => '--aew-fqv2-btn-bg',
				'btn_bg_hover'   => '--aew-fqv2-btn-bg-hover',
				'btn_text_color' => '--aew-fqv2-btn-text',
				'btn2_color'     => '--aew-fqv2-btn2',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$portrait     = $s['portrait'] ?? [];
		$portrait_url = is_array( $portrait ) ? ( $portrait['url'] ?? '' ) : '';
		$side         = 'right' === ( $s['image_side'] ?? 'left' ) ? 'right' : 'left';
		$quote        = (string) ( $s['quote'] ?? '' );
		$name         = (string) ( $s['name'] ?? '' );
		$role         = (string) ( $s['role'] ?? '' );

		$btn1_label = (string) ( $s['btn1_text'] ?? '' );
		$btn1_link  = $this->parse_link( $s['btn1_link'] ?? [] );
		$btn2_on    = 'yes' === ( $s['btn2_show'] ?? '' );
		$btn2_label = (string) ( $s['btn2_text'] ?? '' );
		$btn2_link  = $this->parse_link( $s['btn2_link'] ?? [] );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-fqv2__inner">
				<div class="aew-fqv2__grid aew-fqv2__grid--img-<?php echo esc_attr( $side ); ?>">
					<?php if ( '' !== $portrait_url ) : ?>
						<div class="aew-fqv2__media">
							<img class="aew-fqv2__img" src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" decoding="async" loading="lazy" />
						</div>
					<?php endif; ?>

					<div class="aew-fqv2__content">
						<?php if ( '' !== trim( $quote ) ) : ?>
							<blockquote class="aew-fqv2__quote"><?php echo esc_html( $quote ); ?></blockquote>
						<?php endif; ?>
						<?php if ( '' !== trim( $name ) ) : ?>
							<p class="aew-fqv2__name"><?php echo esc_html( $name ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $role ) ) : ?>
							<p class="aew-fqv2__role"><?php echo esc_html( $role ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== trim( $btn1_label ) || ( $btn2_on && '' !== trim( $btn2_label ) ) ) : ?>
							<div class="aew-fqv2__actions">
								<?php
								$this->render_button( $btn1_label, $btn1_link, 'primary' );
								if ( $btn2_on ) {
									$this->render_button( $btn2_label, $btn2_link, 'secondary' );
								}
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
