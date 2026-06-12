<?php
/**
 * Show Cards V2 Elementor widget (poster cards for productions / events).
 *
 * A responsive grid of poster cards — each card is a poster image + show title
 * + a single "date | venue" meta line, and the whole card links to the show's
 * page. Built for the Utah Metropolitan Ballet homepage "Productions" (4-up)
 * and "Events" (5-up) grids, but generic enough for any image-title-meta-link
 * card row.
 *
 * Brand-free: every colour reads an `aew-*` Elementor global with a neutral-grey
 * fallback; render() resolves global-bound picks via Color_Vars (§6.8 of the
 * widget build guide / gotcha #19).
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
 * Poster cards — image + title + date/venue meta + whole-card link.
 */
class Widget_Show_Cards_V2 extends Widget_Base {

	private const ASSET_SLUG = 'show-cards-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-show-cards-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Show Cards V2', 'agency-elementor-widgets' );
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
		return [ 'show', 'cards', 'productions', 'events', 'poster', 'grid' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY (guide §5 /
	 * gotcha #16) — the stylesheet owns responsive X padding.
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-shcv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * Default cards mirroring the reference design.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_cards(): array {
		return [
			[
				'title' => esc_html__( 'SNOW WHITE', 'agency-elementor-widgets' ),
				'meta'  => esc_html__( 'Sept 18–19 | UVU Noorda Theater', 'agency-elementor-widgets' ),
				'link'  => [ 'url' => '#' ],
			],
			[
				'title' => esc_html__( 'NUTCRACKER', 'agency-elementor-widgets' ),
				'meta'  => esc_html__( 'Dec 11–21 | The Covey Center', 'agency-elementor-widgets' ),
				'link'  => [ 'url' => '#' ],
			],
			[
				'title' => esc_html__( 'COPPÉLIA', 'agency-elementor-widgets' ),
				'meta'  => esc_html__( 'Mar 4–6 | The Covey Center', 'agency-elementor-widgets' ),
				'link'  => [ 'url' => '' ],
			],
			[
				'title' => esc_html__( 'TRIBUTE', 'agency-elementor-widgets' ),
				'meta'  => esc_html__( 'Apr 15–16 | The Covey Center', 'agency-elementor-widgets' ),
				'link'  => [ 'url' => '#' ],
			],
		];
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_header();
		$this->controls_cards();
		$this->controls_layout();
		$this->style_card();
		$this->style_title();
		$this->style_meta();
	}

	/**
	 * CONTENT tab — optional section heading above the grid.
	 *
	 * @return void
	 */
	private function controls_header(): void {
		$this->start_controls_section(
			's_header',
			[ 'label' => esc_html__( 'Section heading (optional)', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'eyebrow',
			[
				'label'       => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Small label above the heading. Leave empty to hide.', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'p'  => 'p',
				],
			]
		);

		$this->add_responsive_control(
			'header_align',
			[
				'label'     => esc_html__( 'Heading alignment', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [
					'{{WRAPPER}} .aew-shcv2__header' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — the repeater of cards.
	 *
	 * @return void
	 */
	private function controls_cards(): void {
		$this->start_controls_section(
			's_cards',
			[ 'label' => esc_html__( 'Cards', 'agency-elementor-widgets' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Poster image', 'agency-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'SHOW TITLE', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'meta',
			[
				'label'       => esc_html__( 'Date / venue line', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Date | Venue', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'A single line, e.g. “Sept 18–19 | UVU Noorda Theater”.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Card link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'The whole card links here. Leave empty for a non-clickable card.', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'cards',
			[
				'label'       => esc_html__( 'Cards', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_cards(),
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — layout knobs (columns, gap, poster aspect ratio).
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
				'label'          => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				],
				'selectors'      => [
					'{{WRAPPER}} .aew-shcv2__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'          => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 24 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-shcv2__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_ratio',
			[
				'label'     => esc_html__( 'Poster aspect ratio', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3 / 4',
				'options'   => [
					'3 / 4'  => esc_html__( 'Portrait (3:4)', 'agency-elementor-widgets' ),
					'2 / 3'  => esc_html__( 'Tall portrait (2:3)', 'agency-elementor-widgets' ),
					'1 / 1'  => esc_html__( 'Square (1:1)', 'agency-elementor-widgets' ),
					'4 / 3'  => esc_html__( 'Landscape (4:3)', 'agency-elementor-widgets' ),
					'16 / 9' => esc_html__( 'Wide (16:9)', 'agency-elementor-widgets' ),
				],
				'selectors' => [
					'{{WRAPPER}} .aew-shcv2__media' => 'aspect-ratio: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card surface.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section(
			's_style_card',
			[
				'label' => esc_html__( 'Card', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_bg',
			[
				'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-shcv2-section-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-shcv2-card-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'          => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 24 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-shcv2__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'          => esc_html__( 'Image corner radius', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 16 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 12 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-shcv2__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — title colour + typography.
	 *
	 * @return void
	 */
	private function style_title(): void {
		$this->start_controls_section(
			's_style_title',
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
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-shcv2-title: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-shcv2__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 40 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				],
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'      => esc_html__( 'Section heading color', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}}' => '--aew-shcv2-heading: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eyebrow_color',
			[
				'label'     => esc_html__( 'Eyebrow color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-shcv2-eyebrow: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — meta (date/venue) colour + typography.
	 *
	 * @return void
	 */
	private function style_meta(): void {
		$this->start_controls_section(
			's_style_meta',
			[
				'label' => esc_html__( 'Date / venue', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-shcv2-meta: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'meta_typography',
				'selector'       => '{{WRAPPER}} .aew-shcv2__meta',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 18 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 14 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Parse an Elementor URL control value into url/target/rel.
	 *
	 * @param mixed $d URL control value.
	 * @return array{url:string,target:string,rel:string}
	 */
	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) {
			$r .= ' nofollow';
		}
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$cards = $s['cards'] ?? [];
		if ( ! is_array( $cards ) || empty( $cards ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-shcv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-show-cards-v2', '' );

		/*
		 * Resolve colour controls → inline CSS vars on the wrapper so global-bound
		 * picks survive on the front end (§6.8 / gotcha #19). The matching
		 * `selectors` on each control drive the editor preview; this inline value
		 * wins on live.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'section_bg'    => '--aew-shcv2-section-bg',
				'card_bg'       => '--aew-shcv2-card-bg',
				'title_color'   => '--aew-shcv2-title',
				'meta_color'    => '--aew-shcv2-meta',
				'heading_color' => '--aew-shcv2-heading',
				'eyebrow_color' => '--aew-shcv2-eyebrow',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$eyebrow     = (string) ( $s['eyebrow'] ?? '' );
		$heading     = (string) ( $s['heading'] ?? '' );
		$heading_tag = (string) ( $s['heading_tag'] ?? 'h2' );
		$heading_tag = in_array( $heading_tag, [ 'h1', 'h2', 'h3', 'p' ], true ) ? $heading_tag : 'h2';
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-shcv2__inner">
				<?php if ( '' !== trim( $eyebrow ) || '' !== trim( $heading ) ) : ?>
					<div class="aew-shcv2__header">
						<?php if ( '' !== trim( $eyebrow ) ) : ?>
							<p class="aew-shcv2__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $heading ) ) : ?>
							<<?php echo esc_attr( $heading_tag ); ?> class="aew-shcv2__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $heading_tag ); ?>>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="aew-shcv2__grid">
					<?php
					foreach ( $cards as $card ) :
						$image     = $card['image'] ?? [];
						$image_url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
						$title     = (string) ( $card['title'] ?? '' );
						$meta      = (string) ( $card['meta'] ?? '' );
						$link      = $this->parse_link( $card['link'] ?? [] );
						$has_link  = '' !== $link['url'];
						$tag       = $has_link ? 'a' : 'div';
						?>
						<<?php echo esc_attr( $tag ); ?> class="aew-shcv2__card<?php echo $has_link ? ' aew-shcv2__card--link' : ''; ?>"
							<?php if ( $has_link ) : ?>
								href="<?php echo esc_url( $link['url'] ); ?>"
								<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
								<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>
								aria-label="<?php echo esc_attr( $title ); ?>"
							<?php endif; ?>>
							<div class="aew-shcv2__media">
								<?php if ( '' !== $image_url ) : ?>
									<img class="aew-shcv2__img" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" decoding="async" loading="lazy" />
								<?php endif; ?>
							</div>
							<div class="aew-shcv2__body">
								<?php if ( '' !== trim( $title ) ) : ?>
									<h3 class="aew-shcv2__title"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== trim( $meta ) ) : ?>
									<p class="aew-shcv2__meta"><?php echo esc_html( $meta ); ?></p>
								<?php endif; ?>
							</div>
						</<?php echo esc_attr( $tag ); ?>>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
