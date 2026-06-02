<?php
/**
 * FAQ Accordion Elementor widget (Frame 26).
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
 * FAQ-style fear accordion — mobile-first (Frame 26).
 */
class Widget_Faq_Accordion extends Widget_Base {

	private const ASSET_SLUG = 'faq-accordion';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-faq-accordion';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'FAQ Accordion', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-accordion';
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
		return [ 'accordion', 'faq', 'questions', 'answers' ];
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function default_items(): array {
		return [
			[
				'question' => esc_html__(
					'What if the price changes once the walls open up?',
					'agency-elementor-widgets'
				),
				'answer'   => esc_html__(
					'This is the most common fear — and the most justified. Most builders quote before they know what is behind your walls. When surprises show up mid-project, the price moves. Bright Homes investigates structure, systems, and scope before your quote is final, so the number you approve is the number you pay.',
					'agency-elementor-widgets'
				),
				'open_by_default' => 'yes',
			],
			[
				'question' => esc_html__(
					'What if I fall in love with a design I can\'t actually build for my budget?',
					'agency-elementor-widgets'
				),
				'answer'   => esc_html__(
					'You should see realistic options early — not after months of planning. We align design decisions with your budget from the start and show you what is possible before you get attached to something that cannot be built.',
					'agency-elementor-widgets'
				),
			],
			[
				'question' => esc_html__(
					'What if the builder disappears once the contract is signed?',
					'agency-elementor-widgets'
				),
				'answer'   => esc_html__(
					'A clear timeline, defined milestones, and consistent communication are part of how we work — not extras you hope for later. You always know who is on your project and what happens next.',
					'agency-elementor-widgets'
				),
			],
			[
				'question' => esc_html__(
					'What if I can\'t see what I\'m getting until it\'s too late to change it?',
					'agency-elementor-widgets'
				),
				'answer'   => esc_html__(
					'You review layouts, finishes, and details before construction starts. Decisions are made when changes are still practical — not when walls are already closed up.',
					'agency-elementor-widgets'
				),
			],
			[
				'question' => esc_html__(
					'What if I\'m forced to make rushed decisions in the middle of the project?',
					'agency-elementor-widgets'
				),
				'answer'   => esc_html__(
					'When planning is done properly upfront, most choices are settled before demo day. That means fewer panic decisions under pressure and a calmer experience while your home is under construction.',
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
			'section_intro',
			[
				'label' => esc_html__( 'Intro', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Show heading', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'title',
			[
				'label'     => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__(
					'What Part Of Your Home Would You Like to Transform?',
					'agency-elementor-widgets'
				),
				'rows'      => 2,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'intro_text',
			[
				'label'   => esc_html__( 'Intro text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'Most people who start looking for a renovation builder carry something with them into every conversation. A story they heard. A friend who went through it. A quiet anxiety they can\'t quite shake.',
					'agency-elementor-widgets'
				),
				'rows'    => 4,
			]
		);

		$this->add_control(
			'subheading',
			[
				'label'   => esc_html__( 'Subheading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__(
					'And underneath all of it, the same fears.',
					'agency-elementor-widgets'
				),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_accordion',
			[
				'label' => esc_html__( 'Accordion', 'agency-elementor-widgets' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'question',
			[
				'label'   => esc_html__( 'Question', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Accordion question', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'answer',
			[
				'label'   => esc_html__( 'Answer', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Accordion answer.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'open_by_default',
			[
				'label'        => esc_html__( 'Open by default', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Items', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_items(),
				'title_field' => '{{{ question }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_closing',
			[
				'label' => esc_html__( 'Closing', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'closing_lead',
			[
				'label'   => esc_html__( 'Lead line (italic)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__(
					'These aren\'t irrational fears.',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'closing_text',
			[
				'label'   => esc_html__( 'Closing text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'They\'re what happens on renovation projects that start without enough planning. Here\'s how Bright Homes makes sure none of them happen on your project.',
					'agency-elementor-widgets'
				),
				'rows'    => 4,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens        = Design_Tokens::get();
		$blue_default  = $tokens['color_blue'] ?? '#305B83';
		$light_default = $tokens['color_blue_light'] ?? '#5796BB';

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
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $blue_default,
				'selectors' => [
					'{{WRAPPER}} .aew-faq-accordion' => '--aew-faq-accordion-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_max_width',
			[
				'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .aew-faq-accordion__inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Section padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '80',
					'right'    => '16',
					'bottom'   => '80',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_intro',
			[
				'label' => esc_html__( 'Intro', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'intro_gap',
			[
				'label'      => esc_html__( 'Intro spacing', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-faq-accordion__intro' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'intro_bottom_spacing',
			[
				'label'      => esc_html__( 'Space below intro', 'agency-elementor-widgets' ),
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
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__intro' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Heading typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__title',
				'condition' => [
					'show_title' => 'yes',
				],
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'DM Serif Display' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 64,
						],
						'tablet_default' => [
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'intro_typography',
				'label'    => esc_html__( 'Intro text typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__intro-text',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'tablet_default' => [
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subheading_typography',
				'label'    => esc_html__( 'Subheading typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__subheading',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'tablet_default' => [
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
			'subheading_bold',
			[
				'label'     => esc_html__( 'Subheading weight', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '700',
				'options'   => [
					'400' => esc_html__( 'Regular', 'agency-elementor-widgets' ),
					'600' => esc_html__( 'Semi bold', 'agency-elementor-widgets' ),
					'700' => esc_html__( 'Bold', 'agency-elementor-widgets' ),
				],
				'selectors' => [
					'{{WRAPPER}} .aew-faq-accordion__subheading' => 'font-weight: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_accordion',
			[
				'label' => esc_html__( 'Accordion', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_background',
			[
				'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $light_default,
				'selectors' => [
					'{{WRAPPER}} .aew-faq-accordion' => '--aew-faq-accordion-card-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
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
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__accordion' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Card radius', 'agency-elementor-widgets' ),
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
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'trigger_padding',
			[
				'label'      => esc_html__( 'Trigger padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '64',
					'right'    => '64',
					'bottom'   => '64',
					'left'     => '64',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'tablet_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'panel_padding',
			[
				'label'      => esc_html__( 'Answer padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '0',
					'right'    => '64',
					'bottom'   => '64',
					'left'     => '64',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '0',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '0',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'question_typography',
				'label'    => esc_html__( 'Question typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__question',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'DM Serif Display' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 48,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 24,
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'answer_typography',
				'label'    => esc_html__( 'Answer typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__answer',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'tablet_default' => [
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_closing',
			[
				'label' => esc_html__( 'Closing', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'closing_top_spacing',
			[
				'label'      => esc_html__( 'Space above closing', 'agency-elementor-widgets' ),
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
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-faq-accordion__closing' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'closing_align',
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
					'{{WRAPPER}} .aew-faq-accordion__closing' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'closing_copy_typography',
				'label'    => esc_html__( 'Closing typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-faq-accordion__closing-copy',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'DM Serif Display' ],
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
			'closing_lead_color',
			[
				'label'     => esc_html__( 'Lead color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-faq-accordion__closing-lead' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'closing_text_color',
			[
				'label'     => esc_html__( 'Closing text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $light_default,
				'selectors' => [
					'{{WRAPPER}} .aew-faq-accordion__closing-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? [];

		if ( ! is_array( $items ) ) {
			$items = [];
		}

		$default_open = null;

		foreach ( $items as $index => $item ) {
			if ( ! empty( $item['open_by_default'] ) && 'yes' === $item['open_by_default'] ) {
				$default_open = $index;
				break;
			}
		}

		if ( null === $default_open && ! empty( $items ) ) {
			$default_open = 0;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-faq-accordion' );
		$this->add_render_attribute( 'wrapper', 'data-aew-faq-accordion', '' );
		$this->add_render_attribute( 'inner', 'class', 'aew-faq-accordion__inner' );
		$this->add_render_attribute( 'accordion', 'class', 'aew-faq-accordion__accordion' );
		$this->add_render_attribute( 'accordion', 'data-aew-faq-accordion-list', '' );

		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<header class="aew-faq-accordion__intro">
					<?php if ( ! empty( $settings['show_title'] ) && ! empty( $settings['title'] ) ) : ?>
						<?php
						$this->add_render_attribute( 'title', 'class', 'aew-faq-accordion__title' );
						$this->add_inline_editing_attributes( 'title', 'none' );
						?>
						<h2 <?php $this->print_render_attribute_string( 'title' ); ?>>
							<?php echo esc_html( $settings['title'] ); ?>
						</h2>
					<?php endif; ?>
					<?php if ( ! Rich_Text::is_empty( (string) ( $settings['intro_text'] ?? '' ) ) ) : ?>
						<?php
						$this->add_render_attribute( 'intro_text', 'class', 'aew-faq-accordion__intro-text aew-rich-text' );
						$this->add_inline_editing_attributes( 'intro_text', 'advanced' );
						?>
						<div <?php $this->print_render_attribute_string( 'intro_text' ); ?>>
							<?php Rich_Text::echo_html( (string) $settings['intro_text'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $settings['subheading'] ) ) : ?>
						<?php
						$this->add_render_attribute( 'subheading', 'class', 'aew-faq-accordion__subheading' );
						$this->add_inline_editing_attributes( 'subheading', 'none' );
						?>
						<p <?php $this->print_render_attribute_string( 'subheading' ); ?>>
							<?php echo esc_html( $settings['subheading'] ); ?>
						</p>
					<?php endif; ?>
				</header>

				<?php if ( ! empty( $items ) ) : ?>
					<div <?php $this->print_render_attribute_string( 'accordion' ); ?>>
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							if ( empty( $item['question'] ) ) {
								continue;
							}

							$is_open     = (int) $index === (int) $default_open;
							$item_key    = 'item_' . $index;
							$trigger_key = 'trigger_' . $index;
							$panel_key   = 'panel_' . $index;
							$question_key = $this->get_repeater_setting_key( 'question', 'items', $index );
							$answer_key   = $this->get_repeater_setting_key( 'answer', 'items', $index );

							$this->add_render_attribute(
								$item_key,
								'class',
								'aew-faq-accordion__item' . ( $is_open ? ' is-open' : '' )
							);
							$this->add_render_attribute( $trigger_key, 'class', 'aew-faq-accordion__trigger' );
							$this->add_render_attribute( $trigger_key, 'type', 'button' );
							$this->add_render_attribute( $trigger_key, 'aria-expanded', $is_open ? 'true' : 'false' );
							$this->add_render_attribute( $panel_key, 'class', 'aew-faq-accordion__panel' );
							$this->add_render_attribute( $panel_key, 'id', 'aew-faq-accordion-panel-' . $this->get_id() . '-' . $index );

							if ( ! $is_open ) {
								$this->add_render_attribute( $panel_key, 'hidden', 'hidden' );
							}

							$this->add_render_attribute( $question_key, 'class', 'aew-faq-accordion__question' );
							$this->add_inline_editing_attributes( $question_key, 'none' );
							$this->add_render_attribute( $answer_key, 'class', 'aew-faq-accordion__answer aew-rich-text' );
							$this->add_inline_editing_attributes( $answer_key, 'advanced' );
							?>
							<article <?php $this->print_render_attribute_string( $item_key ); ?>>
								<button <?php $this->print_render_attribute_string( $trigger_key ); ?>>
									<span <?php $this->print_render_attribute_string( $question_key ); ?>>
										<?php echo esc_html( $item['question'] ); ?>
									</span>
									<span class="aew-faq-accordion__icon" aria-hidden="true"></span>
								</button>
								<div <?php $this->print_render_attribute_string( $panel_key ); ?>>
									<?php if ( ! Rich_Text::is_empty( (string) ( $item['answer'] ?? '' ) ) ) : ?>
										<div <?php $this->print_render_attribute_string( $answer_key ); ?>>
											<?php Rich_Text::echo_html( (string) $item['answer'] ); ?>
										</div>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['closing_lead'] ) || ! Rich_Text::is_empty( (string) ( $settings['closing_text'] ?? '' ) ) ) : ?>
					<footer class="aew-faq-accordion__closing">
						<p class="aew-faq-accordion__closing-copy">
							<?php if ( ! empty( $settings['closing_lead'] ) ) : ?>
								<?php
								$this->add_render_attribute( 'closing_lead', 'class', 'aew-faq-accordion__closing-lead' );
								$this->add_inline_editing_attributes( 'closing_lead', 'none' );
								?>
								<span <?php $this->print_render_attribute_string( 'closing_lead' ); ?>>
									<em><?php echo esc_html( $settings['closing_lead'] ); ?></em>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $settings['closing_lead'] ) && ! Rich_Text::is_empty( (string) ( $settings['closing_text'] ?? '' ) ) ) : ?>
								<?php echo ' '; ?>
							<?php endif; ?>
							<?php if ( ! Rich_Text::is_empty( (string) ( $settings['closing_text'] ?? '' ) ) ) : ?>
								<?php
								$this->add_render_attribute( 'closing_text', 'class', 'aew-faq-accordion__closing-text aew-rich-text' );
								$this->add_inline_editing_attributes( 'closing_text', 'advanced' );
								?>
								<span <?php $this->print_render_attribute_string( 'closing_text' ); ?>>
									<?php Rich_Text::echo_html( (string) $settings['closing_text'] ); ?>
								</span>
							<?php endif; ?>
						</p>
					</footer>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
