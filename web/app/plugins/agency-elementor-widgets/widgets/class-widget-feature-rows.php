<?php
/**
 * Feature Rows Elementor widget (Frame 25).
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
 * Service rows — desktop zigzag, mobile stacked cards.
 */
class Widget_Feature_Rows extends Widget_Base {

	private const ASSET_SLUG = 'feature-rows';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-feature-rows';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Feature Rows', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-posts-grid';
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
		return [ 'services', 'showcase', 'remodeling', 'kitchen', 'bathroom' ];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function default_services(): array {
		$items = [
			[
				'title'       => esc_html__( 'Whole Home Remodeling', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Your home once worked perfectly for the way you lived. Life changed. The house didn\'t. We redesign it from the inside out around how you live today, with a finished result you\'ll see in 3D before construction begins.',
					'agency-elementor-widgets'
				),
				'cta'         => esc_html__( 'Explore Whole Home Remodeling', 'agency-elementor-widgets' ),
				'position'    => 'right',
			],
			[
				'title'       => esc_html__( 'Home Additions', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'You love this neighborhood. You just need your home to do more. We design and build additions that connect so naturally, with the same roofline, materials, and character, that no one can tell what was always there and what\'s new.',
					'agency-elementor-widgets'
				),
				'cta'         => esc_html__( 'Explore Home Additions', 'agency-elementor-widgets' ),
				'position'    => 'left',
			],
			[
				'title'       => esc_html__( 'Kitchen Renovations', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'The heart of your home, where everyone gathers and meals come to life, designed the way you\'ve always imagined it. Every finish is chosen before construction begins, and every cost is agreed to before a wall is touched.',
					'agency-elementor-widgets'
				),
				'cta'         => esc_html__( 'Explore Kitchen Renovations', 'agency-elementor-widgets' ),
				'position'    => 'right',
			],
			[
				'title'       => esc_html__( 'Bathroom Renovations', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'The room you use every morning, finally designed around how you actually live. Warm, thoughtful, and built to feel like it was always meant to be there. You\'ll see it in 3D before we start, and know the full cost before you commit.',
					'agency-elementor-widgets'
				),
				'cta'         => esc_html__( 'Explore Bathroom Renovations', 'agency-elementor-widgets' ),
				'position'    => 'left',
			],
			[
				'title'       => esc_html__( 'Basement Renovations', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Most Utah homes are sitting on 1,600 square feet of untapped potential. Done right, that space becomes a family room, extra bedrooms and bathroom, or even a self-contained apartment generating $1,600 to $1,800 per month in rental income. We\'ll show you exactly what yours could look like before we build it.',
					'agency-elementor-widgets'
				),
				'cta'         => esc_html__( 'Explore Basement Renovations', 'agency-elementor-widgets' ),
				'position'    => 'right',
			],
		];

		$defaults = [];
		foreach ( $items as $index => $item ) {
			$n = $index + 1;
			$defaults[] = [
				'service_title'       => $item['title'],
				'service_description' => $item['description'],
				'service_image'       => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/service-' . $n . '.webp' ),
				],
				'cta_text'            => $item['cta'],
				'cta_link'            => [
					'url' => '#',
				],
				'image_position'      => $item['position'],
			];
		}

		return $defaults;
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
				'label' => esc_html__( 'Services', 'agency-elementor-widgets' ),
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

		$repeater = new Repeater();

		$repeater->add_control(
			'service_title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Service title', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'service_description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Service description.', 'agency-elementor-widgets' ),
				'rows'    => 5,
			]
		);

		$repeater->add_control(
			'service_image',
			[
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/service-1.webp' ),
				],
			]
		);

		$repeater->add_control(
			'cta_text',
			[
				'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Explore', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'cta_link',
			[
				'label'       => esc_html__( 'Button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#',
				'default'     => [
					'url' => '#',
				],
			]
		);

		$repeater->add_control(
			'image_position',
			[
				'label'   => esc_html__( 'Image side (desktop)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Left', 'agency-elementor-widgets' ),
					'right' => esc_html__( 'Right', 'agency-elementor-widgets' ),
				],
			]
		);

		$this->add_control(
			'services',
			[
				'label'       => esc_html__( 'Service items', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_services(),
				'title_field' => '{{{ service_title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens        = Design_Tokens::get();
		$cream_default  = $tokens['color_cream'] ?? '#F8F5F1';
		$white_default  = $tokens['color_white'] ?? '#FFFFFF';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';
		$dark_default   = $tokens['color_blue_dark'] ?? '#252F37';

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
					'{{WRAPPER}} .aew-feature-rows' => '--aew-feature-rows-bg: {{VALUE}};',
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
					'{{WRAPPER}} .aew-feature-rows__inner' => 'max-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-feature-rows__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => esc_html__( 'Gap between services', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 160,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-feature-rows__list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_column_gap',
			[
				'label'      => esc_html__( 'Gap between image & copy (desktop)', 'agency-elementor-widgets' ),
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
				'mobile_default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-feature-rows__item' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_body_background',
			[
				'label'     => esc_html__( 'Card background (copy block)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}} .aew-feature-rows__body' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-feature-rows__body' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
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
					'top'      => '24',
					'right'    => '24',
					'bottom'   => '24',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-feature-rows__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__( 'Images', 'agency-elementor-widgets' ),
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
					'size' => 20,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-feature-rows__media img' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-feature-rows__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .aew-feature-rows__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 48,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 24,
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
			'section_style_text',
			[
				'label' => esc_html__( 'Description', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-feature-rows__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .aew-feature-rows__text',
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
			'text_cta_spacing',
			[
				'label'      => esc_html__( 'Space before button', 'agency-elementor-widgets' ),
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
					'size' => 32,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 32,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-feature-rows__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cta',
			[
				'label' => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cta_full_width_mobile',
			[
				'label'        => esc_html__( 'Full width on mobile', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'cta_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $yellow_default,
				'selectors' => [
					'{{WRAPPER}} .aew-feature-rows__cta' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-feature-rows__cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_background_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6E4F2D',
				'selectors' => [
					'{{WRAPPER}} .aew-feature-rows__cta:hover'         => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .aew-feature-rows__cta:focus-visible' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-feature-rows__cta:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-feature-rows__cta:focus-visible' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .aew-feature-rows__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-feature-rows__cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .aew-feature-rows__cta',
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
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$services = $settings['services'] ?? [];

		if ( empty( $services ) || ! is_array( $services ) ) {
			return;
		}

		$breakpoint = max( 768, (int) ( $settings['mobile_breakpoint'] ?? 768 ) );
		$cta_full   = 'yes' === ( $settings['cta_full_width_mobile'] ?? '' );
		$root_class = 'aew-feature-rows';
		if ( $cta_full ) {
			$root_class .= ' aew-feature-rows--cta-full-width';
		}

		$this->add_render_attribute( 'wrapper', 'class', $root_class );
		$this->add_render_attribute( 'wrapper', 'data-aew-feature-rows', '' );
		$this->add_render_attribute( 'wrapper', 'style', '--aew-feature-rows-bp:' . $breakpoint );
		$this->add_render_attribute( 'inner', 'class', 'aew-feature-rows__inner' );
		$this->add_render_attribute( 'list', 'class', 'aew-feature-rows__list' );

		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'list' ); ?>>
					<?php foreach ( $services as $index => $item ) : ?>
						<?php $this->render_service_item( $item, $index ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * @param array<string, mixed> $item  Repeater row.
	 * @param int                  $index Row index.
	 * @return void
	 */
	private function render_service_item( array $item, int $index ): void {
		$position = $item['image_position'] ?? 'right';
		if ( ! in_array( $position, [ 'left', 'right' ], true ) ) {
			$position = 'right';
		}

		$image = $item['service_image'] ?? [];
		$image_url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
		if ( '' === $image_url ) {
			$fallback_n = ( $index % 5 ) + 1;
			$image_url  = Widget_Assets::url( self::ASSET_SLUG, 'images/service-' . $fallback_n . '.webp' );
		} elseif ( str_contains( $image_url, '/services-showcase/' ) ) {
			$image_url = str_replace( '/services-showcase/', '/feature-rows/', $image_url );
		}

		$cta_link = $this->parse_link( $item['cta_link'] ?? [] );

		$item_key = 'service_item_' . $index;
		$this->add_render_attribute( $item_key, 'class', 'aew-feature-rows__item aew-feature-rows__item--image-' . $position );

		$title_key = $this->get_repeater_setting_key( 'service_title', 'services', $index );
		$desc_key  = $this->get_repeater_setting_key( 'service_description', 'services', $index );
		$cta_key   = $this->get_repeater_setting_key( 'cta_text', 'services', $index );

		?>
		<article <?php $this->print_render_attribute_string( $item_key ); ?>>
			<div class="aew-feature-rows__media">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async" />
			</div>
			<div class="aew-feature-rows__body">
				<?php if ( ! empty( $item['service_title'] ) ) : ?>
					<?php
					$this->add_render_attribute( $title_key, 'class', 'aew-feature-rows__title' );
					$this->add_inline_editing_attributes( $title_key, 'none' );
					?>
					<h3 <?php $this->print_render_attribute_string( $title_key ); ?>>
						<?php echo esc_html( $item['service_title'] ); ?>
					</h3>
				<?php endif; ?>
				<?php if ( ! Rich_Text::is_empty( (string) ( $item['service_description'] ?? '' ) ) ) : ?>
					<?php
					$this->add_render_attribute( $desc_key, 'class', 'aew-feature-rows__text aew-rich-text' );
					$this->add_inline_editing_attributes( $desc_key, 'advanced' );
					?>
					<div <?php $this->print_render_attribute_string( $desc_key ); ?>>
						<?php Rich_Text::echo_html( (string) $item['service_description'] ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $item['cta_text'] ) && $cta_link['url'] ) : ?>
					<?php
					$this->add_render_attribute( $cta_key, 'class', 'aew-feature-rows__cta' );
					$this->add_render_attribute( $cta_key, 'href', $cta_link['url'] );
					if ( $cta_link['target'] ) {
						$this->add_render_attribute( $cta_key, 'target', $cta_link['target'] );
					}
					if ( $cta_link['rel'] ) {
						$this->add_render_attribute( $cta_key, 'rel', $cta_link['rel'] );
					}
					$this->add_inline_editing_attributes( $cta_key, 'none' );
					?>
					<a <?php $this->print_render_attribute_string( $cta_key ); ?>>
						<?php echo esc_html( $item['cta_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}
}
