<?php
/**
 * Quote Band Elementor widget.
 *
 * A full-bleed gold band: big heading on the left, an outlined CTA button on the
 * right (→ /contact-us). Heading supports the {{product}} token (resolved to the
 * product name on product pages by the theme render filter). Stacks on mobile.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Gold "Request a Quote" band — heading left, button right.
 */
class Widget_Quote_Band extends Widget_Base {

	private const ASSET_SLUG = 'quote-band';

	public function get_name(): string { return 'agency-quote-band'; }
	public function get_title(): string { return esc_html__( 'Quote Band (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string { return 'eicon-call-to-action'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	protected function register_controls(): void {

		/* ── Content ───────────────────────────────────────────────────────── */
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'       => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'REQUEST A QUOTE', 'agency-elementor-widgets' ),
			'description' => esc_html__( 'You can use {{product}} to insert the product name on product pages.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading HTML tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'button_text', [
			'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'REQUEST QUOTE', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'button_link', [
			'label'   => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '/contact-us/' ],
		] );

		$this->add_control( 'button_arrow', [
			'label'        => esc_html__( 'Show arrow', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		/* ── Style ─────────────────────────────────────────────────────────── */
		$this->start_controls_section( 's_style', [
			'label' => esc_html__( 'Style', 'agency-elementor-widgets' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'band_bg', [
			'label'   => esc_html__( 'Band background', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#876137',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'heading_color', [
			'label'   => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typography',
			'selector' => '{{WRAPPER}} .aew-qb__heading',
		] );

		$this->add_control( 'btn_text_color', [
			'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#876137',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-btn-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'   => esc_html__( 'Button background', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_hover', [
			'label'   => esc_html__( 'Button text (hover)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-btn-text-hover: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg_hover', [
			'label'   => esc_html__( 'Button background (hover)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#6E4F2D',
			'selectors' => [ '{{WRAPPER}} .aew-qb' => '--aew-qb-btn-bg-hover: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_radius', [
			'label'   => esc_html__( 'Button radius (px)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default' => [ 'size' => 10, 'unit' => 'px' ],
			'selectors' => [ '{{WRAPPER}} .aew-qb__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'pad_block', [
			'label'   => esc_html__( 'Band vertical padding (px)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
			'default' => [ 'size' => 28, 'unit' => 'px' ],
			'selectors' => [ '{{WRAPPER}} .aew-qb__inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$heading = (string) ( $s['heading'] ?? '' );
		$btn_txt = (string) ( $s['button_text'] ?? '' );
		$allowed = [ 'h1', 'h2', 'h3', 'div' ];
		$tag     = in_array( $s['heading_tag'] ?? 'h2', $allowed, true ) ? $s['heading_tag'] : 'h2';

		$link   = is_array( $s['button_link'] ?? null ) ? $s['button_link'] : [ 'url' => '' ];
		$url    = $link['url'] ?? '';
		$target = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
		$rel    = ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : '';
		$arrow  = 'yes' === ( $s['button_arrow'] ?? 'yes' );

		if ( '' === trim( $heading ) && '' === trim( $btn_txt ) ) {
			return;
		}
		?>
		<section class="aew-qb">
			<div class="aew-qb__inner">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<<?php echo esc_attr( $tag ); ?> class="aew-qb__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $tag ); ?>>
				<?php endif; ?>

				<?php if ( '' !== trim( $btn_txt ) && '' !== $url ) : ?>
					<a class="aew-qb__btn" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<span class="aew-qb__btn-label"><?php echo esc_html( $btn_txt ); ?></span>
						<?php if ( $arrow ) : ?>
							<svg class="aew-qb__btn-arrow" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
