<?php
/**
 * CTA Band Elementor widget (Frame 36).
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
 * Dark CTA band — stars, heading, copy, consultation button.
 */
class Widget_Cta_Band extends Widget_Base {

	private const ASSET_SLUG = 'cta-band';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-cta-band';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'CTA Band', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-call-to-action';
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
		return [ 'cta', 'consultation', 'stars', 'button', 'frame' ];
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
			'show_stars',
			[
				'label'        => esc_html__( 'Show stars', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
					'show_stars' => 'yes',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					"Too Many Renovations End in Disappointment. See Why Our Clients' Stories Are Different.",
					'agency-elementor-widgets'
				),
				'rows'    => 3,
			]
		);

		$this->add_control(
			'lead_text',
			[
				'label'   => esc_html__( 'Lead text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__(
					'What a builder says is one thing. What their clients say after a real project is what counts.',
					'agency-elementor-widgets'
				),
				'rows'    => 3,
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
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $paragraph_repeater->get_controls(),
				'default'     => [
					[
						'text' => esc_html__(
							'After 25 years of listening to homeowners, the same themes come up again and again.',
							'agency-elementor-widgets'
						),
					],
					[
						'text' => esc_html__(
							'Clear process. Pricing that holds. Results that match the plan.',
							'agency-elementor-widgets'
						),
					],
				],
				'title_field' => '{{{ text }}}',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Book Your FREE Consultation', 'agency-elementor-widgets' ),
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
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens         = Design_Tokens::get();
		$dark_default   = $tokens['color_blue_dark'] ?? '#252F37';
		$cream_default  = $tokens['color_cream'] ?? '#F8F5F1';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';

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
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band' => '--aew-cta-band-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'overlap_above',
			[
				'label'      => esc_html__( 'Overlap above', 'agency-elementor-widgets' ),
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
				'tablet_default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-cta-band' => '--aew-cta-band-overlap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_top_radius',
			[
				'label'      => esc_html__( 'Top corner radius', 'agency-elementor-widgets' ),
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
					'size' => 0,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-cta-band' => '--aew-cta-band-top-radius: {{SIZE}}{{UNIT}};',
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
					'%' => [
						'min' => 50,
						'max' => 100,
					],
					'px' => [
						'min' => 320,
						'max' => 1200,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 80,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-cta-band__content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Inner padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '120',
					'right'    => '0',
					'bottom'   => '80',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '56',
					'right'    => '0',
					'bottom'   => '56',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '56',
					'right'    => '0',
					'bottom'   => '56',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-cta-band__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label'      => esc_html__( 'Gap between blocks', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-cta-band__content' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_stars',
			[
				'label' => esc_html__( 'Stars', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'star_size',
			[
				'label'      => esc_html__( 'Size', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 12,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 28,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-cta-band__star' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'star_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $yellow_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band__stars' => 'color: {{VALUE}};',
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
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-cta-band__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'DM Serif Display' ],
					'font_weight' => [ 'default' => '400' ],
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_lead',
			[
				'label' => esc_html__( 'Lead & paragraphs', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'body_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band__lead' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-cta-band__paragraph' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'lead_typography',
				'label'          => esc_html__( 'Lead typography', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-cta-band__lead, {{WRAPPER}} .aew-cta-band__paragraph',
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
					'{{WRAPPER}} .aew-cta-band__paragraphs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $yellow_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band__button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-cta-band__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-cta-band__button:hover'         => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .aew-cta-band__button:focus-visible' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-cta-band__button:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-cta-band__button:focus-visible' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-cta-band__button' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-cta-band__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .aew-cta-band__button',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Poppins' ],
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
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<int, array{text: string}>
	 */
	private function get_paragraphs( array $settings ): array {
		$items = $settings['paragraphs'] ?? [];

		if ( ! is_array( $items ) || empty( $items ) ) {
			return [];
		}

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
	 * @return void
	 */
	private function render_star_icon(): void {
		?>
		<svg class="aew-cta-band__star" width="28" height="28" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M9 1.5L11.04 6.63L16.5 7.24L12.45 11.07L13.59 16.44L9 13.77L4.41 16.44L5.55 11.07L1.5 7.24L6.96 6.63L9 1.5Z"/>
		</svg>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$title      = trim( (string) ( $settings['title'] ?? '' ) );
		$lead       = (string) ( $settings['lead_text'] ?? '' );
		$paragraphs = $this->get_paragraphs( $settings );
		$button     = trim( (string) ( $settings['button_text'] ?? '' ) );
		$link       = $this->parse_link( $settings['button_link'] ?? [] );
		$stars      = min( 5, max( 1, (int) ( $settings['star_count'] ?? 5 ) ) );

		if (
			'' === $title
			&& Rich_Text::is_empty( $lead )
			&& empty( $paragraphs )
			&& '' === $button
			&& empty( $settings['show_stars'] )
		) {
			return;
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-cta-band__inner' );
		$this->add_render_attribute( 'content', 'class', 'aew-cta-band__content' );

		?>
		<section class="aew-cta-band">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'content' ); ?>>
					<?php if ( ! empty( $settings['show_stars'] ) ) : ?>
						<div class="aew-cta-band__stars" aria-hidden="true">
							<?php for ( $s = 0; $s < $stars; $s++ ) : ?>
								<?php $this->render_star_icon(); ?>
							<?php endfor; ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $title ) : ?>
						<?php
						$this->add_render_attribute( 'title', 'class', 'aew-cta-band__title' );
						$this->add_inline_editing_attributes( 'title', 'none' );
						?>
						<h2 <?php $this->print_render_attribute_string( 'title' ); ?>>
							<?php echo esc_html( $title ); ?>
						</h2>
					<?php endif; ?>

					<?php if ( ! Rich_Text::is_empty( $lead ) ) : ?>
						<?php
						$this->add_render_attribute( 'lead_text', 'class', 'aew-cta-band__lead aew-rich-text' );
						$this->add_inline_editing_attributes( 'lead_text', 'advanced' );
						?>
						<div <?php $this->print_render_attribute_string( 'lead_text' ); ?>>
							<?php Rich_Text::echo_html( $lead ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $paragraphs ) ) : ?>
						<div class="aew-cta-band__paragraphs">
							<?php foreach ( $paragraphs as $index => $item ) : ?>
								<?php
								$paragraph_key = 'paragraph_' . $index;
								$text_key      = $this->get_repeater_setting_key( 'text', 'paragraphs', $index );

								$this->add_render_attribute( $paragraph_key, 'class', 'aew-cta-band__paragraph aew-rich-text' );
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

					<?php if ( '' !== $button ) : ?>
						<?php
						$this->add_render_attribute( 'button', 'class', 'aew-cta-band__button' );
						$this->add_inline_editing_attributes( 'button_text', 'none' );

						if ( ! empty( $link['url'] ) ) {
							$this->add_render_attribute( 'button', 'href', esc_url( $link['url'] ) );
							if ( ! empty( $link['target'] ) ) {
								$this->add_render_attribute( 'button', 'target', $link['target'] );
							}
							if ( ! empty( $link['rel'] ) ) {
								$this->add_render_attribute( 'button', 'rel', $link['rel'] );
							}
						}
						?>
						<a <?php $this->print_render_attribute_string( 'button' ); ?>>
							<span <?php $this->print_render_attribute_string( 'button_text' ); ?>>
								<?php echo esc_html( $button ); ?>
							</span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
