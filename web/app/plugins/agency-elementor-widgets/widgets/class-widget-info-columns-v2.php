<?php
/**
 * Info Columns V2 Elementor widget ("What Are Timber Frame Kits?" section).
 *
 * A dark banded section with a centered section heading, a 3-up grid of
 * columns (rounded image on top + Teko title + paragraph) and a single
 * centered CTA button below all columns. Column count, gaps, image radius and
 * every colour are editable per-instance from the Elementor Style tab.
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
 * Info columns — dark band, 3 image+text columns, centered CTA.
 */
class Widget_Info_Columns_V2 extends Widget_Base {

	private const ASSET_SLUG = 'info-columns-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-info-columns-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Info Columns V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-columns';
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
		return [ 'columns', 'info', 'feature', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY (see
	 * WIDGET-V2-BUILD-GUIDE §5 / gotcha #16) — the stylesheet owns responsive X
	 * padding.
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-infc__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * Default columns mirroring the reference design.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_columns(): array {
		return [
			[
				'image' => [ 'url' => '' ],
				'title' => esc_html__( 'Notched Kits', 'agency-elementor-widgets' ),
				'text'  => esc_html__( 'Pre-cut, pre-drilled timber frame kits engineered to fit together precisely — no guesswork, no specialty tools.', 'agency-elementor-widgets' ),
			],
			[
				'image' => [ 'url' => '' ],
				'title' => esc_html__( 'Customize Your Kit', 'agency-elementor-widgets' ),
				'text'  => esc_html__( 'Choose your size, finish, and hardware. Every kit is tailored to your space and built to last for decades outdoors.', 'agency-elementor-widgets' ),
			],
			[
				'image' => [ 'url' => '' ],
				'title' => esc_html__( 'DIY or Hire a Pro', 'agency-elementor-widgets' ),
				'text'  => esc_html__( 'Assemble it yourself over a weekend or hand it to your contractor. Clear instructions ship with every kit.', 'agency-elementor-widgets' ),
			],
		];
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_content();
		$this->style_layout();
		$this->style_colors();
		$this->style_button();
	}

	/**
	 * CONTENT tab — heading, columns repeater and CTA.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section(
			's_content',
			[ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ]
		);

		$this->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'What Are Timber Frame Kits?', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h2' => 'H2',
					'h3' => 'H3',
				],
			]
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

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Notched Kits', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'   => esc_html__( 'Text', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Pre-cut, pre-drilled timber frame kits engineered to fit together precisely.', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'       => esc_html__( 'Columns', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_columns(),
				'title_field' => '{{{ title }}}',
			]
		);

		$this->add_control(
			'show_cta',
			[
				'label'        => esc_html__( 'Show CTA button', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label'     => esc_html__( 'CTA label', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Chat With a Designer', 'agency-elementor-widgets' ),
				'condition' => [ 'show_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'cta_link',
			[
				'label'       => esc_html__( 'CTA link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
				'condition'   => [ 'show_cta' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — layout knobs (columns, gap, image radius).
	 *
	 * @return void
	 */
	private function style_layout(): void {
		$this->start_controls_section(
			's_style_layout',
			[
				'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'columns_count',
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
					'{{WRAPPER}} .aew-infc__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'          => esc_html__( 'Gap', 'agency-elementor-widgets' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 32 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'      => [
					'{{WRAPPER}} .aew-infc__grid' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-infc__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_min_height',
			[
				'label'       => esc_html__( 'Card box height (image-less columns)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Minimum height of columns that have no image (e.g. contact cards). Leave empty to size to content.', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'vh' ],
				'range'       => [ 'px' => [ 'min' => 0, 'max' => 800 ], 'vh' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'   => [
					'{{WRAPPER}} .aew-infc__col--noimg' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Card box padding (image-less columns)', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .aew-infc__col--noimg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — colours (band background, heading, title, text).
	 *
	 * @return void
	 */
	private function style_colors(): void {
		$this->start_controls_section(
			's_style_colors',
			[
				'label' => esc_html__( 'Colors', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2A4F41',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Heading color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6F0EC',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-heading: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'heading_typography',
				'selector'       => '{{WRAPPER}} .aew-infc__heading',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 64 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Column title color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-title: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .aew-infc__col-title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 32 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 28 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Column text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6F0EC',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label'       => esc_html__( 'Link color (in text)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Phone / email links inside the column text. Leave empty to match the text color.', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}}' => '--aew-infc-link: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'       => esc_html__( 'Card box color (image-less columns)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Background of columns that have no image (e.g. contact cards).', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}}' => '--aew-infc-card-bg: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'text_typography',
				'selector'       => '{{WRAPPER}} .aew-infc__col-text',
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
	 * STYLE tab — CTA button colours + typography.
	 *
	 * @return void
	 */
	private function style_button(): void {
		$this->start_controls_section(
			's_style_button',
			[
				'label'     => esc_html__( 'Button', 'agency-elementor-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-btn-bg: {{VALUE}};',
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
					'{{WRAPPER}}' => '--aew-infc-btn-bg-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-btn-text: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color_hover',
			[
				'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}' => '--aew-infc-btn-text-hover: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'btn_typography',
				'selector'       => '{{WRAPPER}} .aew-infc__cta',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
					'font_size'   => [
						'default'        => [ 'unit' => 'px', 'size' => 20 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
					],
					'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'btn_radius',
			[
				'label'      => esc_html__( 'Corner radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 8 ],
				'selectors'  => [
					'{{WRAPPER}} .aew-infc__cta' => 'border-radius: {{SIZE}}{{UNIT}};',
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
		$s       = $this->get_settings_for_display();
		$columns = $s['columns'] ?? [];
		if ( ! is_array( $columns ) ) {
			$columns = [];
		}

		$heading     = (string) ( $s['heading'] ?? '' );
		$heading_tag = (string) ( $s['heading_tag'] ?? 'h2' );
		if ( ! in_array( $heading_tag, [ 'h2', 'h3' ], true ) ) {
			$heading_tag = 'h2';
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-infc' );
		$this->add_render_attribute( 'wrapper', 'data-aew-info-columns-v2', '' );

		/*
		 * Emit resolved colours as inline CSS vars on the wrapper.
		 * get_settings_for_display() resolves global colours → hex, so global-
		 * bound picks render on the front end; the matching `selectors` on each
		 * control drive the editor preview.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'bg_color'             => '--aew-infc-bg',
				'heading_color'        => '--aew-infc-heading',
				'title_color'          => '--aew-infc-title',
				'text_color'           => '--aew-infc-text',
				'link_color'           => '--aew-infc-link',
				'card_bg'              => '--aew-infc-card-bg',
				'btn_bg'               => '--aew-infc-btn-bg',
				'btn_bg_hover'         => '--aew-infc-btn-bg-hover',
				'btn_text_color'       => '--aew-infc-btn-text',
				'btn_text_color_hover' => '--aew-infc-btn-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$show_cta = 'yes' === ( $s['show_cta'] ?? '' );
		$cta_text = (string) ( $s['cta_text'] ?? '' );
		$cta_link = $this->parse_link( $s['cta_link'] ?? [] );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-infc__inner">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<<?php echo esc_attr( $heading_tag ); ?> class="aew-infc__heading">
						<?php echo esc_html( $heading ); ?>
					</<?php echo esc_attr( $heading_tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $columns ) ) : ?>
					<div class="aew-infc__grid">
						<?php
						foreach ( $columns as $col ) :
							$image     = $col['image'] ?? [];
							$image_url = is_array( $image ) ? (string) ( $image['url'] ?? '' ) : '';
							$title     = (string) ( $col['title'] ?? '' );
							$text      = (string) ( $col['text'] ?? '' );

							// Skip a column with no image AND no title AND no text.
							if ( '' === $image_url && '' === trim( $title ) && Rich_Text::is_empty( $text ) ) {
								continue;
							}

							// When a column has no image, render a self-contained card
							// (padded colour box) instead of a stranded empty media panel.
							$col_class = '' === $image_url ? 'aew-infc__col aew-infc__col--noimg' : 'aew-infc__col';
							?>
							<div class="<?php echo esc_attr( $col_class ); ?>">
								<?php if ( '' !== $image_url ) : ?>
									<img class="aew-infc__media"
										src="<?php echo esc_url( $image_url ); ?>"
										alt="<?php echo esc_attr( $title ); ?>"
										decoding="async"
										loading="lazy" />
								<?php endif; ?>

								<?php if ( '' !== trim( $title ) ) : ?>
									<h4 class="aew-infc__col-title"><?php echo esc_html( $title ); ?></h4>
								<?php endif; ?>

								<?php if ( ! Rich_Text::is_empty( $text ) ) : ?>
									<div class="aew-infc__col-text aew-rich-text"><?php Rich_Text::echo_html( $text ); ?></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $show_cta && '' !== trim( $cta_text ) ) : ?>
					<div class="aew-infc__cta-wrap">
						<a class="aew-infc__cta"
							href="<?php echo esc_url( $cta_link['url'] ?: '#' ); ?>"
							<?php echo $cta_link['target'] ? 'target="' . esc_attr( $cta_link['target'] ) . '"' : ''; ?>
							<?php echo $cta_link['rel'] ? 'rel="' . esc_attr( $cta_link['rel'] ) . '"' : ''; ?>>
							<?php echo esc_html( $cta_text ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
