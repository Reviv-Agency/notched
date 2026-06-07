<?php
/**
 * Numbered Features V2 Elementor widget.
 *
 * A FULL-BLEED dark band (paper-texture image + #093328 colour overlay) split
 * into two columns: a left COPY column (Teko heading + Lato intro) and a right
 * GRID of numbered features — each a gold circle with a number above a short
 * gold uppercase title. Recreates the "WHY NOTCHED?" section from notched.com.
 *
 * The X gutter lives on the INNER wrapper so the textured background stays
 * full-bleed; only the content caps at the 1440 rail.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Numbered features — copy left, numbered grid right, over a full-bleed band.
 */
class Widget_Numbered_Features_V2 extends Widget_Base {

	private const ASSET_SLUG = 'numbered-features-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-numbered-features-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Numbered Features V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-bullet-list';
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
		return [ 'numbered', 'features', 'why', 'steps', 'list', 'band', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer band keeps its full-bleed textured background. Defaults left EMPTY
	 * (gotcha #16) — the stylesheet owns the responsive X padding.
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-nfv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_band();
		$this->style_heading();
		$this->style_intro();
		$this->style_items();
	}

	/**
	 * Default features — the live /shop-kits "WHY NOTCHED?" list.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_items(): array {
		return [
			[ 'number' => '1', 'title' => 'REAL TIMBER CONSTRUCTION, NO SHORTCUTS' ],
			[ 'number' => '2', 'title' => 'PRECISION-CUT JOINERY FOR A TIGHT, SEAMLESS FIT' ],
			[ 'number' => '3', 'title' => 'STRUCTURAL INTEGRITY BUILT INTO EVERY CONNECTION' ],
			[ 'number' => '4', 'title' => 'DESIGNED FOR LONG-TERM OUTDOOR PERFORMANCE' ],
		];
	}

	/**
	 * CONTENT tab — background, copy column, the numbered repeater.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'bg_image', [
			'label'       => esc_html__( 'Background image', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/paper-texture.jpg' ) ],
			'description' => esc_html__( 'Full-bleed background texture (edge to edge). Tinted by the overlay colour below.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'WHY NOTCHED?', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading HTML tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ],
		] );

		$this->add_control( 'intro', [
			'label'   => esc_html__( 'Intro paragraph', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => esc_html__( 'This isn’t a temporary backyard upgrade. It’s a permanent structure.', 'agency-elementor-widgets' ),
		] );

		$repeater = new Repeater();
		$repeater->add_control( 'number', [
			'label'   => esc_html__( 'Number / label', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );
		$repeater->add_control( 'title', [
			'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Feature title', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'items', [
			'label'       => esc_html__( 'Features', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_items(),
			'title_field' => '{{{ number }}} — {{{ title }}}',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — layout: column split, grid columns, gaps.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'copy_width', [
			'label'      => esc_html__( 'Copy column width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%' ],
			'range'      => [ '%' => [ 'min' => 25, 'max' => 60 ] ],
			'default'    => [ 'unit' => '%', 'size' => 42 ],
			'selectors'  => [ '{{WRAPPER}} .aew-nfv2__layout' => '--aew-nfv2-copy-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'item_columns', [
			'label'     => esc_html__( 'Feature columns', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '2',
			'options'   => [ '1' => '1', '2' => '2', '3' => '3' ],
			'selectors' => [ '{{WRAPPER}} .aew-nfv2__grid' => '--aew-nfv2-cols: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'column_gap', [
			'label'      => esc_html__( 'Gap between copy & features', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 160 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-nfv2__layout' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'item_gap', [
			'label'      => esc_html__( 'Gap between features', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-nfv2__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — full-bleed band background + overlay tint.
	 *
	 * @return void
	 */
	private function style_band(): void {
		$this->start_controls_section( 'ss_band', [ 'label' => esc_html__( 'Band', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'band_bg', [
			'label'     => esc_html__( 'Band base colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-band-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'overlay_color', [
			'label'       => esc_html__( 'Image overlay', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'Colour laid over the texture for the deep-green tint. Use rgba for partial transparency.', 'agency-elementor-widgets' ),
			'selectors'   => [ '{{WRAPPER}}' => '--aew-nfv2-overlay: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — heading.
	 *
	 * @return void
	 */
	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-nfv2__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — intro paragraph.
	 *
	 * @return void
	 */
	private function style_intro(): void {
		$this->start_controls_section( 'ss_intro', [ 'label' => esc_html__( 'Intro paragraph', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'intro_color', [
			'label'     => esc_html__( 'Intro colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-intro: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'intro_typo',
			'selector'       => '{{WRAPPER}} .aew-nfv2__intro',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — feature items (number badge + title).
	 *
	 * @return void
	 */
	private function style_items(): void {
		$this->start_controls_section( 'ss_items', [ 'label' => esc_html__( 'Features', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'badge_bg', [
			'label'     => esc_html__( 'Number badge background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-badge-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'badge_text', [
			'label'     => esc_html__( 'Number colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-badge-text: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'badge_size', [
			'label'      => esc_html__( 'Badge size', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 32, 'max' => 96 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-nfv2__badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-nfv2-title: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'title_typo',
			'selector'       => '{{WRAPPER}} .aew-nfv2__title',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$items = $s['items'] ?? [];
		if ( ! is_array( $items ) ) {
			$items = [];
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-nfv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-numbered-features-v2', '' );

		/*
		 * Colour controls → inline CSS vars on the wrapper so global-bound picks
		 * survive on the front end (gotcha #19). The matching control `selectors`
		 * drive the editor preview; this inline value wins on live.
		 */
		$style = Color_Vars::build(
			$this,
			$s,
			[
				'band_bg'       => '--aew-nfv2-band-bg',
				'overlay_color' => '--aew-nfv2-overlay',
				'heading_color' => '--aew-nfv2-heading',
				'intro_color'   => '--aew-nfv2-intro',
				'badge_bg'      => '--aew-nfv2-badge-bg',
				'badge_text'    => '--aew-nfv2-badge-text',
				'title_color'   => '--aew-nfv2-title',
			]
		);

		// Full-bleed background image (bundled texture by default).
		$bg_image = $s['bg_image'] ?? [];
		$bg_url   = is_array( $bg_image ) ? (string) ( $bg_image['url'] ?? '' ) : '';
		if ( '' !== $bg_url ) {
			$style .= '--aew-nfv2-image: url(' . esc_url( $bg_url ) . ');';
		}
		if ( '' !== $style ) {
			$this->add_render_attribute( 'wrapper', 'style', $style );
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$intro   = (string) ( $s['intro'] ?? '' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-nfv2__inner">
				<div class="aew-nfv2__layout">
					<div class="aew-nfv2__copy">
						<?php if ( '' !== trim( $heading ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="aew-nfv2__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
						<?php if ( '' !== trim( $intro ) ) : ?>
							<p class="aew-nfv2__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $items ) ) : ?>
						<ul class="aew-nfv2__grid">
							<?php
							$auto = 0;
							foreach ( $items as $item ) :
								++$auto;
								$title  = (string) ( $item['title'] ?? '' );
								$number = trim( (string) ( $item['number'] ?? '' ) );
								if ( '' === $number ) {
									$number = (string) $auto;
								}
								if ( '' === trim( $title ) && '' === $number ) {
									continue;
								}
								?>
								<li class="aew-nfv2__item">
									<?php if ( '' !== $number ) : ?>
										<span class="aew-nfv2__badge"><?php echo esc_html( $number ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== trim( $title ) ) : ?>
										<h3 class="aew-nfv2__title"><?php echo esc_html( $title ); ?></h3>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
