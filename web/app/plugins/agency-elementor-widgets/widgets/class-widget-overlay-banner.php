<?php
/**
 * Overlay Banner Elementor widget (Frame 51).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Centered text panel overlaid on a full-width house image.
 */
class Widget_Overlay_Banner extends Widget_Base {

	private const ASSET_SLUG = 'overlay-banner';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-overlay-banner';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Overlay Banner', 'agency-elementor-widgets' );
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
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'banner', 'overlay', 'image', 'cta', 'frame' ];
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
			'image',
			[
				'label'   => esc_html__( 'Background image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/overlay-banner-default.webp' ),
				],
			]
		);

		$this->add_control(
			'image_alt',
			[
				'label'       => esc_html__( 'Image alt text', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bright Homes project', 'agency-elementor-widgets' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					'Not Quite Ready to Talk? This Is Worth Reading First.',
					'agency-elementor-widgets'
				),
				'rows'    => 2,
			]
		);

		$this->add_control(
			'lead',
			[
				'label'   => esc_html__( 'Lead paragraph', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'The Utah Homeowner\'s Guide to a Renovation That Goes the Way It\'s Supposed To',
					'agency-elementor-widgets'
				),
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'<p>Download our free guide and learn what to expect before you hire anyone—budget, timeline, communication, and how to spot red flags early.</p><p>We\'ve spent 25+ years helping Utah homeowners renovate with confidence. This is the same practical advice we give in every first conversation, in one straightforward read.</p>',
					'agency-elementor-widgets'
				),
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

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens         = Design_Tokens::get();
		$cream_default  = $tokens['color_cream'] ?? '#F8F5F1';
		$dark_default   = $tokens['color_blue_dark'] ?? '#252F37';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
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
				'tablet_default' => [
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
					'{{WRAPPER}} .aew-overlay-banner__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Background image height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 870,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 860,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-image-height: {{SIZE}}{{UNIT}};',
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
					'size' => 0,
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
					'{{WRAPPER}} .aew-overlay-banner__media' => 'border-radius: {{SIZE}}{{UNIT}};',
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
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-panel-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_width',
			[
				'label'      => esc_html__( 'Panel width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 280,
						'max' => 1200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 900,
				],
				'mobile_default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-panel-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_min_height',
			[
				'label'      => esc_html__( 'Panel min height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 900,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 630,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-panel-min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_padding',
			[
				'label'      => esc_html__( 'Panel padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '64',
					'right'    => '80',
					'bottom'   => '64',
					'left'     => '80',
					'unit'     => 'px',
					'isLinked' => false,
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
					'{{WRAPPER}} .aew-overlay-banner__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-overlay-banner__panel' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'panel_gap',
			[
				'label'      => esc_html__( 'Gap between elements', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-overlay-banner__panel' => 'gap: {{SIZE}}{{UNIT}};',
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
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-overlay-banner__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
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
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_lead',
			[
				'label' => esc_html__( 'Lead paragraph', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'lead_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-lead-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'lead_typography',
				'selector'       => '{{WRAPPER}} .aew-overlay-banner__lead',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 32,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 24,
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
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-overlay-banner' => '--aew-overlay-banner-description-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'description_typography',
				'selector'       => '{{WRAPPER}} .aew-overlay-banner__description',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
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

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .aew-overlay-banner__button' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-overlay-banner__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-overlay-banner__button:hover'         => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .aew-overlay-banner__button:focus-visible' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-overlay-banner__button:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-overlay-banner__button:focus-visible' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-overlay-banner__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .aew-overlay-banner__button',
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
							'size' => 14,
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
	 * @return void
	 */
	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$title       = trim( (string) ( $settings['title'] ?? '' ) );
		$lead        = (string) ( $settings['lead'] ?? '' );
		$description = (string) ( $settings['description'] ?? '' );
		$image_url   = $this->resolve_image_url( (array) ( $settings['image'] ?? [] ) );
		$image_alt   = trim( (string) ( $settings['image_alt'] ?? '' ) );
		$show_button = 'yes' === ( $settings['show_button'] ?? '' );
		$button_text = trim( (string) ( $settings['button_text'] ?? '' ) );
		$button_link = $this->parse_link( $settings['button_link'] ?? [] );

		if (
			'' === $title
			&& Rich_Text::is_empty( $lead )
			&& Rich_Text::is_empty( $description )
			&& '' === $image_url
			&& ( ! $show_button || '' === $button_text || '' === $button_link['url'] )
		) {
			return;
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-overlay-banner__inner' );
		$this->add_render_attribute( 'stack', 'class', 'aew-overlay-banner__stack aew-overlay-banner__stack--center' );

		?>
		<section class="aew-overlay-banner">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'stack' ); ?>>
					<div class="aew-overlay-banner__panel-wrap">
						<div class="aew-overlay-banner__panel">
							<?php if ( '' !== $title ) : ?>
								<?php
								$this->add_render_attribute( 'title', 'class', 'aew-overlay-banner__title' );
								$this->add_inline_editing_attributes( 'title', 'none' );
								?>
								<h2 <?php $this->print_render_attribute_string( 'title' ); ?>>
									<?php echo esc_html( $title ); ?>
								</h2>
							<?php endif; ?>
							<?php if ( ! Rich_Text::is_empty( $lead ) ) : ?>
								<?php
								$this->add_render_attribute( 'lead', 'class', 'aew-overlay-banner__lead aew-rich-text' );
								$this->add_inline_editing_attributes( 'lead', 'advanced' );
								?>
								<div <?php $this->print_render_attribute_string( 'lead' ); ?>>
									<?php Rich_Text::echo_html( $lead ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ! Rich_Text::is_empty( $description ) ) : ?>
								<?php
								$this->add_render_attribute( 'description', 'class', 'aew-overlay-banner__description aew-rich-text' );
								$this->add_inline_editing_attributes( 'description', 'advanced' );
								?>
								<div <?php $this->print_render_attribute_string( 'description' ); ?>>
									<?php Rich_Text::echo_html( $description ); ?>
								</div>
							<?php endif; ?>
							<?php if ( $show_button && '' !== $button_text && '' !== $button_link['url'] ) : ?>
								<?php
								$this->add_render_attribute( 'button', 'class', 'aew-overlay-banner__button' );
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
					<?php if ( '' !== $image_url ) : ?>
						<div class="aew-overlay-banner__media">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ?: $title ); ?>" loading="lazy" decoding="async" />
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
