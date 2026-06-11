<?php
/**
 * Benefits Card — a large image with a cream card overlapping its inner edge,
 * holding a heading + rich body + a CTA button. Image left or right.
 * Built to match the example.com "BENEFITS AT [COMPANY]" section.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Image + overlapping cream card with heading, body and a CTA button.
 */
class Widget_Benefits_Card extends Widget_Base {

	private const ASSET_SLUG = 'benefits-card';

	public function get_name(): string {
		return 'agency-benefits-card';
	}

	public function get_title(): string {
		return esc_html__( 'Benefits Card', 'agency-elementor-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-image-box';
	}

	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	public function get_keywords(): array {
		return [ 'benefits', 'image', 'card', 'cta', 'overlap' ];
	}

	protected function register_controls(): void {
		// ── CONTENT ──
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'image', [
			'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$this->add_control( 'image_position', [
			'label'   => esc_html__( 'Image side', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'left',
			'options' => [
				'left'  => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-h-align-left' ],
				'right' => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-h-align-right' ],
			],
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'BENEFITS AT [COMPANY]', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'body', [
			'label'   => esc_html__( 'Body', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => esc_html__( 'We believe the people who build great things deserve to be taken care of.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'show_button', [
			'label'        => esc_html__( 'Show button', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		] );

		$this->add_control( 'button_text', [
			'label'     => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'VIEW ALL BENEFITS', 'agency-elementor-widgets' ),
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$this->add_control( 'button_link', [
			'label'     => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => '' ],
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$this->add_control( 'button_arrow', [
			'label'     => esc_html__( 'Button arrow', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$this->end_controls_section();

		// ── STYLE ──
		$this->start_controls_section( 's_style', [ 'label' => esc_html__( 'Style', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-section-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-card-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bcard__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'image_radius', [
			'label'      => esc_html__( 'Image corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bcard__media' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Button background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text', [
			'label'     => esc_html__( 'Button text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bcard-btn-text: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typography',
			'selector'       => '{{WRAPPER}} .aew-bcard__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 48 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 34 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
			],
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$image     = $s['image'] ?? [];
		$image_url = is_array( $image ) ? (string) ( $image['url'] ?? '' ) : '';
		$heading   = (string) ( $s['heading'] ?? '' );
		$tag       = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$body      = (string) ( $s['body'] ?? '' );
		$pos       = 'right' === ( $s['image_position'] ?? 'left' ) ? 'right' : 'left';

		$this->add_render_attribute( 'wrapper', 'class', 'aew-bcard aew-bcard--img-' . $pos );
		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-bcard-section-bg',
			'card_bg'       => '--aew-bcard-card-bg',
			'heading_color' => '--aew-bcard-heading',
			'text_color'    => '--aew-bcard-text',
			'btn_bg'        => '--aew-bcard-btn-bg',
			'btn_text'      => '--aew-bcard-btn-text',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$show_btn = 'yes' === ( $s['show_button'] ?? '' );
		$btn_text = (string) ( $s['button_text'] ?? '' );
		$link     = $s['button_link'] ?? [];
		$btn_url  = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : '';
		$target   = ( is_array( $link ) && ! empty( $link['is_external'] ) ) ? ' target="_blank"' : '';
		$rel      = ( is_array( $link ) && ! empty( $link['nofollow'] ) ) ? ' rel="nofollow"' : '';
		$arrow    = 'yes' === ( $s['button_arrow'] ?? '' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-bcard__inner">
				<?php if ( '' !== $image_url ) : ?>
					<div class="aew-bcard__media" style="background-image:url('<?php echo esc_url( $image_url ); ?>');" role="img" aria-label="<?php echo esc_attr( $heading ); ?>"></div>
				<?php endif; ?>
				<div class="aew-bcard__card">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<<?php echo esc_html( $tag ); ?> class="aew-bcard__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
					<?php endif; ?>
					<?php if ( ! Rich_Text::is_empty( $body ) ) : ?>
						<div class="aew-bcard__body aew-rich-text"><?php Rich_Text::echo_html( $body ); ?></div>
					<?php endif; ?>
					<?php if ( $show_btn && '' !== trim( $btn_text ) ) : ?>
						<a class="aew-bcard__btn" href="<?php echo esc_url( $btn_url ?: '#' ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
							<span class="aew-bcard__btn-label"><?php echo esc_html( $btn_text ); ?></span>
							<?php if ( $arrow ) : ?>
								<svg class="aew-bcard__btn-arrow" viewBox="0 0 200 200" width="22" height="22" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M179.7 96.3H55.9c10.4-6 15.6-15.8 15.6-29.1h-7.3c0 7.2 0 28.9-43 29.1h-.9v7.3c43.2.2 43.2 22 43.2 29.1h7.3c0-13.4-5.3-23.1-15.8-29.1h124.6v-7.3z"/></svg>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
