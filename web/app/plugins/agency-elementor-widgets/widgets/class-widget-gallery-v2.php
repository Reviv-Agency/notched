<?php
/**
 * Gallery V2 Elementor widget — responsive image grid with "Load More".
 *
 * A project-photo gallery: an optional eyebrow + heading, a responsive image
 * grid (2/3/4 columns), and a "Load More" button that reveals the remaining
 * rows. Images beyond the initial count are hidden via CSS until revealed by
 * the front-end script. Button colours and the body background are editable
 * per-instance from the Elementor Style tab.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Image grid with a reveal-more control.
 */
class Widget_Gallery_V2 extends Widget_Base {

	private const ASSET_SLUG = 'gallery-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-gallery-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Gallery V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-gallery-grid';
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
	public function get_keywords(): array {
		return [ 'gallery', 'grid', 'images', 'load more', 'notched' ];
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
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY — the
	 * stylesheet owns responsive X padding.
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-galv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_header();
		$this->controls_images();
		$this->controls_layout();
		$this->controls_load_more();
		$this->style_button();
		$this->style_section();
	}

	/**
	 * CONTENT tab — optional eyebrow + heading above the grid.
	 *
	 * @return void
	 */
	private function controls_header(): void {
		$this->start_controls_section(
			's_header',
			[ 'label' => esc_html__( 'Header', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'eyebrow',
			[
				'label'       => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Optional small label', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label'       => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Optional heading', 'agency-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — the repeater of gallery images.
	 *
	 * @return void
	 */
	private function controls_images(): void {
		$this->start_controls_section(
			's_images',
			[ 'label' => esc_html__( 'Images', 'agency-elementor-widgets' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'images',
			[
				'label'       => esc_html__( 'Images', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [],
				'title_field' => esc_html__( 'Image', 'agency-elementor-widgets' ) . ' #{{{ Number(_id) }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — grid layout knobs.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section(
			's_layout',
			[ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors'      => [
					'{{WRAPPER}} .aew-galv2__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Gap between images', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-galv2__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__( 'Image corner radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-galv2__item, {{WRAPPER}} .aew-galv2__item img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — initial count + the Load More button.
	 *
	 * @return void
	 */
	private function controls_load_more(): void {
		$this->start_controls_section(
			's_load_more',
			[ 'label' => esc_html__( 'Load More', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'initial_count',
			[
				'label'   => esc_html__( 'Images shown initially', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 6,
			]
		);

		$this->add_control(
			'show_load_more',
			[
				'label'        => esc_html__( 'Show Load More button', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'load_more_label',
			[
				'label'     => esc_html__( 'Button label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Load More', 'agency-elementor-widgets' ),
				'condition' => [ 'show_load_more' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — Load More button colours.
	 *
	 * @return void
	 */
	private function style_button(): void {
		$this->start_controls_section(
			's_style_button',
			[
				'label' => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-galv2-btn-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-galv2-btn-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-galv2-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_hover',
			[
				'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-galv2-btn-text-hover: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section(
			's_style_section',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_bg',
			[
				'label'     => esc_html__( 'Background color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6F0EC',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-galv2-bg: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$images = $s['images'] ?? [];
		if ( ! is_array( $images ) || empty( $images ) ) {
			return;
		}

		// Keep only items with a usable image URL.
		$valid = [];
		foreach ( $images as $item ) {
			$image = is_array( $item ) ? ( $item['image'] ?? [] ) : [];
			$url   = is_array( $image ) ? trim( (string) ( $image['url'] ?? '' ) ) : '';
			if ( '' === $url ) {
				continue;
			}
			$alt = '';
			if ( ! empty( $image['id'] ) ) {
				$alt = (string) get_post_meta( (int) $image['id'], '_wp_attachment_image_alt', true );
			}
			$valid[] = [ 'url' => $url, 'alt' => $alt ];
		}
		if ( empty( $valid ) ) {
			return;
		}

		$total   = count( $valid );
		$initial = isset( $s['initial_count'] ) ? (int) $s['initial_count'] : 6;
		if ( $initial < 1 ) {
			$initial = 1;
		}

		$eyebrow = (string) ( $s['eyebrow'] ?? '' );
		$heading = (string) ( $s['heading'] ?? '' );

		$show_more = ( 'yes' === ( $s['show_load_more'] ?? '' ) ) && ( $total > $initial );
		$btn_label = (string) ( $s['load_more_label'] ?? '' );
		if ( '' === trim( $btn_label ) ) {
			$btn_label = esc_html__( 'Load More', 'agency-elementor-widgets' );
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-galv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-gallery-v2', '' );

		/*
		 * Emit resolved colours as inline CSS vars on the wrapper. Globals are
		 * resolved to hex by get_settings_for_display(); Color_Vars keeps real
		 * global bindings as var(--e-global-color-*) so they survive on the
		 * live page (Elementor drops globals from custom-control selectors).
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'section_bg'     => '--aew-galv2-bg',
				'btn_bg'         => '--aew-galv2-btn-bg',
				'btn_text'       => '--aew-galv2-btn-text',
				'btn_bg_hover'   => '--aew-galv2-btn-bg-hover',
				'btn_text_hover' => '--aew-galv2-btn-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-galv2__inner">
				<?php if ( '' !== trim( $eyebrow ) || '' !== trim( $heading ) ) : ?>
					<div class="aew-galv2__header">
						<?php if ( '' !== trim( $eyebrow ) ) : ?>
							<p class="aew-galv2__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $heading ) ) : ?>
							<h2 class="aew-galv2__heading"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<ul class="aew-galv2__grid" data-initial="<?php echo esc_attr( (string) $initial ); ?>">
					<?php foreach ( $valid as $index => $img ) : ?>
						<?php
						$item_classes = 'aew-galv2__item';
						if ( $index >= $initial ) {
							$item_classes .= ' aew-galv2__item--hidden';
						}
						?>
						<li class="<?php echo esc_attr( $item_classes ); ?>">
							<img
								class="aew-galv2__img"
								src="<?php echo esc_url( $img['url'] ); ?>"
								alt="<?php echo esc_attr( $img['alt'] ); ?>"
								loading="lazy"
								decoding="async"
							/>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( $show_more ) : ?>
					<div class="aew-galv2__actions">
						<button type="button" class="aew-galv2__more"><?php echo esc_html( $btn_label ); ?></button>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
