<?php
/**
 * Split Media Elementor widget (Frame 24).
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
 * Split image + copy section — desktop side-by-side, mobile stacked.
 */
class Widget_Split_Media extends Widget_Base {

	private const ASSET_SLUG = 'split-media';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-split-media';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Split Media', 'agency-elementor-widgets' );
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
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_script_depends(): array {
		return [ Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'home', 'story', 'split', 'image', 'content', 'frame' ];
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function default_paragraphs(): array {
		return [
			[
				'paragraph_text' => esc_html__(
					'Your kitchen finally opens up, so you\'re part of the conversation, not working behind a wall that should\'ve come down years ago.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'Your main floor flows the way it should. Bright, connected, and easy to live in.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'Your primary suite feels like a retreat. Quiet, functional, and designed for how you actually live.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'The basement becomes space your family uses every day, not just storage.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'And when someone walks in, they don\'t ask what changed. They just feel like it was always meant to be this way.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'That\'s the home Bright Homes clients are living in right now.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'But for most homeowners, getting there doesn\'t go this smoothly.',
					'agency-elementor-widgets'
				),
			],
			[
				'paragraph_text' => esc_html__(
					'Here\'s why.',
					'agency-elementor-widgets'
				),
			],
		];
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * @return void
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label'   => esc_html__( 'Mobile & tablet max width (px)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1024,
				'min'     => 768,
				'max'     => 1400,
			]
		);

		$this->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'The Home You Always Knew This Could Be', 'agency-elementor-widgets' ),
				'rows'    => 3,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'paragraph_text',
			[
				'label'   => esc_html__( 'Paragraph', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Paragraph text.', 'agency-elementor-widgets' ),
				'rows'    => 4,
			]
		);

		$this->add_control(
			'paragraphs',
			[
				'label'       => esc_html__( 'Paragraphs', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_paragraphs(),
				'title_field' => '{{{ paragraph_text }}}',
			]
		);

		$this->add_control(
			'feature_image',
			[
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/split-media-default.webp' ),
				],
			]
		);

		$this->add_control(
			'copy_block_side',
			[
				'label'   => esc_html__( 'Copy block side (desktop)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Left', 'agency-elementor-widgets' ),
					'right' => esc_html__( 'Right', 'agency-elementor-widgets' ),
				],
			]
		);

		$this->end_controls_section();

		$this->register_button_controls();
	}

	/**
	 * Two fixed buttons (primary + secondary), each independently toggleable.
	 *
	 * @return void
	 */
	private function register_button_controls(): void {
		$this->start_controls_section(
			'section_buttons',
			[
				'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ),
			]
		);

		// ── Primary button ──────────────────────────────────────────────────
		$this->add_control(
			'btn_primary_heading',
			[
				'label' => esc_html__( 'Primary button', 'agency-elementor-widgets' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'btn_primary_show',
			[
				'label'        => esc_html__( 'Show', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'btn_primary_text',
			[
				'label'     => esc_html__( 'Label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Get a Quote', 'agency-elementor-widgets' ),
				'condition' => [ 'btn_primary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_primary_link',
			[
				'label'       => esc_html__( 'Link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'condition'   => [ 'btn_primary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_primary_arrow',
			[
				'label'        => esc_html__( 'Show arrow icon', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => [ 'btn_primary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_primary_arrow_pos',
			[
				'label'     => esc_html__( 'Arrow position', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'before' => esc_html__( 'Before label', 'agency-elementor-widgets' ),
					'after'  => esc_html__( 'After label', 'agency-elementor-widgets' ),
				],
				'condition' => [
					'btn_primary_show'  => 'yes',
					'btn_primary_arrow' => 'yes',
				],
			]
		);

		// ── Secondary button ────────────────────────────────────────────────
		$this->add_control(
			'btn_secondary_heading',
			[
				'label'     => esc_html__( 'Secondary button', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_secondary_show',
			[
				'label'        => esc_html__( 'Show', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'btn_secondary_text',
			[
				'label'     => esc_html__( 'Label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Learn More', 'agency-elementor-widgets' ),
				'condition' => [ 'btn_secondary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_secondary_link',
			[
				'label'     => esc_html__( 'Link', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'btn_secondary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_secondary_arrow',
			[
				'label'        => esc_html__( 'Show arrow icon', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => [ 'btn_secondary_show' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_secondary_arrow_pos',
			[
				'label'     => esc_html__( 'Arrow position', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'before' => esc_html__( 'Before label', 'agency-elementor-widgets' ),
					'after'  => esc_html__( 'After label', 'agency-elementor-widgets' ),
				],
				'condition' => [
					'btn_secondary_show'  => 'yes',
					'btn_secondary_arrow' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens        = Design_Tokens::get();
		$cream_default = $tokens['color_cream'] ?? '#F8F5F1';
		$dark_default  = $tokens['color_blue_dark'] ?? '#252F37';

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_background',
			[
				'label'       => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Full-width band (extends edge to edge on large screens).',
					'agency-elementor-widgets'
				),
				'type'        => Controls_Manager::COLOR,
				'default'     => $cream_default,
				'selectors'   => [
					'{{WRAPPER}} .aew-split-media' => '--aew-split-media-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 320,
						'max' => 1920,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1440,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__row' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Section padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '64',
					'right'    => '40',
					'bottom'   => '64',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '48',
					'right'    => '24',
					'bottom'   => '48',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '40',
					'right'    => '16',
					'bottom'   => '40',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_column_gap',
			[
				'label'      => esc_html__( 'Gap (unused with background layout)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__row' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'copy_block_width',
			[
				'label'      => esc_html__( 'Copy block width (desktop)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%' => [
						'min' => 30,
						'max' => 70,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media:not(.aew-split-media--compact) .aew-split-media__body' => 'flex: 0 0 {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_body_background',
			[
				'label'     => esc_html__( 'Card background (copy block)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-card-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_body_radius',
			[
				'label'      => esc_html__( 'Card corner radius (copy block)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__body' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'card_body_padding',
			[
				'label'      => esc_html__( 'Card padding (copy block)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '80',
					'right'    => '80',
					'bottom'   => '80',
					'left'     => '80',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'copy_block_margin',
			[
				'label'      => esc_html__( 'Copy block top margin (desktop overlay)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Vertical inset of the copy panel. Left/right margin is always 0 so the panel lines up with the content rail.', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 160, 'step' => 1 ] ],
				'default'    => [ 'size' => '', 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media:not(.aew-split-media--compact) .aew-split-media__row--copy-left .aew-split-media__body' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .aew-split-media:not(.aew-split-media--compact) .aew-split-media__row--copy-right .aew-split-media__body' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'media_padding',
			[
				'label'      => esc_html__( 'Background image inset padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__bg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__bg img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-title: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .aew-split-media__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 64,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 32,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 32,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.15,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Space below heading', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__( 'Paragraphs', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-text: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .aew-split-media__text',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.5,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'paragraph_spacing',
			[
				'label'      => esc_html__( 'Space between paragraphs', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__paragraphs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->register_button_style_controls();
	}

	/**
	 * Style controls for the buttons (colors + spacing). Sizes/padding follow
	 * the design system in CSS; these expose the brand colors so they can be
	 * retoned per instance.
	 *
	 * @return void
	 */
	private function register_button_style_controls(): void {
		$this->start_controls_section(
			'section_style_buttons',
			[
				'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'buttons_gap',
			[
				'label'      => esc_html__( 'Space above / between buttons', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__buttons' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .aew-split-media__buttons:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// ── Size ────────────────────────────────────────────────────────────
		$this->add_control(
			'btn_size_heading',
			[
				'label'     => esc_html__( 'Size', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_width_mode',
			[
				'label'   => esc_html__( 'Width', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto'   => esc_html__( 'Auto (fit label)', 'agency-elementor-widgets' ),
					'full'   => esc_html__( 'Full width', 'agency-elementor-widgets' ),
					'custom' => esc_html__( 'Custom', 'agency-elementor-widgets' ),
				],
				'selectors_dictionary' => [
					'auto'   => 'width: auto; max-width: none;',
					'full'   => 'width: 100%; max-width: none;',
					'custom' => '', // width comes from btn_width below.
				],
				'selectors' => [
					'{{WRAPPER}} .aew-split-media__btn' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'btn_width',
			[
				'label'      => esc_html__( 'Custom width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 80, 'max' => 600 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 240 ],
				'condition'  => [ 'btn_width_mode' => 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__btn' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				],
			]
		);

		$this->add_responsive_control(
			'btn_height',
			[
				'label'      => esc_html__( 'Height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 36, 'max' => 120 ] ],
				'description' => esc_html__( 'Sets a minimum height; leave empty to size from padding.', 'agency-elementor-widgets' ),
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__btn' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .aew-split-media__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Primary colors.
		$this->add_control(
			'btn_primary_bg',
			[
				'label'     => esc_html__( 'Primary background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btnp-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_primary_bg_hover',
			[
				'label'     => esc_html__( 'Primary background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btnp-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_primary_text_color',
			[
				'label'     => esc_html__( 'Primary text', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btnp-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_primary_text_color_hover',
			[
				'label'     => esc_html__( 'Primary text (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btnp-text-hover: {{VALUE}};',
				],
			]
		);

		// Secondary colors.
		$this->add_control(
			'btn_secondary_color',
			[
				'label'     => esc_html__( 'Secondary text / border', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_secondary_hover_bg',
			[
				'label'     => esc_html__( 'Secondary background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btns-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_secondary_hover_text',
			[
				'label'     => esc_html__( 'Secondary text (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-spm-btns-text-hover: {{VALUE}};',
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
	 * Decorative arrow icon used inside buttons. Hidden from a11y tree
	 * (the button label carries the meaning). `position` adds a modifier
	 * so CSS can flip it when placed before the label.
	 *
	 * @param string $position before|after.
	 * @return string
	 */
	private function arrow_svg( string $position = 'after' ): string {
		$mod = 'before' === $position ? ' aew-split-media__btn-arrow--before' : '';
		return '<svg class="aew-split-media__btn-arrow' . esc_attr( $mod ) . '" viewBox="0 0 200 200" width="24" height="24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
			. '<path d="M179.7 96.3H55.9c10.4-6 15.6-15.8 15.6-29.1h-7.3c0 7.2 0 28.9-43 29.1h-.9v7.3c43.2.2 43.2 22 43.2 29.1h7.3c0-13.4-5.3-23.1-15.8-29.1h124.6v-7.3z"/>'
			. '</svg>';
	}

	/**
	 * Renders the inner content of a button: label + optional arrow in the
	 * chosen order. Label is escaped; arrow markup is a fixed, safe constant.
	 *
	 * @param string $label    Button text.
	 * @param bool   $arrow    Whether to show the arrow.
	 * @param string $position before|after.
	 * @return string
	 */
	private function btn_inner( string $label, bool $arrow, string $position ): string {
		$text = '<span class="aew-split-media__btn-label">' . esc_html( $label ) . '</span>';
		if ( ! $arrow ) {
			return $text;
		}
		$icon = $this->arrow_svg( $position );
		return 'before' === $position ? $icon . $text : $text . $icon;
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$copy_side = $settings['copy_block_side'] ?? '';
		if ( ! in_array( $copy_side, [ 'left', 'right' ], true ) ) {
			$legacy = $settings['image_position'] ?? 'left';
			$copy_side = 'right' === $legacy ? 'left' : 'right';
		}

		$image     = $settings['feature_image'] ?? [];
		$image_url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
		if ( '' === $image_url ) {
			$image_url = Widget_Assets::url( self::ASSET_SLUG, 'images/split-media-default.webp' );
		}

		$paragraphs = $settings['paragraphs'] ?? [];
		if ( ! is_array( $paragraphs ) ) {
			$paragraphs = [];
		}

		$breakpoint = max( 768, (int) ( $settings['mobile_breakpoint'] ?? 1024 ) );

		/*
		 * Resolved colours as inline CSS vars on the wrapper. get_settings_for_display()
		 * resolves global colours → hex so global-bound picks render on the front end
		 * (Elementor drops globals for direct-property custom-control selectors). The
		 * `selectors` on each control drive the editor preview; this inline value wins
		 * on live. CSS consumes each var with a design-system fallback. section_background
		 * is included too for the same live-global safety (its own --aew-split-media-bg
		 * var is already consumed by the stylesheet).
		 */
		$color_vars = Color_Vars::build(
			$this,
			$settings,
			[
				'section_background'       => '--aew-split-media-bg',
				'card_body_background'     => '--aew-spm-card-bg',
				'title_color'              => '--aew-spm-title',
				'text_color'               => '--aew-spm-text',
				'btn_primary_bg'           => '--aew-spm-btnp-bg',
				'btn_primary_bg_hover'     => '--aew-spm-btnp-bg-hover',
				'btn_primary_text_color'   => '--aew-spm-btnp-text',
				'btn_primary_text_color_hover' => '--aew-spm-btnp-text-hover',
				'btn_secondary_color'      => '--aew-spm-btns',
				'btn_secondary_hover_bg'   => '--aew-spm-btns-bg-hover',
				'btn_secondary_hover_text' => '--aew-spm-btns-text-hover',
			]
		);

		$this->add_render_attribute( 'wrapper', 'class', 'aew-split-media' );
		$this->add_render_attribute( 'wrapper', 'data-aew-split-media', '' );
		$this->add_render_attribute(
			'wrapper',
			'style',
			sprintf(
				'--aew-split-media-bp:%d;--aew-split-media-image-url:url(%s);',
				$breakpoint,
				esc_url( $image_url )
			) . $color_vars
		);
		$this->add_render_attribute( 'inner', 'class', 'aew-split-media__inner' );
		$this->add_render_attribute( 'row', 'class', 'aew-split-media__row aew-split-media__row--copy-' . $copy_side );

		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<article <?php $this->print_render_attribute_string( 'row' ); ?>>
					<div class="aew-split-media__body">
						<?php if ( ! empty( $settings['heading'] ) ) : ?>
							<?php
							$this->add_render_attribute( 'heading', 'class', 'aew-split-media__title' );
							$this->add_inline_editing_attributes( 'heading', 'none' );
							?>
							<h2 <?php $this->print_render_attribute_string( 'heading' ); ?>>
								<?php echo esc_html( $settings['heading'] ); ?>
							</h2>
						<?php endif; ?>
						<?php if ( ! empty( $paragraphs ) ) : ?>
							<div class="aew-split-media__paragraphs">
								<?php foreach ( $paragraphs as $index => $row ) : ?>
									<?php
									if ( Rich_Text::is_empty( (string) ( $row['paragraph_text'] ?? '' ) ) ) {
										continue;
									}
									$key = $this->get_repeater_setting_key( 'paragraph_text', 'paragraphs', $index );
									$this->add_render_attribute( $key, 'class', 'aew-split-media__text aew-rich-text' );
									$this->add_inline_editing_attributes( $key, 'advanced' );
									?>
									<div <?php $this->print_render_attribute_string( $key ); ?>>
										<?php Rich_Text::echo_html( (string) $row['paragraph_text'] ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php
						$show_primary   = 'yes' === ( $settings['btn_primary_show'] ?? '' ) && '' !== trim( (string) ( $settings['btn_primary_text'] ?? '' ) );
						$show_secondary = 'yes' === ( $settings['btn_secondary_show'] ?? '' ) && '' !== trim( (string) ( $settings['btn_secondary_text'] ?? '' ) );
						if ( $show_primary || $show_secondary ) :
							$p_link = $this->parse_link( $settings['btn_primary_link'] ?? [] );
							$s_link = $this->parse_link( $settings['btn_secondary_link'] ?? [] );

							$p_arrow = 'yes' === ( $settings['btn_primary_arrow'] ?? '' );
							$p_pos   = 'before' === ( $settings['btn_primary_arrow_pos'] ?? 'after' ) ? 'before' : 'after';
							$s_arrow = 'yes' === ( $settings['btn_secondary_arrow'] ?? '' );
							$s_pos   = 'before' === ( $settings['btn_secondary_arrow_pos'] ?? 'after' ) ? 'before' : 'after';
							?>
							<div class="aew-split-media__buttons">
								<?php if ( $show_primary ) : ?>
									<a class="aew-split-media__btn aew-split-media__btn--primary"
										href="<?php echo esc_url( $p_link['url'] ?: '#' ); ?>"
										<?php echo $p_link['target'] ? 'target="' . esc_attr( $p_link['target'] ) . '"' : ''; ?>
										<?php echo $p_link['rel'] ? 'rel="' . esc_attr( $p_link['rel'] ) . '"' : ''; ?>>
										<?php echo $this->btn_inner( (string) $settings['btn_primary_text'], $p_arrow, $p_pos ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — label escaped inside, arrow is a fixed safe constant ?>
									</a>
								<?php endif; ?>
								<?php if ( $show_secondary ) : ?>
									<a class="aew-split-media__btn aew-split-media__btn--secondary"
										href="<?php echo esc_url( $s_link['url'] ?: '#' ); ?>"
										<?php echo $s_link['target'] ? 'target="' . esc_attr( $s_link['target'] ) . '"' : ''; ?>
										<?php echo $s_link['rel'] ? 'rel="' . esc_attr( $s_link['rel'] ) . '"' : ''; ?>>
										<?php echo $this->btn_inner( (string) $settings['btn_secondary_text'], $s_arrow, $s_pos ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — label escaped inside, arrow is a fixed safe constant ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="aew-split-media__bg" aria-hidden="true">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" width="1440" height="960" loading="eager" decoding="async" />
					</div>
				</article>
			</div>
		</section>
		<?php
	}
}
