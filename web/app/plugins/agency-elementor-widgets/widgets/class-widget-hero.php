<?php
/**
 * Hero Elementor widget.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Universal hero section widget (desktop Frame 3, mobile Frame 73).
 */
class Widget_Hero extends Widget_Base {

	private const ASSET_SLUG = 'hero';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-hero';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Hero', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-banner';
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
		return [ 'hero', 'banner', 'cta', 'features' ];
	}

	/**
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );

		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['label']       = esc_html__( 'Hero shell padding', 'agency-elementor-widgets' );
			$stack['controls']['_padding']['description'] = esc_html__(
				'Padding around the hero shell (rounded container).',
				'agency-elementor-widgets'
			);
			$stack['controls']['_padding']['default']        = [
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => true,
			];
			$stack['controls']['_padding']['selectors']      = [
				'{{WRAPPER}} .aew-hero__shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			];
		}

		return $stack;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function default_features(): array {
		$text = esc_html__(
			'We investigate what\'s behind your walls and plan every detail before quoting, so your price stays locked from start to finish.',
			'agency-elementor-widgets'
		);

		return [
			[
				'feature_title'       => esc_html__( 'No surprise costs', 'agency-elementor-widgets' ),
				'feature_description' => $text,
			],
			[
				'feature_title'       => esc_html__( 'No surprise costs', 'agency-elementor-widgets' ),
				'feature_description' => $text,
			],
			[
				'feature_title'       => esc_html__( 'No surprise costs', 'agency-elementor-widgets' ),
				'feature_description' => $text,
			],
		];
	}

	/**
	 * @return void
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_background',
			[
				'label' => esc_html__( 'Background', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'background_image',
			[
				'label'   => esc_html__( 'Background image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/hero-default.webp' ),
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__( 'Overlay color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__visual' => '--aew-hero-overlay-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label'      => esc_html__( 'Overlay opacity', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'custom' ],
				'range'      => [
					'custom' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'    => [
					'unit' => 'custom',
					'size' => 0.5,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__visual' => '--aew-hero-overlay-opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ),
			]
		);

		$this->add_responsive_control(
			'shell_max_width',
			[
				'label'      => esc_html__( 'Shell max width', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-hero__shell' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inner_max_width',
			[
				'label'      => esc_html__( 'Content area max width', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-hero__inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'shell_radius',
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
					'size' => 24,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__shell' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label'       => esc_html__( 'Mobile & tablet max width (px)', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 768,
				'min'         => 768,
				'max'         => 1400,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'eyebrow_desktop',
			[
				'label'   => esc_html__( 'Eyebrow (desktop)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__(
					'RENOVATIONS THAT TURN OUT EXACTLY AS YOU PICTURED AND COST EXACTLY AS QUOTED',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'eyebrow_mobile',
			[
				'label'   => esc_html__( 'Eyebrow (mobile)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__(
					'FOR SOUTH SALT LAKE & NORTH UTAH COUNTY HOMEOWNERS',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'headline',
			[
				'label'   => esc_html__( 'Headline', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					'Renovations That Turn Out Exactly as You Pictured and Cost Exactly as Quoted',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'headline_mobile_html',
			[
				'label'       => esc_html__( 'Headline (mobile HTML)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Optional. Use &lt;em&gt; for italic. Leave empty to use the main headline.', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'Renovations That Turn Out Exactly as You Pictured and <em>Cost Exactly as Quoted</em>',
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'We fully design and plan your renovation before construction starts, so you know exactly what you\'re getting and exactly what it costs',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label'   => esc_html__( 'CTA text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Book Your FREE Consultation', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'cta_link',
			[
				'label'       => esc_html__( 'CTA link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#',
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social',
			[
				'label' => esc_html__( 'Social proof', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_social_proof',
			[
				'label'   => esc_html__( 'Show social proof', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'social_proof_text',
			[
				'label'     => esc_html__( 'Label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Over 2,000 Happy Clients & Counting', 'agency-elementor-widgets' ),
				'condition' => [
					'show_social_proof' => 'yes',
				],
			]
		);

		$this->add_control(
			'star_count',
			[
				'label'     => esc_html__( 'Star count', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 5,
				'condition' => [
					'show_social_proof' => 'yes',
				],
			]
		);

		foreach ( [ 1, 2, 3 ] as $i ) {
			$this->add_control(
				'avatar_' . $i,
				[
					'label'     => sprintf(
						/* translators: %d: avatar number */
						esc_html__( 'Avatar %d', 'agency-elementor-widgets' ),
						$i
					),
					'type'      => Controls_Manager::MEDIA,
					'default'   => [
						'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/avatar-' . $i . '.webp' ),
					],
					'condition' => [
						'show_social_proof' => 'yes',
					],
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features',
			[
				'label' => esc_html__( 'Feature cards', 'agency-elementor-widgets' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'feature_title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'No surprise costs', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'feature_description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'We investigate what\'s behind your walls and plan every detail before quoting, so your price stays locked from start to finish.',
					'agency-elementor-widgets'
				),
			]
		);

		$repeater->add_control(
			'feature_icon',
			[
				'label'   => esc_html__( 'Icon (optional)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/icon-check.svg' ),
				],
			]
		);

		$this->add_control(
			'features',
			[
				'label'       => esc_html__( 'Features', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_features(),
				'title_field' => '{{{ feature_title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_style_visual',
			[
				'label' => esc_html__( 'Hero visual', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'visual_min_height',
			[
				'label'      => esc_html__( 'Min height (desktop overlay)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1400,
					],
					'vh' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1130,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__visual' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visual_padding',
			[
				'label'      => esc_html__( 'Content area padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visual_border_radius',
			[
				'label'      => esc_html__( 'Background image radius', 'agency-elementor-widgets' ),
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
					'size' => 24,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__visual' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visual_band_height',
			[
				'label'      => esc_html__( 'Image band height (mobile)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 160,
						'max' => 500,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__visual' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_text_on_image',
			[
				'label'     => esc_html__( 'Text on image', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__copy' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__eyebrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_text_on_cream',
			[
				'label'     => esc_html__( 'Text on cream panel', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252F37',
				'selectors' => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eyebrow_typography',
				'label'    => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__eyebrow',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
					'letter_spacing' => [
						'default' => [
							'unit' => 'px',
							'size' => 0.5,
						],
					],
				],
			]
		);

		$headline_desktop_selector = '{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__headline--desktop, {{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__headline--shared';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'headline_typography',
				'label'    => esc_html__( 'Headline (on image)', 'agency-elementor-widgets' ),
				'selector' => $headline_desktop_selector,
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 80,
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
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'headline_text_stroke',
				'label'    => esc_html__( 'Headline stroke (on image)', 'agency-elementor-widgets' ),
				'selector' => $headline_desktop_selector,
				'fields_options' => [
					'text_stroke' => [
						'default' => [
							'unit' => 'px',
							'size' => 1,
						],
					],
					'stroke_color' => [
						'default' => '#000000',
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'headline_mobile_typography',
				'label'    => esc_html__( 'Headline (mobile panel)', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__headline--mobile',
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 38,
						],
					],
					'font_style' => [ 'default' => 'normal' ],
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
				'name'     => 'description_typography',
				'label'    => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__description',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cta',
			[
				'label' => esc_html__( 'CTA button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cta_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3C400',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__cta' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252F37',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_background_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__cta:hover'         => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero__cta:focus-visible' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_color_hover',
			[
				'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__cta:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero__cta:focus-visible' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cta_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '12',
					'right'    => '24',
					'bottom'   => '12',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cta_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 32,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .aew-hero__cta',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 16,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_features',
			[
				'label' => esc_html__( 'Feature cards', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'feature_card_bg',
			[
				'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#305B83',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__feature' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'feature_title_color',
			[
				'label'     => esc_html__( 'Title color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__feature-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'feature_text_color',
			[
				'label'     => esc_html__( 'Description color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.9)',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__feature-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'feature_title_typography',
				'label'    => esc_html__( 'Title typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__feature-title',
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 32,
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
				'name'     => 'feature_text_typography',
				'label'    => esc_html__( 'Description typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__feature-text',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_size'   => [
						'default' => [
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
			'features_gap',
			[
				'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
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
					'size' => 12,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 64,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__features' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_area_padding',
			[
				'label'      => esc_html__( 'Features area padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '40',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '0',
					'right'    => '16',
					'bottom'   => '24',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '0',
					'right'    => '16',
					'bottom'   => '24',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_card_max_width',
			[
				'label'      => esc_html__( 'Card max width (desktop row)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 392,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__feature' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_card_min_height',
			[
				'label'      => esc_html__( 'Card min height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 120,
						'max' => 400,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 194,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__feature' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_card_radius',
			[
				'label'      => esc_html__( 'Card border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 32,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__feature' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_icon_size',
			[
				'label'      => esc_html__( 'Check icon size', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 80,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__feature-icon-badge img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'feature_icon_bg',
			[
				'label'     => esc_html__( 'Check icon background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#305B83',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__feature-icon-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'feature_icon_border_color',
			[
				'label'     => esc_html__( 'Check icon border color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#305B83',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__feature-icon-badge' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_icon_border_width',
			[
				'label'      => esc_html__( 'Check icon border width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 16,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__feature-icon-badge' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_responsive_control(
			'feature_padding',
			[
				'label'      => esc_html__( 'Card padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '40',
					'right'    => '24',
					'bottom'   => '28',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__feature' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_copy_panel',
			[
				'label' => esc_html__( 'Copy panel (mobile)', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$cream_default = Design_Tokens::get()['color_cream'] ?? '#F8F5F1';

		$this->add_control(
			'cream_panel_bg',
			[
				'label'       => esc_html__( 'Section background', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Full-width band behind the hero (extends edge to edge on large screens). Content stays within max width.',
					'agency-elementor-widgets'
				),
				'type'        => Controls_Manager::COLOR,
				'default'     => $cream_default,
				'selectors'   => [
					'{{WRAPPER}} .aew-hero' => '--aew-hero-bg: {{VALUE}};',
					'{{WRAPPER}} .aew-hero--compact .aew-hero__copy' => 'background-color: {{VALUE}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__copy' => 'background-color: {{VALUE}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__copy' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_accent_mobile',
			[
				'label'     => esc_html__( 'Eyebrow & headline color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#305B83',
				'selectors' => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__eyebrow--mobile' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero--compact .aew-hero__headline--mobile' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero--compact .aew-hero__headline--shared' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-hero--compact .aew-hero__social-text' => 'color: {{VALUE}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__eyebrow--mobile' => 'color: {{VALUE}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__eyebrow--mobile' => 'color: {{VALUE}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__headline--mobile' => 'color: {{VALUE}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__headline--mobile' => 'color: {{VALUE}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__headline--shared' => 'color: {{VALUE}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__headline--shared' => 'color: {{VALUE}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__social-text' => 'color: {{VALUE}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__social-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'copy_panel_margin_top',
			[
				'label'      => esc_html__( 'Overlap image (margin-top)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => -120,
						'max' => 0,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => -30,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__copy' => 'margin-top: {{SIZE}}{{UNIT}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__copy' => 'margin-top: {{SIZE}}{{UNIT}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__copy' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'copy_panel_radius',
			[
				'label'      => esc_html__( 'Panel top radius', 'agency-elementor-widgets' ),
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
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__copy' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__copy' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__copy' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
				],
			]
		);

		$this->add_responsive_control(
			'copy_panel_padding',
			[
				'label'      => esc_html__( 'Panel padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '24',
					'right'    => '24',
					'bottom'   => '80',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero--compact .aew-hero__copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'body.elementor-device-tablet {{WRAPPER}} .aew-hero__copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'body.elementor-device-mobile {{WRAPPER}} .aew-hero__copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_copy_desktop',
			[
				'label' => esc_html__( 'Copy overlay (desktop)', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'copy_overlay_padding',
			[
				'label'      => esc_html__( 'Overlay padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '48',
					'right'    => '40',
					'bottom'   => '48',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'copy_gap',
			[
				'label'      => esc_html__( 'Spacing between copy items', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
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
					'{{WRAPPER}} .aew-hero__copy' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_social',
			[
				'label' => esc_html__( 'Social proof', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'avatar_size',
			[
				'label'      => esc_html__( 'Avatar size', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 32,
						'max' => 96,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 64,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__avatar-frame' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_overlap',
			[
				'label'      => esc_html__( 'Avatar overlap', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 32,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-hero__avatar-frame + .aew-hero__avatar-frame' => 'margin-left: calc(-1 * {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'star_color',
			[
				'label'     => esc_html__( 'Star color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EBC543',
				'selectors' => [
					'{{WRAPPER}} .aew-hero__stars' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'social_text_typography',
				'label'    => esc_html__( 'Label typography', 'agency-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .aew-hero__social-text',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
				],
			]
		);

		$this->add_control(
			'social_text_color_desktop',
			[
				'label'     => esc_html__( 'Label color (on image)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-hero:not(.aew-hero--compact) .aew-hero__social-text' => 'color: {{VALUE}};',
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
	 * Single copy block — Elementor inline editing on p, h, and button text.
	 *
	 * @param array<string, mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_copy_block( array $settings ): void {
		$cta_link          = $this->parse_link( $settings['cta_link'] ?? [] );
		$show_social       = 'yes' === ( $settings['show_social_proof'] ?? '' );
		$headline          = $settings['headline'] ?? '';
		$headline_mobile   = trim( (string) ( $settings['headline_mobile_html'] ?? '' ) );
		$has_mobile_headline = '' !== $headline_mobile;

		?>
		<div class="aew-hero__copy">
			<?php if ( ! empty( $settings['eyebrow_desktop'] ) ) : ?>
				<?php
				$this->add_render_attribute( 'eyebrow_desktop', 'class', 'aew-hero__eyebrow aew-hero__eyebrow--desktop' );
				$this->add_inline_editing_attributes( 'eyebrow_desktop', 'none' );
				?>
				<p <?php $this->print_render_attribute_string( 'eyebrow_desktop' ); ?>>
					<?php echo esc_html( $settings['eyebrow_desktop'] ); ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $settings['eyebrow_mobile'] ) ) : ?>
				<?php
				$this->add_render_attribute( 'eyebrow_mobile', 'class', 'aew-hero__eyebrow aew-hero__eyebrow--mobile' );
				$this->add_inline_editing_attributes( 'eyebrow_mobile', 'none' );
				?>
				<p <?php $this->print_render_attribute_string( 'eyebrow_mobile' ); ?>>
					<?php echo esc_html( $settings['eyebrow_mobile'] ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $has_mobile_headline ) : ?>
				<?php
				$this->add_render_attribute( 'headline_mobile_html', 'class', 'aew-hero__headline aew-hero__headline--mobile' );
				$this->add_inline_editing_attributes( 'headline_mobile_html', 'advanced' );
				?>
				<h1 <?php $this->print_render_attribute_string( 'headline_mobile_html' ); ?>>
					<?php echo wp_kses_post( $headline_mobile ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $headline ) : ?>
				<?php
				$headline_class = 'aew-hero__headline aew-hero__headline--desktop';
				if ( ! $has_mobile_headline ) {
					$headline_class .= ' aew-hero__headline--shared';
				}
				$this->add_render_attribute( 'headline', 'class', $headline_class );
				$this->add_inline_editing_attributes( 'headline', 'none' );
				?>
				<h1 <?php $this->print_render_attribute_string( 'headline' ); ?>>
					<?php echo esc_html( $headline ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( ! Rich_Text::is_empty( (string) ( $settings['description'] ?? '' ) ) ) : ?>
				<?php
				$this->add_render_attribute( 'description', 'class', 'aew-hero__description aew-rich-text' );
				$this->add_inline_editing_attributes( 'description', 'advanced' );
				?>
				<div <?php $this->print_render_attribute_string( 'description' ); ?>>
					<?php Rich_Text::echo_html( (string) $settings['description'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $settings['cta_text'] ) && $cta_link['url'] ) : ?>
				<?php
				$this->add_render_attribute( 'cta_text', 'class', 'aew-hero__cta' );
				$this->add_render_attribute( 'cta_text', 'href', $cta_link['url'] );
				if ( $cta_link['target'] ) {
					$this->add_render_attribute( 'cta_text', 'target', $cta_link['target'] );
				}
				if ( $cta_link['rel'] ) {
					$this->add_render_attribute( 'cta_text', 'rel', $cta_link['rel'] );
				}
				$this->add_inline_editing_attributes( 'cta_text', 'none' );
				?>
				<a <?php $this->print_render_attribute_string( 'cta_text' ); ?>>
					<?php echo esc_html( $settings['cta_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $show_social ) : ?>
				<?php $this->render_social_proof( $settings ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_social_proof( array $settings ): void {
		$stars = min( 5, max( 1, (int) ( $settings['star_count'] ?? 5 ) ) );
		?>
		<div class="aew-hero__social">
			<div class="aew-hero__avatars" aria-hidden="true">
				<?php
				foreach ( [ 1, 2, 3 ] as $i ) {
					$avatar = $settings[ 'avatar_' . $i ] ?? [];
					$url    = is_array( $avatar ) ? ( $avatar['url'] ?? '' ) : '';
					if ( $url ) {
						printf(
							'<span class="aew-hero__avatar-frame"><img class="aew-hero__avatar" src="%s" alt="" decoding="async" loading="eager" width="48" height="48" /></span>',
							esc_url( $url )
						);
					} else {
						echo '<span class="aew-hero__avatar-frame aew-hero__avatar-frame--placeholder" aria-hidden="true"></span>';
					}
				}
				?>
			</div>
			<div class="aew-hero__rating">
				<div class="aew-hero__stars" aria-hidden="true">
					<?php for ( $s = 0; $s < $stars; $s++ ) : ?>
						<?php $this->render_star_icon(); ?>
					<?php endfor; ?>
				</div>
				<?php if ( ! empty( $settings['social_proof_text'] ) ) : ?>
					<?php
					$this->add_render_attribute( 'social_proof_text', 'class', 'aew-hero__social-text' );
					$this->add_inline_editing_attributes( 'social_proof_text', 'none' );
					?>
					<p <?php $this->print_render_attribute_string( 'social_proof_text' ); ?>>
						<?php echo esc_html( $settings['social_proof_text'] ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_star_icon(): void {
		?>
		<svg class="aew-hero__star" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M9 1.5L11.04 6.63L16.5 7.24L12.45 11.07L13.59 16.44L9 13.77L4.41 16.44L5.55 11.07L1.5 7.24L6.96 6.63L9 1.5Z"/>
		</svg>
		<?php
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_features( array $settings ): void {
		$features = $settings['features'] ?? [];
		if ( empty( $features ) || ! is_array( $features ) ) {
			return;
		}

		$default_icon = Widget_Assets::url( self::ASSET_SLUG, 'images/icon-check.svg' );

		?>
		<div class="aew-hero__features">
			<?php foreach ( $features as $index => $item ) : ?>
				<?php
				$icon = $item['feature_icon'] ?? [];
				$icon_url = is_array( $icon ) ? ( $icon['url'] ?? '' ) : '';
				if ( '' === $icon_url ) {
					$icon_url = $default_icon;
				}

				$title_key = $this->get_repeater_setting_key( 'feature_title', 'features', $index );
				$desc_key  = $this->get_repeater_setting_key( 'feature_description', 'features', $index );
				?>
				<article class="aew-hero__feature">
					<div class="aew-hero__feature-icon">
						<span class="aew-hero__feature-icon-badge">
							<img src="<?php echo esc_url( $icon_url ); ?>" alt="" decoding="async" loading="eager" />
						</span>
					</div>
					<?php if ( ! empty( $item['feature_title'] ) ) : ?>
						<?php
						$this->add_render_attribute( $title_key, 'class', 'aew-hero__feature-title' );
						$this->add_inline_editing_attributes( $title_key, 'none' );
						?>
						<h3 <?php $this->print_render_attribute_string( $title_key ); ?>>
							<?php echo esc_html( $item['feature_title'] ); ?>
						</h3>
					<?php endif; ?>
					<?php if ( ! Rich_Text::is_empty( (string) ( $item['feature_description'] ?? '' ) ) ) : ?>
						<?php
						$this->add_render_attribute( $desc_key, 'class', 'aew-hero__feature-text aew-rich-text' );
						$this->add_inline_editing_attributes( $desc_key, 'advanced' );
						?>
						<div <?php $this->print_render_attribute_string( $desc_key ); ?>>
							<?php Rich_Text::echo_html( (string) $item['feature_description'] ); ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$breakpoint = max( 768, (int) ( $settings['mobile_breakpoint'] ?? 768 ) );
		$bg         = $settings['background_image'] ?? [];
		$bg_url     = is_array( $bg ) ? ( $bg['url'] ?? '' ) : '';
		if ( '' === $bg_url ) {
			$bg_url = Widget_Assets::url( self::ASSET_SLUG, 'images/hero-default.webp' );
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-hero' );
		$this->add_render_attribute( 'wrapper', 'data-aew-hero', '' );
		$this->add_render_attribute( 'wrapper', 'style', '--aew-hero-bp:' . $breakpoint );
		$this->add_render_attribute( 'visual', 'class', 'aew-hero__visual' );
		$this->add_render_attribute( 'visual', 'style', 'background-image: url(' . esc_url( $bg_url ) . ');' );

		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-hero__shell">
				<div class="aew-hero__inner">
					<div class="aew-hero__stage">
						<div <?php $this->print_render_attribute_string( 'visual' ); ?> role="img" aria-label="<?php echo esc_attr__( 'Hero background', 'agency-elementor-widgets' ); ?>"></div>
						<?php $this->render_copy_block( $settings ); ?>
					</div>

					<?php $this->render_features( $settings ); ?>
				</div>
			</div>
		</section>
		<?php
	}
}
