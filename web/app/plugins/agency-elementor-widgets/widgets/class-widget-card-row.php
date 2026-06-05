<?php
/**
 * Card Row Elementor widget (Frame 35).
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
 * Cream band with repeater white cards — mobile stack, desktop row.
 */
class Widget_Card_Row extends Widget_Base {

	private const ASSET_SLUG = 'card-row';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-card-row';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Card Row', 'agency-elementor-widgets' );
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
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'card', 'row', 'grid', 'image', 'frame', 'cream' ];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function default_cards(): array {
		$items = [
			[
				'title'       => esc_html__( 'See It Before You Build It', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Walk through a photo realistic 3D model of your finished home before construction begins.',
					'agency-elementor-widgets'
				),
			],
			[
				'title'       => esc_html__( 'Know the Real Cost Upfront', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'We investigate your home thoroughly and fully plan the project before pricing it, so the number holds.',
					'agency-elementor-widgets'
				),
			],
			[
				'title'       => esc_html__( 'Enjoy a Calm, Guided Process', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Every selection is made with your designer before construction starts, so there are no rushed decisions mid build.',
					'agency-elementor-widgets'
				),
			],
		];

		$defaults = [];
		foreach ( $items as $index => $item ) {
			$n = $index + 1;
			$defaults[] = [
				'card_title'       => $item['title'],
				'card_description' => $item['description'],
				'card_image'       => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/card-' . $n . '.webp' ),
				],
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
				'label' => esc_html__( 'Cards', 'agency-elementor-widgets' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'card_image',
			[
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/card-1.webp' ),
				],
			]
		);

		$repeater->add_control(
			'card_title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Card title', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'card_description',
			[
				'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Card description.', 'agency-elementor-widgets' ),
				'rows'    => 4,
			]
		);

		$this->add_control(
			'cards',
			[
				'label'       => esc_html__( 'Cards', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_cards(),
				'title_field' => '{{{ card_title }}}',
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
		$white_default = $tokens['color_white'] ?? '#FFFFFF';
		$blue_default  = $tokens['color_blue'] ?? '#305B83';
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
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}} .aew-card-row' => '--aew-card-row-bg: {{VALUE}};',
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
					'{{WRAPPER}} .aew-card-row__inner' => 'max-width: {{SIZE}}{{UNIT}};',
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
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '120',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '56',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '56',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-card-row__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => esc_html__( 'Card grid', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
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
				'tablet_default' => [
					'unit' => 'px',
					'size' => 32,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 32,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-card-row__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}} .aew-card-row__card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-card-row__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
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
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-card-row__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_inner_gap',
			[
				'label'      => esc_html__( 'Gap inside card', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-card-row__card' => 'gap: {{SIZE}}{{UNIT}};',
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
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-card-row__media img' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-card-row__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-card-row__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 48,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 24,
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
					'{{WRAPPER}} .aew-card-row__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'description_typography',
				'selector'       => '{{WRAPPER}} .aew-card-row__description',
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

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed> $card Repeater row.
	 * @param int                  $index Row index.
	 * @return string
	 */
	private function get_card_image_url( array $card, int $index ): string {
		$image      = $card['card_image'] ?? [];
		$fallback_n = ( $index % 3 ) + 1;
		$fallback   = Widget_Assets::url( self::ASSET_SLUG, 'images/card-' . $fallback_n . '.webp' );

		if ( ! is_array( $image ) ) {
			return $fallback;
		}

		$attachment_id = (int) ( $image['id'] ?? 0 );
		if ( $attachment_id > 0 ) {
			$attachment_url = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( is_string( $attachment_url ) && '' !== $attachment_url ) {
				return $attachment_url;
			}
		}

		$url = (string) ( $image['url'] ?? '' );
		if ( '' === $url ) {
			return $fallback;
		}

		$url = str_replace(
			'/agency-elementor-widgets/agency-elementor-widgets/',
			'/agency-elementor-widgets/',
			$url
		);

		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( is_string( $path ) && preg_match( '#/card-row/images/card-(\d+)\.webp$#', $path, $matches ) ) {
			$canonical = Widget_Assets::url( self::ASSET_SLUG, 'images/card-' . $matches[1] . '.webp' );
			$file_path = Widget_Assets::path( self::ASSET_SLUG, 'images/card-' . $matches[1] . '.webp' );
			if ( is_readable( $file_path ) ) {
				return $canonical;
			}
		}

		return $url;
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$cards    = $settings['cards'] ?? [];

		if ( ! is_array( $cards ) || empty( $cards ) ) {
			return;
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-card-row__inner' );
		$this->add_render_attribute( 'grid', 'class', 'aew-card-row__grid' );

		?>
		<section class="aew-card-row">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'grid' ); ?>>
					<?php foreach ( $cards as $index => $card ) : ?>
						<?php
						if ( ! is_array( $card ) ) {
							continue;
						}

						$title = trim( (string) ( $card['card_title'] ?? '' ) );
						$desc  = (string) ( $card['card_description'] ?? '' );
						$image = $this->get_card_image_url( $card, (int) $index );

						if ( '' === $title && Rich_Text::is_empty( $desc ) && '' === $image ) {
							continue;
						}

						$card_key   = 'card_' . $index;
						$title_key  = $this->get_repeater_setting_key( 'card_title', 'cards', $index );
						$desc_key   = $this->get_repeater_setting_key( 'card_description', 'cards', $index );

						$this->add_render_attribute( $card_key, 'class', 'aew-card-row__card' );
						$this->add_render_attribute( $title_key, 'class', 'aew-card-row__title' );
						$this->add_render_attribute( $desc_key, 'class', 'aew-card-row__description aew-rich-text' );
						$this->add_inline_editing_attributes( $title_key, 'none' );
						$this->add_inline_editing_attributes( $desc_key, 'advanced' );
						?>
						<article <?php $this->print_render_attribute_string( $card_key ); ?>>
							<?php if ( '' !== $image ) : ?>
								<div class="aew-card-row__media">
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async" />
								</div>
							<?php endif; ?>
							<?php if ( '' !== $title ) : ?>
								<h3 <?php $this->print_render_attribute_string( $title_key ); ?>>
									<?php echo esc_html( $title ); ?>
								</h3>
							<?php endif; ?>
							<?php if ( ! Rich_Text::is_empty( $desc ) ) : ?>
								<div <?php $this->print_render_attribute_string( $desc_key ); ?>>
									<?php Rich_Text::echo_html( $desc ); ?>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
