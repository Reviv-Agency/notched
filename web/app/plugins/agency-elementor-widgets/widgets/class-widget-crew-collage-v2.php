<?php
/**
 * Crew Collage V2 Elementor widget ("Our Crew" intro block).
 *
 * A split section: a copy block (eyebrow-free heading + paragraph + button) on
 * one side and a three-image collage on the other (one tall lead image plus two
 * stacked supporting images), mirroring notched.com/contact-us "Our Crew".
 * Desktop = side-by-side; mobile = stacked with the collage below the copy.
 * Colours and the button are editable per-instance from the Style tab (§6.8).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Our Crew — copy block beside a three-image collage.
 */
class Widget_Crew_Collage_V2 extends Widget_Base {

	private const ASSET_SLUG = 'crew-collage-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-crew-collage-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Crew Collage V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-box';
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
		return [ 'crew', 'team', 'collage', 'about', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper.
	 * Defaults left EMPTY (WIDGET-V2-BUILD-GUIDE §5 / gotcha #16) so the
	 * stylesheet owns responsive X padding.
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-crew__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_images();
		$this->style_section();
		$this->style_typography();
		$this->style_button();
	}

	/**
	 * CONTENT tab — heading, paragraph, button, layout side.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'OUR CREW', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'paragraph', [
			'label'   => esc_html__( 'Paragraph', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 6,
			'default' => esc_html__( 'With expertise spanning from design conception through to meticulous building and seamless installation, our experienced team at Notched ensures every project is crafted with precision and care. Whether you’re envisioning a custom timber structure or assembling one of our DIY kits, trust our seasoned professionals to bring your outdoor vision to life with expertise and dedication.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'btn_text', [
			'label'   => esc_html__( 'Button label', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Meet the Crew', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'btn_link', [
			'label'       => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::URL,
			'default'     => [ 'url' => '/our-crew/' ],
			'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'media_side', [
			'label'   => esc_html__( 'Collage side (desktop)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'left',
			'options' => [
				'left'  => esc_html__( 'Left', 'agency-elementor-widgets' ),
				'right' => esc_html__( 'Right', 'agency-elementor-widgets' ),
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — the three collage images.
	 *
	 * @return void
	 */
	private function controls_images(): void {
		$this->start_controls_section( 's_images', [ 'label' => esc_html__( 'Collage images', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'image_lead', [
			'label'       => esc_html__( 'Lead image (tall)', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'The large image on the outer edge of the collage.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'image_top', [
			'label'   => esc_html__( 'Top image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$this->add_control( 'image_bottom', [
			'label'   => esc_html__( 'Bottom image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$this->add_control( 'image_alt', [
			'label'       => esc_html__( 'Images alt text', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'The Notched crew', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background + image radius.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-section-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'image_radius', [
			'label'      => esc_html__( 'Image corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-crew__img' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — heading + paragraph colours/typography.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-crew__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
			],
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Paragraph colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-text: {{VALUE}};' ],
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
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_color', [
			'label'     => esc_html__( 'Text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-btn-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-btn-bg-hover: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_hover', [
			'label'     => esc_html__( 'Text (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crew-btn-text-hover: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'aew-crew' );
		$this->add_render_attribute( 'wrapper', 'data-aew-crew-collage-v2', '' );

		$side = ( 'right' === ( $s['media_side'] ?? 'left' ) ) ? 'aew-crew--media-right' : 'aew-crew--media-left';
		$this->add_render_attribute( 'wrapper', 'class', $side );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'     => '--aew-crew-section-bg',
			'heading_color'  => '--aew-crew-heading',
			'text_color'     => '--aew-crew-text',
			'btn_bg'         => '--aew-crew-btn-bg',
			'btn_text_color' => '--aew-crew-btn-text',
			'btn_bg_hover'   => '--aew-crew-btn-bg-hover',
			'btn_text_hover' => '--aew-crew-btn-text-hover',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$para    = (string) ( $s['paragraph'] ?? '' );
		$btn_lbl = (string) ( $s['btn_text'] ?? '' );
		$link    = $this->parse_link( $s['btn_link'] ?? [] );
		$alt     = (string) ( $s['image_alt'] ?? '' );

		$lead   = $this->img_url( $s['image_lead'] ?? [] );
		$top    = $this->img_url( $s['image_top'] ?? [] );
		$bottom = $this->img_url( $s['image_bottom'] ?? [] );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-crew__inner">
				<div class="aew-crew__media">
					<?php if ( '' !== $lead ) : ?>
						<img class="aew-crew__img aew-crew__img--lead" src="<?php echo esc_url( $lead ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="lazy" />
					<?php endif; ?>
					<div class="aew-crew__stack">
						<?php if ( '' !== $top ) : ?>
							<img class="aew-crew__img aew-crew__img--top" src="<?php echo esc_url( $top ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="lazy" />
						<?php endif; ?>
						<?php if ( '' !== $bottom ) : ?>
							<img class="aew-crew__img aew-crew__img--bottom" src="<?php echo esc_url( $bottom ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="lazy" />
						<?php endif; ?>
					</div>
				</div>
				<div class="aew-crew__copy">
					<?php if ( '' !== $heading ) : ?>
						<<?php echo esc_html( $tag ); ?> class="aew-crew__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
					<?php endif; ?>
					<?php if ( '' !== trim( $para ) ) : ?>
						<p class="aew-crew__text"><?php echo esc_html( $para ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $btn_lbl ) ) : ?>
						<a class="aew-crew__btn"
							href="<?php echo esc_url( $link['url'] ?: '#' ); ?>"
							<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
							<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
							<?php echo esc_html( $btn_lbl ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Pull a URL out of an Elementor MEDIA control value.
	 *
	 * @param mixed $m Raw media control value.
	 * @return string
	 */
	private function img_url( $m ): string {
		return is_array( $m ) ? (string) ( $m['url'] ?? '' ) : '';
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
