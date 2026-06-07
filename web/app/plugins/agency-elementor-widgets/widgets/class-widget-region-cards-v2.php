<?php
/**
 * Region Cards V2 Elementor widget ("Built for {Region}" promo panels).
 *
 * A row of image panels, each with a floating text card overlaid on the
 * background image: heading + paragraph + CTA button. Desktop = N-up grid;
 * mobile = stacked, fixed-height image with the text card anchored to the
 * bottom. Every text colour, the card background and the button colours are
 * editable per-instance from the Elementor Style tab.
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
 * Region promo cards — image panels with an overlaid copy card.
 */
class Widget_Region_Cards_V2 extends Widget_Base {

	private const ASSET_SLUG = 'region-cards-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-region-cards-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Region Cards V2 (Notched)', 'agency-elementor-widgets' );
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
		return [ 'region', 'cards', 'built for', 'utah', 'arizona', 'notched' ];
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
	 * padding.
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-rgcv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * Default panels mirroring the reference design (Utah + Arizona).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_panels(): array {
		return [
			[
				'heading'   => esc_html__( 'BUILT FOR UTAH', 'agency-elementor-widgets' ),
				'paragraph' => esc_html__( 'Explore timber pergola kits designed specifically for Utah snow load, elevation, temperature swings, and long-term outdoor durability.', 'agency-elementor-widgets' ),
				'btn_text'  => esc_html__( 'WHY UTAH CHOOSES NOTCHED', 'agency-elementor-widgets' ),
				'btn_link'  => [ 'url' => '#' ],
				'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/region-1.webp' ) ],
			],
			[
				'heading'   => esc_html__( 'BUILT FOR ARIZONA', 'agency-elementor-widgets' ),
				'paragraph' => esc_html__( 'Explore timber pergola kits designed specifically for Arizona heat, UV exposure, dry climates, and long-term outdoor living.', 'agency-elementor-widgets' ),
				'btn_text'  => esc_html__( 'WHY ARIZONA CHOOSES NOTCHED', 'agency-elementor-widgets' ),
				'btn_link'  => [ 'url' => '#' ],
				'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/region-2.webp' ) ],
			],
		];
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_panels();
		$this->controls_layout();
		$this->style_card();
		$this->style_heading();
		$this->style_paragraph();
		$this->style_button();
	}

	/**
	 * CONTENT tab — the repeater of panels.
	 *
	 * @return void
	 */
	private function controls_panels(): void {
		$this->start_controls_section(
			's_panels',
			[ 'label' => esc_html__( 'Panels', 'agency-elementor-widgets' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Background image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/region-1.webp' ) ],
			]
		);

		$repeater->add_control(
			'image_position',
			[
				'label'       => esc_html__( 'Image focal point', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'center center',
				'description' => esc_html__( 'Which part of the image stays visible around the card.', 'agency-elementor-widgets' ),
				'options'     => [
					'center center' => esc_html__( 'Center', 'agency-elementor-widgets' ),
					'center top'    => esc_html__( 'Top', 'agency-elementor-widgets' ),
					'center bottom' => esc_html__( 'Bottom', 'agency-elementor-widgets' ),
					'left center'   => esc_html__( 'Left', 'agency-elementor-widgets' ),
					'right center'  => esc_html__( 'Right', 'agency-elementor-widgets' ),
				],
			]
		);

		$repeater->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'BUILT FOR UTAH', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'heading_accent',
			[
				'label'       => esc_html__( 'Heading — second-colour text', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Optional second colour for the heading. If this text is part of the heading above, that part is recoloured; otherwise it is added after the heading in the accent colour. Leave empty for a single-colour heading.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'paragraph',
			[
				'label'   => esc_html__( 'Paragraph', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Explore timber pergola kits designed specifically for Utah snow load, elevation, temperature swings, and long-term outdoor durability.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'btn_text',
			[
				'label'   => esc_html__( 'Button label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'WHY UTAH CHOOSES NOTCHED', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'btn_link',
			[
				'label'       => esc_html__( 'Button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'panels',
			[
				'label'       => esc_html__( 'Panels', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_panels(),
				'title_field' => '{{{ heading }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — layout knobs (columns, gaps, card inset, mobile sizing).
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section(
			's_layout',
			[ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'          => esc_html__( 'Gap between panels', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 32 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_radius',
			[
				'label'          => esc_html__( 'Panel corner radius', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 16 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 12 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__panel' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_min_height',
			[
				'label'          => esc_html__( 'Image / panel height', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 200, 'max' => 900 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 400 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 400 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 400 ],
				'description'    => esc_html__( 'Exact height of each image panel (desktop, tablet and mobile).', 'agency-elementor-widgets' ),
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__panel' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_inset',
			[
				'label'          => esc_html__( 'Card gap from bottom of image', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'description'    => esc_html__( 'Distance between the text card and the bottom edge of the image (desktop, tablet and mobile). The card is anchored to the bottom.', 'agency-elementor-widgets' ),
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 160 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 40 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__panel' => '--aew-rgcv2-inset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — the floating copy card.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section(
			's_style_card',
			[
				'label' => esc_html__( 'Card', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6F0EC',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-card-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'          => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 24 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'          => esc_html__( 'Card padding', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', 'em' ],
				'default'        => [ 'top' => '48', 'right' => '40', 'bottom' => '48', 'left' => '40', 'unit' => 'px', 'isLinked' => false ],
				'mobile_default' => [ 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'unit' => 'px', 'isLinked' => true ],
				'selectors'      => [
					'{{WRAPPER}} .aew-rgcv2__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_width',
			[
				'label'       => esc_html__( 'Card width', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%' ],
				'range'       => [
					'px' => [ 'min' => 120, 'max' => 700 ],
					'%'  => [ 'min' => 20, 'max' => 100 ],
				],
				'default'     => [ 'unit' => '%', 'size' => 100 ],
				'description' => esc_html__( 'Sets the card width on desktop/tablet. The card stays centered over the image and may exceed the old inset-limited size (capped only by the panel).', 'agency-elementor-widgets' ),
				'selectors'   => [
					'{{WRAPPER}} .aew-rgcv2__card' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_max_width',
			[
				'label'       => esc_html__( 'Card max width (desktop)', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%' ],
				'range'       => [
					'px' => [ 'min' => 200, 'max' => 720 ],
					'%'  => [ 'min' => 40, 'max' => 100 ],
				],
				'default'     => [ 'unit' => '%', 'size' => 100 ],
				'description' => esc_html__( 'Caps the card width within the inset area; the card stays centered.', 'agency-elementor-widgets' ),
				'selectors'   => [
					'{{WRAPPER}} .aew-rgcv2__card' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_text_align',
			[
				'label'     => esc_html__( 'Text alignment', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [
					'{{WRAPPER}} .aew-rgcv2__card' => 'text-align: {{VALUE}}; align-items: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left'   => 'left; align-items: flex-start',
					'center' => 'center; align-items: center',
					'right'  => 'right; align-items: flex-end',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — heading colour + typography.
	 *
	 * @return void
	 */
	private function style_heading(): void {
		$this->start_controls_section(
			's_style_heading',
			[
				'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-title: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_accent_color',
			[
				'label'       => esc_html__( 'Second colour (accent)', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#2A4F41',
				'description' => esc_html__( 'Colour applied to each panel’s “second-colour text”.', 'agency-elementor-widgets' ),
				'selectors'   => [
					'{{WRAPPER}}' => '--aew-rgcv2-title-accent: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'heading_typography',
				'selector'       => '{{WRAPPER}} .aew-rgcv2__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 40 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label'      => esc_html__( 'Space below heading', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-rgcv2__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — paragraph colour + typography.
	 *
	 * @return void
	 */
	private function style_paragraph(): void {
		$this->start_controls_section(
			's_style_paragraph',
			[
				'label' => esc_html__( 'Paragraph', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'paragraph_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#141C19',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-text: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'paragraph_typography',
				'selector'       => '{{WRAPPER}} .aew-rgcv2__text',
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

		$this->add_responsive_control(
			'paragraph_spacing',
			[
				'label'      => esc_html__( 'Space below paragraph', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-rgcv2__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
	private function style_button(): void {
		$this->start_controls_section(
			's_style_button',
			[
				'label' => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-btn-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color_hover',
			[
				'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-rgcv2-btn-text-hover: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'btn_typography',
				'selector'       => '{{WRAPPER}} .aew-rgcv2__btn',
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
					'{{WRAPPER}} .aew-rgcv2__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
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
	 * Build the heading HTML, colouring the optional accent text with the second
	 * colour. Two behaviours, so the field "just works" either way:
	 *   1. If the accent text is a substring of the heading, that part is wrapped
	 *      in place (e.g. heading "TRADITIONAL TIMBER KITS FOR A TIMELESS LOOK"
	 *      + accent "FOR A TIMELESS LOOK").
	 *   2. If the accent text is NOT in the heading, it is APPENDED after the
	 *      heading in the accent colour (e.g. heading "TRADITIONAL TIMBER KITS"
	 *      + accent "FOR A TIMELESS LOOK" → both render, accent in green).
	 * Match is case-insensitive; everything stays escaped.
	 *
	 * @param string $heading Full heading text.
	 * @param string $accent  Text to colour with the accent colour.
	 * @return string Escaped HTML for inside the <h3>.
	 */
	private function heading_html( string $heading, string $accent ): string {
		$accent = trim( $accent );
		if ( '' === $accent ) {
			return esc_html( $heading );
		}

		$pos = stripos( $heading, $accent );
		if ( false === $pos ) {
			// Accent text not inside the heading → append it in the accent colour.
			$sep = '' === trim( $heading ) ? '' : ' ';
			return esc_html( $heading )
				. $sep
				. '<span class="aew-rgcv2__title-accent">' . esc_html( $accent ) . '</span>';
		}

		$len    = strlen( $accent );
		$before = substr( $heading, 0, $pos );
		$match  = substr( $heading, $pos, $len );  // preserve the heading's own casing
		$after  = substr( $heading, $pos + $len );

		return esc_html( $before )
			. '<span class="aew-rgcv2__title-accent">' . esc_html( $match ) . '</span>'
			. esc_html( $after );
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$panels = $s['panels'] ?? [];
		if ( ! is_array( $panels ) || empty( $panels ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-rgcv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-region-cards-v2', '' );

		/*
		 * Emit resolved colours as inline CSS vars on the wrapper.
		 * get_settings_for_display() resolves global colours → hex, so global-
		 * bound picks render on the front end (Elementor's selector pipeline
		 * drops globals for custom controls). The matching `selectors` on each
		 * control drive the editor preview; this inline value wins on live.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'card_bg'              => '--aew-rgcv2-card-bg',
				'heading_color'        => '--aew-rgcv2-title',
				'heading_accent_color' => '--aew-rgcv2-title-accent',
				'paragraph_color'      => '--aew-rgcv2-text',
				'btn_bg'          => '--aew-rgcv2-btn-bg',
				'btn_bg_hover'    => '--aew-rgcv2-btn-bg-hover',
				'btn_text_color'  => '--aew-rgcv2-btn-text',
				'btn_text_color_hover' => '--aew-rgcv2-btn-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-rgcv2__inner">
				<div class="aew-rgcv2__grid">
					<?php
					foreach ( $panels as $index => $panel ) :
						$image     = $panel['image'] ?? [];
						$image_url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
						if ( '' === $image_url ) {
							$image_url = Widget_Assets::url( self::ASSET_SLUG, 'images/region-1.webp' );
						}
						$focal   = $panel['image_position'] ?? 'center center';
						$heading = (string) ( $panel['heading'] ?? '' );
						$accent  = (string) ( $panel['heading_accent'] ?? '' );
						$text    = (string) ( $panel['paragraph'] ?? '' );
						$btn_lbl = (string) ( $panel['btn_text'] ?? '' );
						$link    = $this->parse_link( $panel['btn_link'] ?? [] );

						$panel_style = sprintf(
							'--aew-rgcv2-image: url(%s); --aew-rgcv2-focal: %s;',
							esc_url( $image_url ),
							esc_attr( $focal )
						);
						?>
						<article class="aew-rgcv2__panel" style="<?php echo esc_attr( $panel_style ); ?>">
							<div class="aew-rgcv2__media" role="img" aria-label="<?php echo esc_attr( $heading ); ?>"></div>
							<div class="aew-rgcv2__card">
								<?php if ( '' !== $heading ) : ?>
									<h3 class="aew-rgcv2__title"><?php echo $this->heading_html( $heading, $accent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped inside helper ?></h3>
								<?php endif; ?>
								<?php if ( '' !== trim( $text ) ) : ?>
									<p class="aew-rgcv2__text"><?php echo esc_html( $text ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== trim( $btn_lbl ) ) : ?>
									<a class="aew-rgcv2__btn"
										href="<?php echo esc_url( $link['url'] ?: '#' ); ?>"
										<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
										<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
										<?php echo esc_html( $btn_lbl ); ?>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
