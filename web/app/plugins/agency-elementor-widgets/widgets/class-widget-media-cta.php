<?php
/**
 * Media CTA Elementor widget (Frame 18).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * 50/50 image + blue CTA panel on a dark blue band.
 */
class Widget_Media_Cta extends Widget_Base {

	private const ASSET_SLUG = 'media-cta';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-media-cta';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Media CTA', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-rollover';
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
		return [ 'cta', 'split', 'image', 'banner', 'frame', 'media' ];
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
			'media_type',
			[
				'label'   => esc_html__( 'Media type', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => esc_html__( 'Image', 'agency-elementor-widgets' ),
					'video' => esc_html__( 'Video', 'agency-elementor-widgets' ),
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/media-cta-default.webp' ),
				],
				'condition' => [ 'media_type' => 'image' ],
			]
		);

		$this->add_control(
			'image_alt',
			[
				'label'       => esc_html__( 'Image alt text', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bright Homes renovation project', 'agency-elementor-widgets' ),
				'label_block' => true,
				'condition'   => [ 'media_type' => 'image' ],
			]
		);

		$this->add_control(
			'video',
			[
				'label'        => esc_html__( 'Video file', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::MEDIA,
				'media_types'  => [ 'video' ],
				'description'  => esc_html__( 'Upload or pick an MP4/WebM. Plays muted, looped, inline.', 'agency-elementor-widgets' ),
				'condition'    => [ 'media_type' => 'video' ],
			]
		);

		$this->add_control(
			'video_poster',
			[
				'label'       => esc_html__( 'Video poster (fallback image)', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'Shown while the video loads.', 'agency-elementor-widgets' ),
				'condition'   => [ 'media_type' => 'video' ],
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label'        => esc_html__( 'Autoplay (muted loop)', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'media_type' => 'video' ],
			]
		);

		$this->add_control(
			'video_controls',
			[
				'label'        => esc_html__( 'Show player controls', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [ 'media_type' => 'video' ],
			]
		);

		$this->add_control(
			'image_position',
			[
				'label'   => esc_html__( 'Image position', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'agency-elementor-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					'Ready to See What Your Home Could Become?',
					'agency-elementor-widgets'
				),
				'rows'    => 2,
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'Book a free consultation with Jon. No pressure, no obligation — just a clear conversation about your home, your goals, and what a realistic path forward looks like.',
					'agency-elementor-widgets'
				),
				'rows'    => 4,
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
				'default'      => 'yes',
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

		$this->add_control(
			'button_arrow',
			[
				'label'        => esc_html__( 'Show arrow icon', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [ 'show_button' => 'yes' ],
			]
		);

		$this->add_control(
			'button_arrow_pos',
			[
				'label'     => esc_html__( 'Arrow position', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'before' => esc_html__( 'Before label', 'agency-elementor-widgets' ),
					'after'  => esc_html__( 'After label', 'agency-elementor-widgets' ),
				],
				'condition' => [
					'show_button'  => 'yes',
					'button_arrow' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens         = Design_Tokens::get();
		$blue_default   = $tokens['color_blue'] ?? '#305B83';
		$light_default  = $tokens['color_blue_light'] ?? '#5796BB';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';
		$dark_default   = $tokens['color_blue_dark'] ?? '#252F37';
		$white_default  = $tokens['color_white'] ?? '#FFFFFF';
		$cta_default    = '#AA7D44';   /* brand CTA — filled button background */

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
				'label'     => esc_html__( 'Wrapper background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $blue_default,
				'selectors' => [
					'{{WRAPPER}} .aew-media-cta' => '--aew-media-cta-bg: {{VALUE}};',
				],
			]
		);

		/* Defaults left EMPTY so the stylesheet owns the design-system padding
		   (64px/40px desktop, 32px/16px mobile on .aew-media-cta__inner). A
		   non-empty default here emits one high-specificity rule that clobbers
		   the stylesheet and doubles up the gutter (build guide §5 / gotcha #16). */
		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'        => [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ],
				'mobile_default' => [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => esc_html__( 'Gap between image and panel', 'agency-elementor-widgets' ),
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
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__row' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Image height (stacked layouts)', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'On desktop, image height matches the text panel automatically.',
					'agency-elementor-widgets'
				),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 120,
						'max' => 800,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 500,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 240,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta' => '--aew-media-cta-image-height: {{SIZE}}{{UNIT}};',
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
					'size' => 40,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_panel',
			[
				'label' => esc_html__( 'Text panel', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'panel_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $light_default,
				'selectors' => [
					'{{WRAPPER}} .aew-media-cta' => '--aew-media-cta-panel-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
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
					'top'      => '24',
					'right'    => '24',
					'bottom'   => '24',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_radius',
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
				'tablet_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__panel' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'panel_gap',
			[
				'label'      => esc_html__( 'Gap between title, text, and button', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 64,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__panel' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-media-cta-title: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-media-cta__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'DM Serif Display' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 48,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-media-cta-text: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'description_typography',
				'selector'       => '{{WRAPPER}} .aew-media-cta__text',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
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

		$this->end_controls_section();

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
			'button_width_mode',
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
					'custom' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .aew-media-cta__button' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'      => esc_html__( 'Custom width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 80, 'max' => 600 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 280 ],
				'condition'  => [ 'button_width_mode' => 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .aew-media-cta__button' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $cta_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-media-cta-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}}' => '--aew-media-cta-btn-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-media-cta-btn-bg-hover: {{VALUE}};',
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
					'{{WRAPPER}}' => '--aew-media-cta-btn-text-hover: {{VALUE}};',
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
					'{{WRAPPER}} .aew-media-cta__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-media-cta__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .aew-media-cta__button',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
					'font_weight' => [ 'default' => '700' ],
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
	 * @param array<string, mixed> $image Image control value.
	 * @return string
	 */
	private function resolve_image_url( array $image ): string {
		$url = trim( (string) ( $image['url'] ?? '' ) );
		if ( '' === $url ) {
			return '';
		}

		$plugin_path = '/agency-elementor-widgets/agency-elementor-widgets/';
		if ( str_contains( $url, $plugin_path ) ) {
			$url = str_replace( $plugin_path, '/agency-elementor-widgets/', $url );
		}

		return $url;
	}

	/**
	 * Decorative arrow icon used inside the button.
	 *
	 * @param string $position before|after.
	 * @return string
	 */
	private function arrow_svg( string $position = 'after' ): string {
		$mod = 'before' === $position ? ' aew-media-cta__btn-arrow--before' : '';
		return '<svg class="aew-media-cta__btn-arrow' . esc_attr( $mod ) . '" viewBox="0 0 200 200" width="24" height="24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
			. '<path d="M179.7 96.3H55.9c10.4-6 15.6-15.8 15.6-29.1h-7.3c0 7.2 0 28.9-43 29.1h-.9v7.3c43.2.2 43.2 22 43.2 29.1h7.3c0-13.4-5.3-23.1-15.8-29.1h124.6v-7.3z"/>'
			. '</svg>';
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$title       = trim( (string) ( $settings['title'] ?? '' ) );
		$description = (string) ( $settings['description'] ?? '' );
		$media_type  = ( 'video' === ( $settings['media_type'] ?? 'image' ) ) ? 'video' : 'image';
		$image_url   = $this->resolve_image_url( (array) ( $settings['image'] ?? [] ) );
		$image_alt   = trim( (string) ( $settings['image_alt'] ?? '' ) );
		$video_url   = $this->resolve_image_url( (array) ( $settings['video'] ?? [] ) );
		$poster_url  = $this->resolve_image_url( (array) ( $settings['video_poster'] ?? [] ) );
		$v_autoplay  = 'yes' === ( $settings['video_autoplay'] ?? '' );
		$v_controls  = 'yes' === ( $settings['video_controls'] ?? '' );
		$show_button = 'yes' === ( $settings['show_button'] ?? '' );
		$button_text = trim( (string) ( $settings['button_text'] ?? '' ) );
		$button_link = $this->parse_link( $settings['button_link'] ?? [] );
		$btn_arrow   = 'yes' === ( $settings['button_arrow'] ?? '' );
		$btn_pos     = 'before' === ( $settings['button_arrow_pos'] ?? 'after' ) ? 'before' : 'after';
		$position    = ( 'right' === ( $settings['image_position'] ?? 'left' ) ) ? 'right' : 'left';

		$has_media = ( 'video' === $media_type ) ? ( '' !== $video_url ) : ( '' !== $image_url );

		if (
			'' === $title
			&& Rich_Text::is_empty( $description )
			&& ! $has_media
			&& ( ! $show_button || '' === $button_text || '' === $button_link['url'] )
		) {
			return;
		}

		$row_class = 'aew-media-cta__row';
		if ( 'right' === $position ) {
			$row_class .= ' aew-media-cta__row--image-right';
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-media-cta__inner' );
		$this->add_render_attribute( 'row', 'class', $row_class );

		$color_vars = Color_Vars::build(
			$this,
			$settings,
			[
				'section_background' => '--aew-media-cta-bg',
				'panel_background'   => '--aew-media-cta-panel-bg',
				'title_color'        => '--aew-media-cta-title',
				'description_color'  => '--aew-media-cta-text',
				'button_background'  => '--aew-media-cta-btn-bg',
				'button_color'       => '--aew-media-cta-btn-text',
				'button_background_hover' => '--aew-media-cta-btn-bg-hover',
				'button_color_hover'      => '--aew-media-cta-btn-text-hover',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		?>
		<section class="aew-media-cta"<?php echo $style_attr; ?>>
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'row' ); ?>>
					<?php if ( $has_media ) : ?>
						<div class="aew-media-cta__media">
							<?php if ( 'video' === $media_type ) : ?>
								<video class="aew-media-cta__video"
									<?php echo $poster_url ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>
									<?php echo $v_autoplay ? 'autoplay muted loop' : ''; ?>
									<?php echo $v_controls ? 'controls' : ''; ?>
									playsinline
									preload="metadata">
									<source src="<?php echo esc_url( $video_url ); ?>" />
								</video>
							<?php else : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ?: $title ); ?>" loading="lazy" decoding="async" />
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="aew-media-cta__panel">
						<?php if ( '' !== $title ) : ?>
							<?php
							$this->add_render_attribute( 'title', 'class', 'aew-media-cta__title' );
							$this->add_inline_editing_attributes( 'title', 'none' );
							?>
							<h2 <?php $this->print_render_attribute_string( 'title' ); ?>>
								<?php echo esc_html( $title ); ?>
							</h2>
						<?php endif; ?>
						<?php if ( ! Rich_Text::is_empty( $description ) ) : ?>
							<?php
							$this->add_render_attribute( 'description', 'class', 'aew-media-cta__body aew-media-cta__text aew-rich-text' );
							$this->add_inline_editing_attributes( 'description', 'advanced' );
							?>
							<div <?php $this->print_render_attribute_string( 'description' ); ?>>
								<?php Rich_Text::echo_html( $description ); ?>
							</div>
						<?php endif; ?>
						<?php if ( $show_button && '' !== $button_text && '' !== $button_link['url'] ) : ?>
							<?php
							$this->add_render_attribute( 'button', 'class', 'aew-media-cta__button' );
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
								<?php
								if ( $btn_arrow && 'before' === $btn_pos ) {
									echo $this->arrow_svg( 'before' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — fixed safe SVG constant
								}
								?>
								<span class="aew-media-cta__btn-label" <?php $this->print_render_attribute_string( 'button_text' ); ?>>
									<?php echo esc_html( $button_text ); ?>
								</span>
								<?php
								if ( $btn_arrow && 'after' === $btn_pos ) {
									echo $this->arrow_svg( 'after' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — fixed safe SVG constant
								}
								?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
