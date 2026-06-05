<?php
/**
 * Gallery V2 Elementor widget — responsive infinite-scroll image grid.
 *
 * A project-photo gallery: an optional eyebrow + heading and a responsive image
 * grid (2/3/4 columns). An initial batch of images is shown; further batches are
 * auto-revealed as the visitor scrolls near the bottom of the grid (no button),
 * driven by an IntersectionObserver watching a sentinel after the grid. Images
 * beyond the initial count are hidden via CSS until revealed by the front-end
 * script. The body background is editable per-instance from the Style tab.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Infinite-scroll image grid.
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
		return [ 'gallery', 'grid', 'images', 'infinite scroll', 'notched' ];
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
		$this->controls_loading();
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

		// Optional CTA button below the grid. Empty by default so existing
		// galleries (e.g. Home) render no button and are unaffected.
		$this->add_control(
			'cta_label',
			[
				'label'       => esc_html__( 'Button text (optional)', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'e.g. Shop all kits', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'cta_link',
			[
				'label'     => esc_html__( 'Button link', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '' ],
				'condition' => [ 'cta_label!' => '' ],
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
					'{{WRAPPER}} .aew-galv2__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr)); column-count: {{VALUE}};',
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
					'{{WRAPPER}} .aew-galv2__grid'                  => 'gap: {{SIZE}}{{UNIT}}; column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .aew-galv2__grid--masonry .aew-galv2__item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'masonry',
			[
				'label'        => esc_html__( 'Masonry layout', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Variable-height images packed into columns (like the source site). Off = uniform square grid.', 'agency-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — infinite-scroll loading knobs.
	 *
	 * @return void
	 */
	private function controls_loading(): void {
		$this->start_controls_section(
			's_loading',
			[ 'label' => esc_html__( 'Loading', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'initial_count',
			[
				'label'   => esc_html__( 'Images shown initially', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 9,
			]
		);

		$this->add_control(
			'batch_size',
			[
				'label'   => esc_html__( 'Images revealed per scroll', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 6,
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
			'bg_color',
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
		$initial = isset( $s['initial_count'] ) ? (int) $s['initial_count'] : 9;
		if ( $initial < 1 ) {
			$initial = 1;
		}
		$batch = isset( $s['batch_size'] ) ? (int) $s['batch_size'] : 6;
		if ( $batch < 1 ) {
			$batch = 1;
		}

		$eyebrow  = (string) ( $s['eyebrow'] ?? '' );
		$heading  = (string) ( $s['heading'] ?? '' );
		$masonry  = 'yes' === ( $s['masonry'] ?? '' );
		$has_more = $total > $initial;

		// Optional CTA below the grid.
		$cta_label = (string) ( $s['cta_label'] ?? '' );
		$cta       = is_array( $s['cta_link'] ?? null ) ? $s['cta_link'] : [];
		$cta_url   = (string) ( $cta['url'] ?? '' );
		$cta_target = ! empty( $cta['is_external'] ) ? '_blank' : '';
		$cta_rel    = '';
		if ( $cta_target ) {
			$cta_rel = 'noopener';
		}
		if ( ! empty( $cta['nofollow'] ) ) {
			$cta_rel = trim( $cta_rel . ' nofollow' );
		}
		$show_cta = '' !== trim( $cta_label ) && '' !== $cta_url;

		$this->add_render_attribute( 'wrapper', 'class', 'aew-galv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-gallery-v2', '' );

		$grid_classes = 'aew-galv2__grid';
		if ( $masonry ) {
			$grid_classes .= ' aew-galv2__grid--masonry';
		}

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
				'bg_color' => '--aew-galv2-bg',
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

				<ul class="<?php echo esc_attr( $grid_classes ); ?>" data-initial="<?php echo esc_attr( (string) $initial ); ?>" data-batch="<?php echo esc_attr( (string) $batch ); ?>">
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

				<?php if ( $has_more ) : ?>
					<div class="aew-galv2__sentinel" aria-hidden="true"></div>
				<?php endif; ?>

				<?php if ( $show_cta ) : ?>
					<div class="aew-galv2__cta-wrap">
						<a class="aew-galv2__cta" href="<?php echo esc_url( $cta_url ); ?>"
							<?php echo $cta_target ? ' target="' . esc_attr( $cta_target ) . '"' : ''; ?>
							<?php echo $cta_rel ? ' rel="' . esc_attr( $cta_rel ) . '"' : ''; ?>>
							<?php echo esc_html( $cta_label ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
