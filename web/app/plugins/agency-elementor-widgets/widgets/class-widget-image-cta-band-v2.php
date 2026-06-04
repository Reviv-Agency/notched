<?php
/**
 * Image CTA Band V2 Elementor widget.
 *
 * A FULL-BLEED background image (edge to edge, not capped at the 1440 content
 * width) with a centered content box floating on top — eyebrow + heading +
 * description + button. Mirrors notched.com "Want to Know More About Notched?".
 * The background image, box surface, colours and button are editable from the
 * Style tab (§6.8 var pattern). An optional dark overlay improves legibility.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Full-bleed image band with a centered CTA box.
 */
class Widget_Image_Cta_Band_V2 extends Widget_Base {

	private const ASSET_SLUG = 'image-cta-band-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-image-cta-band-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Image CTA Band V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-rollover';
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
		return [ 'cta', 'banner', 'image', 'full bleed', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to the inner box.
	 * Defaults left EMPTY (WIDGET-V2-BUILD-GUIDE §5 / gotcha #16).
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-icb__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_button();
		$this->style_band();
		$this->style_box();
		$this->style_typography();
		$this->style_button();
	}

	/**
	 * CONTENT tab — background image + box copy.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'image', [
			'label'   => esc_html__( 'Background image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
			'description' => esc_html__( 'Full-bleed background image (edge to edge).', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'eyebrow', [
			'label'   => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => esc_html__( 'Want to Know More About Notched?', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'description', [
			'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => '',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — button.
	 *
	 * @return void
	 */
	private function controls_button(): void {
		$this->start_controls_section( 's_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'button_text', [
			'label'   => esc_html__( 'Button label', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Here’s Our Story', 'agency-elementor-widgets' ),
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

	/**
	 * STYLE tab — band (image height + overlay).
	 *
	 * @return void
	 */
	private function style_band(): void {
		$this->start_controls_section( 'ss_band', [ 'label' => esc_html__( 'Band', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'min_height', [
			'label'      => esc_html__( 'Minimum height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 240, 'max' => 900 ], 'vh' => [ 'min' => 30, 'max' => 100 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 600 ],
			'selectors'  => [ '{{WRAPPER}} .aew-icb' => 'min-height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'focal', [
			'label'   => esc_html__( 'Image focal point', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'center center',
			'options' => [
				'center center' => esc_html__( 'Center', 'agency-elementor-widgets' ),
				'center top'    => esc_html__( 'Top', 'agency-elementor-widgets' ),
				'center bottom' => esc_html__( 'Bottom', 'agency-elementor-widgets' ),
				'left center'   => esc_html__( 'Left', 'agency-elementor-widgets' ),
				'right center'  => esc_html__( 'Right', 'agency-elementor-widgets' ),
			],
			'selectors' => [ '{{WRAPPER}} .aew-icb' => 'background-position: {{VALUE}};' ],
		] );

		$this->add_control( 'overlay_color', [
			'label'     => esc_html__( 'Overlay colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0)',
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-overlay: {{VALUE}};' ],
			'description' => esc_html__( 'Optional tint over the image for legibility.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — content box.
	 *
	 * @return void
	 */
	private function style_box(): void {
		$this->start_controls_section( 'ss_box', [ 'label' => esc_html__( 'Box', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'box_bg', [
			'label'     => esc_html__( 'Box background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-box-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'box_radius', [
			'label'      => esc_html__( 'Box radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-icb__box' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'box_max_width', [
			'label'      => esc_html__( 'Box max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 320, 'max' => 1000 ], '%' => [ 'min' => 30, 'max' => 100 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 800 ],
			'selectors'  => [ '{{WRAPPER}} .aew-icb__box' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — typography colours.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Eyebrow colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-eyebrow: {{VALUE}};' ],
		] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Description colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-description: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — button colours.
	 *
	 * @return void
	 */
	private function style_button(): void {
		$this->start_controls_section( 'ss_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_color', [
			'label'     => esc_html__( 'Text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-btn-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-btn-bg-hover: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_hover', [
			'label'     => esc_html__( 'Text (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-icb-btn-text-hover: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'aew-icb' );
		$this->add_render_attribute( 'wrapper', 'data-aew-image-cta-band-v2', '' );

		$image = $s['image'] ?? [];
		$url   = is_array( $image ) ? (string) ( $image['url'] ?? '' ) : '';

		$color_vars = Color_Vars::build( $this, $s, [
			'overlay_color'     => '--aew-icb-overlay',
			'box_bg'            => '--aew-icb-box-bg',
			'eyebrow_color'     => '--aew-icb-eyebrow',
			'heading_color'     => '--aew-icb-heading',
			'description_color' => '--aew-icb-description',
			'btn_bg'            => '--aew-icb-btn-bg',
			'btn_text_color'    => '--aew-icb-btn-text',
			'btn_bg_hover'      => '--aew-icb-btn-bg-hover',
			'btn_text_hover'    => '--aew-icb-btn-text-hover',
		] );
		$style = $color_vars;
		if ( '' !== $url ) {
			$style .= '--aew-icb-image: url(' . esc_url( $url ) . ');';
		}
		if ( '' !== $style ) {
			$this->add_render_attribute( 'wrapper', 'style', $style );
		}

		$eyebrow = (string) ( $s['eyebrow'] ?? '' );
		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$desc    = (string) ( $s['description'] ?? '' );
		$btn_lbl = (string) ( $s['button_text'] ?? '' );
		$arrow   = 'yes' === ( $s['button_arrow'] ?? '' );
		$link    = $this->parse_link( $s['button_link'] ?? [] );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-icb__inner">
				<div class="aew-icb__box">
					<?php if ( '' !== trim( $eyebrow ) ) : ?>
						<p class="aew-icb__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $heading ) ) : ?>
						<<?php echo esc_html( $tag ); ?> class="aew-icb__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
					<?php endif; ?>
					<?php if ( '' !== trim( $desc ) ) : ?>
						<p class="aew-icb__description"><?php echo esc_html( $desc ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $btn_lbl ) ) : ?>
						<a class="aew-icb__btn"
							href="<?php echo esc_url( $link['url'] ?: '#' ); ?>"
							<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
							<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
							<span class="aew-icb__btn-label"><?php echo esc_html( $btn_lbl ); ?></span>
							<?php if ( $arrow ) : ?>
								<span class="aew-icb__btn-arrow" aria-hidden="true">&rarr;</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Normalise an Elementor URL control value into url/target/rel.
	 *
	 * @param mixed $d Raw URL control value.
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
}
