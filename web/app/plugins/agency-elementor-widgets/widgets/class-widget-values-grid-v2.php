<?php
/**
 * Values Grid V2 Elementor widget ("A Few Things We Believe").
 *
 * A section heading + a responsive grid of value cards — each an icon, a title
 * and a paragraph. Mirrors notched.com/our-story "A Few Things We Believe".
 * Column count, gap, icon size and all colours are editable from the Style tab
 * (§6.8 var pattern).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Values / beliefs grid — icon + title + paragraph cards.
 */
class Widget_Values_Grid_V2 extends Widget_Base {

	private const ASSET_SLUG = 'values-grid-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-values-grid-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Values Grid V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-info-box';
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
		return [ 'values', 'beliefs', 'icons', 'features', 'notched' ];
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
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-vals__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_typography();
	}

	/**
	 * Default value cards (the live Our Story beliefs).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_values(): array {
		return [
			[ 'title' => 'Championing Sustainability', 'text' => 'At Notched, we are committed to sustainable practices, using eco-friendly materials and precision techniques that minimize waste and honor our natural resources.' ],
			[ 'title' => 'Our Team Feels Like Family', 'text' => 'We want our employees to be well paid, happy, and hardworking. If our people don’t enjoy their work, they don’t do good work. Our team loves their work.' ],
			[ 'title' => 'Unwavering Innovation', 'text' => 'From bold ideas to intricate details, we relentlessly pursue excellence to bring your vision to life.' ],
			[ 'title' => 'Warm & Welcome', 'text' => 'Every Notched interaction should feel like you’re talking to a good friend or family member who has your best interest at heart. Our foundation is trust.' ],
			[ 'title' => 'Expert Guidance', 'text' => 'Our team of skilled designers, engineers, and craftsmen delivers expert solutions to every project, empowering our customers with knowledge, resources, and superior craftsmanship.' ],
			[ 'title' => 'Safety & Durability', 'text' => 'Our structures are built with premium materials, precise joinery, and a dedication to safety, ensuring unmatched quality and long-lasting strength for years to come.' ],
		];
	}

	/**
	 * CONTENT tab — section title + value repeater.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Section heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'A Few Things We Believe', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'shared_icon', [
			'label'       => esc_html__( 'Shared icon', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => [ 'image', 'svg' ],
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Used for any card that has no per-card icon set.', 'agency-elementor-widgets' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'icon', [
			'label'       => esc_html__( 'Icon (optional)', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => [ 'image', 'svg' ],
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Leave empty to use the shared icon.', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'title', [
			'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Value', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'text', [
			'label'   => esc_html__( 'Paragraph', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 4,
			'default' => '',
		] );

		$this->add_control( 'values', [
			'label'       => esc_html__( 'Values', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_values(),
			'title_field' => '{{{ title }}}',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — columns, gap, icon size.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'columns', [
			'label'     => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '2',
			'options'   => [ '2' => '2', '3' => '3', '4' => '4' ],
			'selectors' => [ '{{WRAPPER}} .aew-vals__grid' => '--aew-vals-cols: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => esc_html__( 'Gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-vals__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'icon_size', [
			'label'       => esc_html__( 'Icon size', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px' ],
			'range'       => [ 'px' => [ 'min' => 24, 'max' => 280 ] ],
			// Default left EMPTY so the stylesheet's 180px wins (gotcha #16).
			'selectors'   => [ '{{WRAPPER}} .aew-vals__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
			'description' => esc_html__( 'Defaults to a large faint mark behind each item.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'align', [
			'label'     => esc_html__( 'Card text alignment', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'left',
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
			],
			'selectors' => [ '{{WRAPPER}} .aew-vals__card' => 'text-align: {{VALUE}};' ],
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
			'selectors' => [ '{{WRAPPER}}' => '--aew-vals-section-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'section_image', [
			'label'       => esc_html__( 'Section background image', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Full-bleed background image behind the whole section (e.g. paper texture).', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'panel_bg', [
			'label'       => esc_html__( 'Panel background', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'selectors'   => [ '{{WRAPPER}}' => '--aew-vals-panel-bg: {{VALUE}};' ],
			'description' => esc_html__( 'Optional inner panel behind the grid (e.g. a paper texture colour).', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'panel_radius', [
			'label'      => esc_html__( 'Panel radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-vals__panel' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'icon_color', [
			'label'       => esc_html__( 'Icon tint', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '#876137',
			'selectors'   => [ '{{WRAPPER}}' => '--aew-vals-icon: {{VALUE}};' ],
			'description' => esc_html__( 'Fills the wood-print icon with a flat brand colour.', 'agency-elementor-widgets' ),
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
			'selectors' => [ '{{WRAPPER}}' => '--aew-vals-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Card title colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-vals-title: {{VALUE}};' ],
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Card text colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-vals-text: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$values = $s['values'] ?? [];
		if ( ! is_array( $values ) || empty( $values ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-vals' );
		$this->add_render_attribute( 'wrapper', 'data-aew-values-grid-v2', '' );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-vals-section-bg',
			'panel_bg'      => '--aew-vals-panel-bg',
			'icon_color'    => '--aew-vals-icon',
			'heading_color' => '--aew-vals-heading',
			'title_color'   => '--aew-vals-title',
			'text_color'    => '--aew-vals-text',
		] );
		$bg_image = $s['section_image'] ?? [];
		$bg_url   = is_array( $bg_image ) ? (string) ( $bg_image['url'] ?? '' ) : '';
		$style    = $color_vars;
		if ( '' !== $bg_url ) {
			$style .= '--aew-vals-bg-image: url(' . esc_url( $bg_url ) . ');';
		}
		if ( '' !== $style ) {
			$this->add_render_attribute( 'wrapper', 'style', $style );
		}

		$heading     = (string) ( $s['heading'] ?? '' );
		$tag         = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$shared_icon = $s['shared_icon'] ?? [];
		$shared_url  = is_array( $shared_icon ) ? (string) ( $shared_icon['url'] ?? '' ) : '';
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-vals__inner">
				<div class="aew-vals__panel">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<<?php echo esc_html( $tag ); ?> class="aew-vals__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
					<?php endif; ?>
					<div class="aew-vals__grid">
						<?php
						foreach ( $values as $v ) :
							$icon     = $v['icon'] ?? [];
							$icon_url = is_array( $icon ) ? (string) ( $icon['url'] ?? '' ) : '';
							if ( '' === $icon_url ) {
								$icon_url = $shared_url;
							}
							$title = (string) ( $v['title'] ?? '' );
							$text  = (string) ( $v['text'] ?? '' );

							if ( '' === trim( $title ) && '' === trim( $text ) && '' === $icon_url ) {
								continue;
							}
							?>
							<article class="aew-vals__card">
								<?php if ( '' !== $icon_url ) : ?>
									<img class="aew-vals__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" decoding="async" loading="lazy" />
								<?php endif; ?>
								<?php if ( '' !== trim( $title ) ) : ?>
									<h3 class="aew-vals__title"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== trim( $text ) ) : ?>
									<p class="aew-vals__text"><?php echo esc_html( $text ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
