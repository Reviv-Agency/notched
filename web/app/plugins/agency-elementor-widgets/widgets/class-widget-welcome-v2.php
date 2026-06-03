<?php
/**
 * Welcome V2 — Notched brand.
 *
 * Two-column intro band: eyebrow + large Teko heading on the left, body
 * paragraphs + script signature + two CTA buttons on the right. Owns its own
 * (dark-green) full-bleed background so it drops in as a self-contained unit
 * beneath the Parallax Image band.
 *
 * Mirrors the Hero V2 / Footer V2 conventions (see WIDGET-V2-BUILD-GUIDE.md).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Welcome_V2 extends Widget_Base {

	private const ASSET_SLUG = 'welcome-v2';

	public function get_name(): string      { return 'agency-welcome-v2'; }
	public function get_title(): string     { return esc_html__( 'Welcome V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-text-area'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'welcome', 'intro', 'about', 'notched', 'cta' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to the inner wrapper so the
	 * outer block keeps its full-bleed background. Leave defaults EMPTY so the
	 * stylesheet owns responsive X padding (WIDGET-V2-BUILD-GUIDE §5/§6.5).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-welc__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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

		$this->style_body();
		$this->style_eyebrow();
		$this->style_heading();
		$this->style_text();
		$this->style_signature();
		$this->style_buttons();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'eyebrow', [
			'label'   => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'WELCOME TO', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( "NOTCHED\nTIMBER", 'agency-elementor-widgets' ),
			'description' => esc_html__( 'Use line breaks to control wrapping.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'body', [
			'label'   => esc_html__( 'Body text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<p>Welcome to Notched Timbers. I&#8217;m Jardin, the owner and founder. Thank you for all the support over the years. It really means a lot.</p><p>My team and I love helping people design and build outdoor spaces where life happens. We focus on real timber craftsmanship, using techniques like dovetail joinery to create structures that look incredible and last.</p><p>Take a look around and explore what&#8217;s possible. We&#8217;re a small team, and we&#8217;ll treat you like family every step of the way. If you&#8217;re thinking about building something, give us a call. We&#8217;d love to help.</p>',
		] );

		$this->add_control( 'signature', [
			'label'   => esc_html__( 'Signature', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Jardin Gleason', 'agency-elementor-widgets' ),
			'description' => esc_html__( 'Rendered in a script font with a leading dash. Leave empty to hide.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	private function controls_buttons(): void {
		$this->start_controls_section( 's_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'primary_text', [
			'label'   => esc_html__( 'Primary button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'See Our Story', 'agency-elementor-widgets' ),
		] );
		$this->add_control( 'primary_link', [
			'label'   => esc_html__( 'Primary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'secondary_text', [
			'label'     => esc_html__( 'Secondary button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Meet Our Crew', 'agency-elementor-widgets' ),
			'separator' => 'before',
		] );
		$this->add_control( 'secondary_link', [
			'label'   => esc_html__( 'Secondary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'show_arrow', [
			'label'     => esc_html__( 'Show arrow on buttons', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'separator' => 'before',
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => esc_html__( 'Body', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'body_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#093328',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'body_max_w', [
			'label'      => esc_html__( 'Inner max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1600 ],
			'selectors'  => [ '{{WRAPPER}} .aew-welc__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'body_pad_y', [
			'label'      => esc_html__( 'Vertical padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 240 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 100 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 56 ],
			'selectors'  => [ '{{WRAPPER}} .aew-welc__inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'col_gap', [
			'label'      => esc_html__( 'Column gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 160 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 80 ],
			'selectors'  => [ '{{WRAPPER}} .aew-welc__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_eyebrow(): void {
		$this->start_controls_section( 'ss_eyebrow', [ 'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#C9B89A',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-eyebrow: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'eyebrow_typo',
			'selector' => '{{WRAPPER}} .aew-welc__eyebrow',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 28 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 2 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#C9B89A',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-heading: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-welc__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 80 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_text(): void {
		$this->start_controls_section( 'ss_text', [ 'label' => esc_html__( 'Body text', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-text: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'text_typo',
			'selector' => '{{WRAPPER}} .aew-welc__body',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.5 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_signature(): void {
		$this->start_controls_section( 'ss_signature', [ 'label' => esc_html__( 'Signature', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'signature_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#C9B89A',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-signature: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'signature_typo',
			'selector' => '{{WRAPPER}} .aew-welc__signature',
			'fields_options' => [
				'font_family' => [ 'default' => 'Dancing Script' ],
				'font_weight' => [ 'default' => '700' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 34 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_buttons(): void {
		$this->start_controls_section( 'ss_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Buttons in the image are light (cream) fill with dark text.
		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-btn-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-btn-text: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-btn-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-welc-btn-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'selector'  => '{{WRAPPER}} .aew-welc__btn',
			'separator' => 'before',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_control( 'btn_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 32 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-welc__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		// The signature uses a script font that isn't part of the site's base
		// font set — enqueue it only when this widget renders. display=swap so
		// text never blocks paint.
		if ( ! wp_style_is( 'aew-welc-script-font', 'enqueued' ) ) {
			wp_enqueue_style(
				'aew-welc-script-font',
				'https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap',
				[],
				AEW_VERSION
			);
		}

		$eyebrow   = (string) ( $s['eyebrow'] ?? '' );
		$heading   = (string) ( $s['heading'] ?? '' );
		$body      = (string) ( $s['body'] ?? '' );
		$signature = (string) ( $s['signature'] ?? '' );

		$primary        = $this->parse_link( $s['primary_link'] ?? [] );
		$secondary      = $this->parse_link( $s['secondary_link'] ?? [] );
		$primary_text   = (string) ( $s['primary_text'] ?? '' );
		$secondary_text = (string) ( $s['secondary_text'] ?? '' );
		$show_arrow     = 'yes' === ( $s['show_arrow'] ?? 'yes' );

		$color_vars = Color_Vars::build( $this, $s, [
			'body_bg'         => '--aew-welc-bg',
			'eyebrow_color'   => '--aew-welc-eyebrow',
			'heading_color'   => '--aew-welc-heading',
			'text_color'      => '--aew-welc-text',
			'signature_color' => '--aew-welc-signature',
			'btn_bg'          => '--aew-welc-btn-bg',
			'btn_text'        => '--aew-welc-btn-text',
			'btn_bg_hover'    => '--aew-welc-btn-bg-hover',
			'btn_text_hover'  => '--aew-welc-btn-text-hover',
		] );
		$this->add_render_attribute( 'wrapper', 'class', 'aew-welc' );
		$this->add_render_attribute( 'wrapper', 'data-aew-welcome-v2', '' );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-welc__inner">
				<div class="aew-welc__grid">

					<div class="aew-welc__col aew-welc__col--head">
						<?php if ( '' !== $eyebrow ) : ?>
							<p class="aew-welc__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $heading ) : ?>
							<h2 class="aew-welc__heading"><?php echo nl2br( esc_html( $heading ) ); ?></h2>
						<?php endif; ?>
					</div>

					<div class="aew-welc__col aew-welc__col--body">
						<?php if ( '' !== $body ) : ?>
							<div class="aew-welc__body"><?php echo wp_kses_post( $body ); ?></div>
						<?php endif; ?>

						<?php if ( '' !== $signature ) : ?>
							<p class="aew-welc__signature"><span class="aew-welc__sig-dash" aria-hidden="true">&mdash;&nbsp;</span><?php echo esc_html( $signature ); ?></p>
						<?php endif; ?>

						<?php if ( ( '' !== $primary_text && $primary['url'] ) || ( '' !== $secondary_text && $secondary['url'] ) ) : ?>
							<div class="aew-welc__buttons">
								<?php
								$this->render_button( $primary_text, $primary, 'primary', $show_arrow );
								$this->render_button( $secondary_text, $secondary, 'secondary', $show_arrow );
								?>
							</div>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</section>
		<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * @param string                                       $text  Button label.
	 * @param array{url: string, target: string, rel: string} $link Parsed link.
	 * @param string                                       $kind  primary|secondary.
	 * @param bool                                         $arrow Show trailing arrow.
	 * @return void
	 */
	private function render_button( string $text, array $link, string $kind, bool $arrow ): void {
		if ( '' === $text || '' === $link['url'] ) {
			return;
		}
		?>
		<a class="aew-welc__btn aew-welc__btn--<?php echo esc_attr( $kind ); ?>"
			href="<?php echo esc_url( $link['url'] ); ?>"
			<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
			<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
			<span class="aew-welc__btn-label"><?php echo esc_html( $text ); ?></span>
			<?php if ( $arrow ) : ?>
				<svg class="aew-welc__btn-arrow" width="22" height="14" viewBox="0 0 22 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M1 7H20M20 7L14.5 1.5M20 7L14.5 12.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			<?php endif; ?>
		</a>
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
