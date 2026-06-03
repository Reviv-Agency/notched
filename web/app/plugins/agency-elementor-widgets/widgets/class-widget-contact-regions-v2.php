<?php
/**
 * Contact Regions V2 Elementor widget (region contact boxes).
 *
 * A heading + a row of full-width region boxes. Each box holds, INSIDE the box,
 * the region name plus a list of contact lines (phone / email), each line
 * optionally linked (tel: / mailto:). Mirrors notched.com/contact-us
 * "Reach Us Directly" — full-bleed coloured boxes with the text inside, not
 * beneath. Colours are editable per-instance from the Style tab (§6.8 pattern).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Region contact boxes — region name + linked contact lines inside each box.
 */
class Widget_Contact_Regions_V2 extends Widget_Base {

	private const ASSET_SLUG = 'contact-regions-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-contact-regions-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Contact Regions V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-mail';
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
		return [ 'contact', 'region', 'phone', 'email', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper.
	 * Defaults left EMPTY (WIDGET-V2-BUILD-GUIDE §5 / gotcha #16).
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-crgn__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_layout();
		$this->style_section();
		$this->style_box();
		$this->style_typography();
	}

	/**
	 * Default region boxes (Utah / Arizona).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_regions(): array {
		return [
			[
				'name'        => 'UTAH',
				'phone'       => '801.410.4255',
				'phone_link'  => [ 'url' => 'tel:8014104255' ],
				'email'       => 'hello@notched.com',
				'email_link'  => [ 'url' => 'mailto:hello@notched.com' ],
			],
			[
				'name'        => 'ARIZONA',
				'phone'       => '480.716.6300',
				'phone_link'  => [ 'url' => 'tel:4807166300' ],
				'email'       => 'hello@notched.com',
				'email_link'  => [ 'url' => 'mailto:hello@notched.com' ],
			],
		];
	}

	/**
	 * CONTENT tab — heading + region repeater.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'We’d Love to Hear From You!', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'intro', [
			'label'   => esc_html__( 'Intro text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => esc_html__( 'If you have not been able to find answers to your questions in other areas of this site, please contact us using the following information:', 'agency-elementor-widgets' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'name', [
			'label'   => esc_html__( 'Region name', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'UTAH', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'phone', [
			'label'   => esc_html__( 'Phone', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '801.410.4255',
		] );

		$repeater->add_control( 'phone_link', [
			'label'         => esc_html__( 'Phone link', 'agency-elementor-widgets' ),
			'type'          => Controls_Manager::URL,
			'default'       => [ 'url' => 'tel:8014104255' ],
			'placeholder'   => 'tel:8014104255',
			'show_external' => false,
		] );

		$repeater->add_control( 'email', [
			'label'   => esc_html__( 'Email', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'hello@notched.com',
		] );

		$repeater->add_control( 'email_link', [
			'label'         => esc_html__( 'Email link', 'agency-elementor-widgets' ),
			'type'          => Controls_Manager::URL,
			'default'       => [ 'url' => 'mailto:hello@notched.com' ],
			'placeholder'   => 'mailto:hello@notched.com',
			'show_external' => false,
		] );

		$this->add_control( 'regions', [
			'label'       => esc_html__( 'Regions', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_regions(),
			'title_field' => '{{{ name }}}',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — columns + gap.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'header_layout', [
			'label'       => esc_html__( 'Header layout', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'split',
			'options'     => [
				'split'   => esc_html__( 'Heading left / intro right', 'agency-elementor-widgets' ),
				'stacked' => esc_html__( 'Stacked & centered', 'agency-elementor-widgets' ),
			],
			'description' => esc_html__( 'Split = heading and intro side by side. Stacked = both centered, one above the other.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'columns', [
			'label'     => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '2',
			'options'   => [ '1' => '1', '2' => '2', '3' => '3' ],
			'selectors' => [ '{{WRAPPER}} .aew-crgn__grid' => '--aew-crgn-cols: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => esc_html__( 'Gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 64 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-crgn__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'box_height', [
			'label'      => esc_html__( 'Box min height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 120, 'max' => 480 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 220 ],
			'selectors'  => [ '{{WRAPPER}} .aew-crgn__box' => 'min-height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'align', [
			'label'     => esc_html__( 'Box text alignment', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'left',
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
			],
			'selectors' => [ '{{WRAPPER}} .aew-crgn__box' => 'text-align: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-section-bg: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — box surface.
	 *
	 * @return void
	 */
	private function style_box(): void {
		$this->start_controls_section( 'ss_box', [ 'label' => esc_html__( 'Box', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'box_bg', [
			'label'     => esc_html__( 'Box background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#7D958D',
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-box-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'box_radius', [
			'label'      => esc_html__( 'Box radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-crgn__box' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — typography + colours.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Section heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'intro_color', [
			'label'     => esc_html__( 'Intro colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-intro: {{VALUE}};' ],
		] );

		$this->add_control( 'name_color', [
			'label'     => esc_html__( 'Region name colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-name: {{VALUE}};' ],
		] );

		$this->add_control( 'line_color', [
			'label'     => esc_html__( 'Contact line colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-line: {{VALUE}};' ],
		] );

		$this->add_control( 'line_color_hover', [
			'label'     => esc_html__( 'Contact line colour (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-crgn-line-hover: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$regions = $s['regions'] ?? [];
		if ( ! is_array( $regions ) || empty( $regions ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-crgn' );
		$this->add_render_attribute( 'wrapper', 'data-aew-contact-regions-v2', '' );

		$layout = ( 'stacked' === ( $s['header_layout'] ?? 'split' ) ) ? 'aew-crgn--header-stacked' : 'aew-crgn--header-split';
		$this->add_render_attribute( 'wrapper', 'class', $layout );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-crgn-section-bg',
			'box_bg'        => '--aew-crgn-box-bg',
			'heading_color' => '--aew-crgn-heading',
			'intro_color'   => '--aew-crgn-intro',
			'name_color'    => '--aew-crgn-name',
			'line_color'    => '--aew-crgn-line',
			'line_color_hover' => '--aew-crgn-line-hover',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$intro   = (string) ( $s['intro'] ?? '' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-crgn__inner">
				<?php if ( '' !== trim( $heading ) || '' !== trim( $intro ) ) : ?>
					<div class="aew-crgn__header">
						<?php if ( '' !== trim( $heading ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="aew-crgn__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
						<?php if ( '' !== trim( $intro ) ) : ?>
							<p class="aew-crgn__intro"><?php echo esc_html( $intro ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="aew-crgn__grid">
					<?php
					foreach ( $regions as $r ) :
						$name  = (string) ( $r['name'] ?? '' );
						$phone = (string) ( $r['phone'] ?? '' );
						$email = (string) ( $r['email'] ?? '' );
						$pl    = $this->link_url( $r['phone_link'] ?? [] );
						$el    = $this->link_url( $r['email_link'] ?? [] );

						if ( '' === trim( $name ) && '' === trim( $phone ) && '' === trim( $email ) ) {
							continue;
						}
						?>
						<div class="aew-crgn__box">
							<?php if ( '' !== trim( $name ) ) : ?>
								<p class="aew-crgn__name"><?php echo esc_html( $name ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== trim( $phone ) ) : ?>
								<p class="aew-crgn__line">
									<?php if ( '' !== $pl ) : ?>
										<a class="aew-crgn__link" href="<?php echo esc_url( $pl ); ?>"><?php echo esc_html( $phone ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $phone ); ?>
									<?php endif; ?>
								</p>
							<?php endif; ?>
							<?php if ( '' !== trim( $email ) ) : ?>
								<p class="aew-crgn__line">
									<?php if ( '' !== $el ) : ?>
										<a class="aew-crgn__link" href="<?php echo esc_url( $el ); ?>"><?php echo esc_html( $email ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $email ); ?>
									<?php endif; ?>
								</p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Pull a (possibly tel:/mailto:) URL out of an Elementor URL control.
	 *
	 * @param mixed $d Raw URL control value.
	 * @return string
	 */
	private function link_url( $d ): string {
		return is_array( $d ) ? (string) ( $d['url'] ?? '' ) : '';
	}
}
