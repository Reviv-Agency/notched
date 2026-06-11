<?php
/**
 * Heading Band Elementor widget (Frame 16 + Frame 29 paragraph).
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
 * Centered prompt heading band — Frame 16.
 */
class Widget_Heading_Band extends Widget_Base {

	private const ASSET_SLUG = 'heading-band';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-heading-band';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Heading Band', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-heading';
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
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'heading', 'title', 'transform', 'prompt', 'banner' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_eyebrow',
			[
				'label'        => esc_html__( 'Show eyebrow', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'eyebrow',
			[
				'label'     => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'HOW IT WORKS', 'agency-elementor-widgets' ),
				'condition' => [
					'show_eyebrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					"What Part Of Your Home Would\nYou Like to Transform?",
					'agency-elementor-widgets'
				),
				'rows'    => 3,
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label'   => esc_html__( 'HTML tag', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'p'  => 'P',
				],
			]
		);

		$paragraph_repeater = new Repeater();

		$paragraph_repeater->add_control(
			'text',
			[
				'label'   => esc_html__( 'Text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$this->add_control(
			'paragraphs',
			[
				'label'       => esc_html__( 'Paragraphs', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Add, remove, or reorder paragraphs below the heading.',
					'agency-elementor-widgets'
				),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $paragraph_repeater->get_controls(),
				'default'     => [
					[
						'text' => esc_html__(
							'From the first conversation to the final walkthrough, <strong>construction feels calm, predictable, and fully under control.</strong>',
							'agency-elementor-widgets'
						),
					],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'        => esc_html__( 'Show button', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => esc_html__( 'Button text', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Book Your FREE Consultation', 'agency-elementor-widgets' ),
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'       => esc_html__( 'Button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://', 'agency-elementor-widgets' ),
				'default'     => [
					'url' => '#',
				],
				'condition'   => [
					'show_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$cream_default = Design_Tokens::get()['color_cream'] ?? '#F8F5F1';

		$this->add_control(
			'section_background',
			[
				'label'       => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Full-width band (extends edge to edge on large screens). Heading content stays within max width.',
					'agency-elementor-widgets'
				),
				'type'        => Controls_Manager::COLOR,
				'default'     => $cream_default,
				'selectors'   => [
					'{{WRAPPER}} .aew-heading-band' => '--aew-heading-band-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_max_width',
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
					'{{WRAPPER}} .aew-heading-band__inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_min_height',
			[
				'label'      => esc_html__( 'Section height (min-height)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 600,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 288,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__inner' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'       => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Padding on all sides of the cream band inner box. Adjust per breakpoint (desktop, tablet, mobile).',
					'agency-elementor-widgets'
				),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '24',
					'right'    => '40',
					'bottom'   => '24',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '24',
					'right'    => '24',
					'bottom'   => '24',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '80',
					'right'    => '16',
					'bottom'   => '24',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$blue_default = Design_Tokens::get()['color_blue'] ?? '#305B83';

		$this->start_controls_section(
			'section_style_eyebrow',
			[
				'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eyebrow_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $blue_default,
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__eyebrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eyebrow_uppercase',
			[
				'label'        => esc_html__( 'Uppercase', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => [
					'{{WRAPPER}} .aew-heading-band__eyebrow' => 'text-transform: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'yes' => 'uppercase',
					''    => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'eyebrow_spacing',
			[
				'label'      => esc_html__( 'Space below eyebrow', 'agency-elementor-widgets' ),
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
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__eyebrow' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'eyebrow_typography',
				'selector'       => '{{WRAPPER}} .aew-heading-band__eyebrow',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 20,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.3,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_heading',
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
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_align',
			[
				'label'     => esc_html__( 'Alignment', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .aew-heading-band__title',
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 64,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_paragraph',
			[
				'label' => esc_html__( 'Paragraphs', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$dark_default = Design_Tokens::get()['color_blue_dark'] ?? '#252F37';

		$this->add_control(
			'paragraph_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__paragraph' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'paragraph_align',
			[
				'label'     => esc_html__( 'Alignment', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__paragraph' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'paragraphs_top_spacing',
			[
				'label'      => esc_html__( 'Space above paragraphs', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-heading-band__paragraphs' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'paragraphs_gap',
			[
				'label'      => esc_html__( 'Gap between paragraphs', 'agency-elementor-widgets' ),
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
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__paragraphs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'paragraph_typography',
				'selector' => '{{WRAPPER}} .aew-heading-band__paragraph',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 14,
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

		$this->add_control(
			'paragraph_bold_weight',
			[
				'label'     => esc_html__( 'Bold weight', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '700',
				'options'   => [
					'600' => esc_html__( 'Semi bold', 'agency-elementor-widgets' ),
					'700' => esc_html__( 'Bold', 'agency-elementor-widgets' ),
				],
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__paragraph strong' => 'font-weight: {{VALUE}};',
					'{{WRAPPER}} .aew-heading-band__paragraph b' => 'font-weight: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$yellow_default = Design_Tokens::get()['color_yellow'] ?? '#EBC543';

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_top_spacing',
			[
				'label'      => esc_html__( 'Space above button', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-heading-band__button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__button:hover'         => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .aew-heading-band__button:focus-visible' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-heading-band__button:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-heading-band__button:focus-visible' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
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
					'size' => 18,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '16',
					'right'    => '24',
					'bottom'   => '16',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-heading-band__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .aew-heading-band__button',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 18,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.2,
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed>|string $link_data URL control value.
	 * @return array{url: string, target: string, rel: string}
	 */
	private function parse_link( $link_data ): array {
		if ( ! is_array( $link_data ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}

		$url    = $link_data['url'] ?? '';
		$target = ! empty( $link_data['is_external'] ) ? '_blank' : '';
		$rel    = [];
		if ( $target ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link_data['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		return [
			'url'    => $url,
			'target' => $target,
			'rel'    => implode( ' ', $rel ),
		];
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<int, array{text: string}>
	 */
	private function get_paragraphs( array $settings ): array {
		$items = $settings['paragraphs'] ?? [];

		if ( is_array( $items ) && ! empty( $items ) ) {
			$paragraphs = [];

			foreach ( $items as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				$text = (string) ( $item['text'] ?? '' );
				if ( Rich_Text::is_empty( $text ) ) {
					continue;
				}

				$paragraphs[] = [
					'text' => $text,
				];
			}

			return $paragraphs;
		}

		$legacy_text = (string) ( $settings['paragraph'] ?? '' );
		if ( Rich_Text::is_empty( $legacy_text ) ) {
			return [];
		}

		return [
			[
				'text' => $legacy_text,
			],
		];
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$heading     = trim( (string) ( $settings['heading'] ?? '' ) );
		$paragraphs  = $this->get_paragraphs( $settings );
		$show_eyebrow = 'yes' === ( $settings['show_eyebrow'] ?? '' );
		$eyebrow     = trim( (string) ( $settings['eyebrow'] ?? '' ) );

		$show_button = 'yes' === ( $settings['show_button'] ?? '' );
		$button_text = trim( (string) ( $settings['button_text'] ?? '' ) );
		$button_link = $this->parse_link( $settings['button_link'] ?? [] );

		if (
			'' === $heading
			&& empty( $paragraphs )
			&& ( ! $show_eyebrow || '' === $eyebrow )
			&& ( ! $show_button || '' === $button_text || '' === $button_link['url'] )
		) {
			return;
		}

		$tag = $settings['heading_tag'] ?? 'h2';
		if ( ! in_array( $tag, [ 'h1', 'h2', 'h3', 'h4', 'p' ], true ) ) {
			$tag = 'h2';
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-heading-band__inner' );
		$this->add_render_attribute( 'heading', 'class', 'aew-heading-band__title' );
		$this->add_inline_editing_attributes( 'heading', 'none' );

		if ( $show_eyebrow && '' !== $eyebrow ) {
			$this->add_render_attribute( 'eyebrow', 'class', 'aew-heading-band__eyebrow' );
			$this->add_inline_editing_attributes( 'eyebrow', 'none' );
		}

		?>
		<section class="aew-heading-band">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div class="aew-heading-band__content">
					<?php if ( $show_eyebrow && '' !== $eyebrow ) : ?>
						<p <?php $this->print_render_attribute_string( 'eyebrow' ); ?>>
							<?php echo esc_html( $eyebrow ); ?>
						</p>
					<?php endif; ?>
					<?php if ( '' !== $heading ) : ?>
						<?php printf( '<%s ', esc_attr( $tag ) ); ?>
						<?php $this->print_render_attribute_string( 'heading' ); ?>
						><?php echo esc_html( $heading ); ?><?php printf( '</%s>', esc_attr( $tag ) ); ?>
					<?php endif; ?>
					<?php if ( ! empty( $paragraphs ) ) : ?>
						<div class="aew-heading-band__paragraphs">
							<?php foreach ( $paragraphs as $index => $item ) : ?>
								<?php
								$paragraph_key = 'paragraph_' . $index;
								$text_key      = $this->get_repeater_setting_key( 'text', 'paragraphs', $index );

								$this->add_render_attribute( $paragraph_key, 'class', 'aew-heading-band__paragraph aew-rich-text' );
								$this->add_inline_editing_attributes( $text_key, 'advanced' );
								?>
								<div <?php $this->print_render_attribute_string( $paragraph_key ); ?>>
									<span <?php $this->print_render_attribute_string( $text_key ); ?>>
										<?php Rich_Text::echo_html( $item['text'] ); ?>
									</span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $show_button && '' !== $button_text && '' !== $button_link['url'] ) : ?>
						<?php
						$this->add_render_attribute( 'button', 'class', 'aew-heading-band__button' );
						$this->add_render_attribute( 'button', 'href', esc_url( $button_link['url'] ) );
						$this->add_inline_editing_attributes( 'button_text', 'none' );
						if ( $button_link['target'] ) {
							$this->add_render_attribute( 'button', 'target', $button_link['target'] );
						}
						if ( $button_link['rel'] ) {
							$this->add_render_attribute( 'button', 'rel', $button_link['rel'] );
						}
						?>
						<a <?php $this->print_render_attribute_string( 'button' ); ?>>
							<span <?php $this->print_render_attribute_string( 'button_text' ); ?>>
								<?php echo esc_html( $button_text ); ?>
							</span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
