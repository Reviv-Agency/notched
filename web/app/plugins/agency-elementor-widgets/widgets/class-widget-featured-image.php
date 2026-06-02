<?php
/**
 * Featured Image Elementor widget (Frame 41).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Cream wrapper with a single full-width image at responsive fixed heights.
 */
class Widget_Featured_Image extends Widget_Base {

	private const ASSET_SLUG = 'featured-image';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-featured-image';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Featured Image', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image';
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
		return [ 'image', 'photo', 'featured', 'frame', 'cream' ];
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
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/featured-image-default.webp' ),
				],
			]
		);

		$this->add_control(
			'image_alt',
			[
				'label'       => esc_html__( 'Alt text', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bright Homes interior renovation', 'agency-elementor-widgets' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$cream_default = '#F8F5F1';

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
					'{{WRAPPER}} .aew-featured-image' => '--aew-featured-image-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
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
					'{{WRAPPER}} .aew-featured-image__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Image height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 120,
						'max' => 1200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 880,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 600,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-featured-image' => '--aew-featured-image-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__( 'Image border radius', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-featured-image' => '--aew-featured-image-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed> $image_setting Media control value.
	 * @return string
	 */
	private function resolve_image_url( array $image_setting ): string {
		$url = trim( (string) ( $image_setting['url'] ?? '' ) );

		if ( '' !== $url && ! empty( $image_setting['id'] ) ) {
			$attachment_url = wp_get_attachment_image_url( (int) $image_setting['id'], 'full' );
			if ( is_string( $attachment_url ) && '' !== $attachment_url ) {
				$url = $attachment_url;
			}
		}

		if ( '' === $url ) {
			return Widget_Assets::url( self::ASSET_SLUG, 'images/featured-image-default.webp' );
		}

		if ( preg_match( '#/featured-image/images/([^/]+\.webp)$#', $url, $matches ) ) {
			return Widget_Assets::url( self::ASSET_SLUG, 'images/' . $matches[1] );
		}

		return $url;
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$image_url = $this->resolve_image_url( (array) ( $settings['image'] ?? [] ) );
		$alt       = trim( (string) ( $settings['image_alt'] ?? '' ) );

		if ( '' === $image_url ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-featured-image' );
		$this->add_render_attribute( 'inner', 'class', 'aew-featured-image__inner' );
		$this->add_render_attribute( 'figure', 'class', 'aew-featured-image__figure' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<figure <?php $this->print_render_attribute_string( 'figure' ); ?>>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
				</figure>
			</div>
		</section>
		<?php
	}
}
