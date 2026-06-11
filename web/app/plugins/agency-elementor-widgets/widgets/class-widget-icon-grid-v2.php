<?php
/**
 * Icon Grid V2 Elementor widget (icon + title cards).
 *
 * A responsive grid of clean cards — each just an ICON (a user-picked image)
 * above a short TITLE. No body copy, no buttons. On the /custom-project page it
 * renders the 6-item "WHAT HAPPENS DURING YOUR CONSULTATION?" grid. An optional
 * centred eyebrow + section heading can sit above the grid. Column count, gap,
 * alignment and all colours are editable from the Style tab (§6.8 var pattern).
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
 * Icon grid — icon + title cards in a responsive grid.
 */
class Widget_Icon_Grid_V2 extends Widget_Base {

	private const ASSET_SLUG = 'icon-grid-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-icon-grid-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Icon Grid V2', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-gallery-grid';
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
		return [ 'icon', 'grid', 'cards', 'features', 'consultation' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY (see
	 * WIDGET-V2-BUILD-GUIDE §5 / gotcha #16) — the stylesheet owns responsive X
	 * padding; a non-empty default would clobber it at every breakpoint.
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-igv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_eyebrow();
		$this->style_heading();
		$this->style_card();
		$this->style_typography();
	}

	/**
	 * Default items — the live /custom-project consultation list.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_items(): array {
		$icon = static function ( string $file ): array {
			return [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/' . $file ) ];
		};
		return [
			[ 'title' => 'Discuss Your Goals',            'icon' => $icon( 'discuss-goals.svg' ) ],
			[ 'title' => 'Review Inspiration Photos',     'icon' => $icon( 'review-photos.svg' ) ],
			[ 'title' => 'Talk Sizing & Layout',          'icon' => $icon( 'talk-sizing.svg' ) ],
			[ 'title' => 'Explore Timber & Roof Options', 'icon' => $icon( 'explore-timber.svg' ) ],
			[ 'title' => 'Review Budget Expectations',    'icon' => $icon( 'review-budget.svg' ) ],
			[ 'title' => 'Get Expert Recommendations',    'icon' => $icon( 'get-expert.svg' ) ],
		];
	}

	/**
	 * CONTENT tab — optional header + the icon/title repeater.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'eyebrow', [
			'label'       => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'Small label above the heading. Leave empty to hide.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading', [
			'label'       => esc_html__( 'Section heading', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'Leave empty to hide the header row.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'icon', [
			'label'       => esc_html__( 'Icon', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => [ 'image', 'svg' ],
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Image shown at the top of the card.', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'title', [
			'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Discuss Your Goals', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'items', [
			'label'       => esc_html__( 'Items', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_items(),
			'title_field' => '{{{ title }}}',
		] );

		$this->add_control( 'footnote', [
			'label'       => esc_html__( 'Footnote (optional)', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => '',
			'rows'        => 2,
			'description' => esc_html__( 'A line of text shown below the cards, inside the cards box. Leave empty to hide.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — columns, gap, alignment.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'layout', [
			'label'   => esc_html__( 'Layout', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'stacked',
			'options' => [
				'stacked' => esc_html__( 'Stacked (heading on top)', 'agency-elementor-widgets' ),
				'side'    => esc_html__( 'Side heading (heading left, cards right)', 'agency-elementor-widgets' ),
			],
		] );

		$this->add_responsive_control( 'side_heading_width', [
			'label'      => esc_html__( 'Heading column width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%' ],
			'range'      => [ '%' => [ 'min' => 20, 'max' => 60 ] ],
			'default'    => [ 'unit' => '%', 'size' => 35 ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2--side .aew-igv2__inner' => '--aew-igv2-heading-col: {{SIZE}}{{UNIT}};' ],
			'condition'  => [ 'layout' => 'side' ],
		] );

		$this->add_control( 'columns', [
			'label'     => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '3',
			'options'   => [ '2' => '2', '3' => '3', '4' => '4', '6' => '6' ],
			'selectors' => [ '{{WRAPPER}} .aew-igv2__grid' => '--aew-igv2-cols: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => esc_html__( 'Gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'align', [
			'label'     => esc_html__( 'Card alignment', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'center',
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
			],
			'selectors' => [ '{{WRAPPER}} .aew-igv2__card' => '--aew-igv2-align: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'icon_size', [
			'label'      => esc_html__( 'Icon size', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 24, 'max' => 200 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
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
			'selectors' => [ '{{WRAPPER}}' => '--aew-igv2-section-bg: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — eyebrow.
	 *
	 * @return void
	 */
	private function style_eyebrow(): void {
		$this->start_controls_section( 'ss_eyebrow', [ 'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Eyebrow colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-igv2-eyebrow: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'eyebrow_typo',
			'selector' => '{{WRAPPER}} .aew-igv2__eyebrow',
			'fields_options' => [
				'font_family' => [ 'default' => 'Playfair Display' ],
				'font_weight' => [ 'default' => '700' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section heading.
	 *
	 * @return void
	 */
	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-igv2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-igv2__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card surface.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label'       => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'selectors'   => [ '{{WRAPPER}}' => '--aew-igv2-card-bg: {{VALUE}};' ],
			'description' => esc_html__( 'Leave empty for transparent cards on the section background.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'cards_box_heading', [
			'label'     => esc_html__( 'Cards box (wraps all cards)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'cards_box_bg', [
			'label'       => esc_html__( 'Cards box background', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'selectors'   => [ '{{WRAPPER}}' => '--aew-igv2-cards-box-bg: {{VALUE}};' ],
			'description' => esc_html__( 'Background of the box that wraps all the cards (separate from the section background). Leave empty for none.', 'agency-elementor-widgets' ),
		] );

		$this->add_responsive_control( 'cards_box_padding', [
			'label'      => esc_html__( 'Cards box padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 48 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2__cards-box' => 'padding: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'cards_box_radius', [
			'label'      => esc_html__( 'Cards box radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2__cards-box' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => esc_html__( 'Card radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-igv2__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'footnote_color', [
			'label'     => esc_html__( 'Footnote colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-igv2-footnote: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'footnote_typo',
			'label'          => esc_html__( 'Footnote typography', 'agency-elementor-widgets' ),
			'selector'       => '{{WRAPPER}} .aew-igv2__footnote',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card title typography + colour.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Title', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-igv2-title: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-igv2__title',
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
		if ( ! is_array( $items ) || empty( $items ) ) {
			return;
		}

		$layout = 'side' === ( $s['layout'] ?? 'stacked' ) ? 'side' : 'stacked';
		$this->add_render_attribute( 'wrapper', 'class', 'aew-igv2' );
		if ( 'side' === $layout ) {
			$this->add_render_attribute( 'wrapper', 'class', 'aew-igv2--side' );
		}
		$this->add_render_attribute( 'wrapper', 'data-aew-icon-grid-v2', '' );

		/*
		 * Resolve colour controls → inline CSS vars on the wrapper so global-
		 * bound picks survive on the front end (§6.8 / gotcha #19). The matching
		 * `selectors` on each control drive the editor preview; this inline value
		 * wins on live.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'section_bg'    => '--aew-igv2-section-bg',
				'eyebrow_color' => '--aew-igv2-eyebrow',
				'heading_color' => '--aew-igv2-heading',
				'card_bg'       => '--aew-igv2-card-bg',
				'cards_box_bg'   => '--aew-igv2-cards-box-bg',
				'title_color'    => '--aew-igv2-title',
				'footnote_color' => '--aew-igv2-footnote',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$eyebrow = (string) ( $s['eyebrow'] ?? '' );
		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-igv2__inner">
				<?php if ( '' !== trim( $eyebrow ) || '' !== trim( $heading ) ) : ?>
					<div class="aew-igv2__header">
						<?php if ( '' !== trim( $eyebrow ) ) : ?>
							<p class="aew-igv2__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $heading ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="aew-igv2__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="aew-igv2__cards-box">
				<div class="aew-igv2__grid">
					<?php
					foreach ( $items as $item ) :
						$icon     = $item['icon'] ?? [];
						$icon_url = is_array( $icon ) ? (string) ( $icon['url'] ?? '' ) : '';
						$title    = (string) ( $item['title'] ?? '' );

						if ( '' === $icon_url && '' === trim( $title ) ) {
							continue;
						}
						?>
						<article class="aew-igv2__card">
							<?php if ( '' !== $icon_url ) : ?>
								<img class="aew-igv2__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" decoding="async" loading="lazy" />
							<?php endif; ?>
							<?php if ( '' !== trim( $title ) ) : ?>
								<h3 class="aew-igv2__title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
				</div>
				<?php $footnote = (string) ( $s['footnote'] ?? '' ); ?>
				<?php if ( '' !== trim( $footnote ) ) : ?>
					<p class="aew-igv2__footnote"><?php echo esc_html( $footnote ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
