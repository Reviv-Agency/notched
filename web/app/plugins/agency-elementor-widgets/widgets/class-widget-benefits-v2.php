<?php
/**
 * Benefits V2 — [Company] brand.
 *
 * "Benefits of a Traditional Pergola" band: a section title over a row of
 * feature columns, each with a circular image, a heading and a paragraph.
 * 4 columns desktop → 2 tablet → 1 mobile. Full-bleed dark green band.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Benefits_V2 extends Widget_Base {

	private const ASSET_SLUG = 'benefits-v2';

	public function get_name(): string      { return 'agency-benefits-v2'; }
	public function get_title(): string     { return esc_html__( 'Benefits V2', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-check-circle'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'benefits', 'features', 'pergola', 'columns' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to our inner wrapper so
	 * the outer block keeps its full-bleed background. Defaults left EMPTY —
	 * a non-empty default emits one non-responsive rule that clobbers the
	 * stylesheet at every breakpoint (see build guide §5 / gotcha #16).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-bnv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_heading();
		$this->controls_items();

		$this->style_body();
		$this->style_title();
		$this->style_item_heading();
		$this->style_item_text();
	}

	private function controls_heading(): void {
		$this->start_controls_section( 's_heading', [ 'label' => 'Section title' ] );
		$this->add_control( 'title', [
			'label'   => 'Title',
			'type'    => Controls_Manager::TEXT,
			'default' => 'Benefits of a Traditional Pergola',
		] );
		$this->add_control( 'title_tag', [
			'label'   => 'Title HTML tag',
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ],
		] );
		$this->end_controls_section();
	}

	private function controls_items(): void {
		$this->start_controls_section( 's_items', [ 'label' => 'Benefit columns' ] );

		$rep = new Repeater();
		$rep->add_control( 'image', [
			'label' => 'Circular image',
			'type'  => Controls_Manager::MEDIA,
		] );
		$rep->add_control( 'image_alt', [
			'label' => 'Image alt text',
			'type'  => Controls_Manager::TEXT,
		] );
		$rep->add_control( 'heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'Heading',
			'label_block' => true,
		] );
		$rep->add_control( 'text', [
			'label'   => 'Paragraph',
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 4,
			'default' => '',
		] );

		$this->add_control( 'items', [
			'label'       => 'Columns',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ heading }}}',
			'default'     => [
				[
					'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/benefit-1-pool-pergola.jpg' ) ],
					'image_alt' => 'Pergola beside a backyard pool',
					'heading'   => 'Timeless Timber Frame Design',
					'text'      => 'Single beam construction on all four sides with curved knee braces provides strong lateral support and a balanced, classic aesthetic.',
				],
				[
					'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/benefit-2-dovetail.jpg' ) ],
					'image_alt' => 'Close-up of dovetail timber joinery',
					'heading'   => 'True Dovetail Craftsmanship',
					'text'      => 'All posts are connected using dovetail joinery for a clean, hardware free appearance that feels architectural and intentional.',
				],
				[
					'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/benefit-3-pavilion.jpg' ) ],
					'image_alt' => 'Backyard timber pavilion with dining set',
					'heading'   => 'Designed & Built for Your Space',
					'text'      => 'With multiple timber sizes and stain options, each pergola is made to order and includes a 3D design rendering so you can fully visualize the finished result before installation.',
				],
				[
					'image'     => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/benefit-4-warranty.png' ) ],
					'image_alt' => '25 year structural warranty badge',
					'heading'   => 'Guaranteed & Backed by 25 Year Structural Warranty',
					'text'      => 'Single beam construction on all four sides with curved knee braces provides strong lateral support and a balanced, classic aesthetic.',
				],
			],
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => 'Body', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'body_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bnv2-body-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'image_size', [
			'label'      => 'Circle size',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 320 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 180 ],
			'selectors'  => [ '{{WRAPPER}} .aew-bnv2__media' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => 'Section title', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'title_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bnv2-title: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-bnv2__title',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 64 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_item_heading(): void {
		$this->start_controls_section( 'ss_item_heading', [ 'label' => 'Column heading', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'item_heading_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bnv2-heading: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'item_heading_typo',
			'selector' => '{{WRAPPER}} .aew-bnv2__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 40 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_item_text(): void {
		$this->start_controls_section( 'ss_item_text', [ 'label' => 'Column paragraph', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'item_text_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-bnv2-text: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'item_text_typo',
			'selector' => '{{WRAPPER}} .aew-bnv2__text',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
			],
		] );
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$title = (string) ( $s['title'] ?? '' );
		$tag   = in_array( $s['title_tag'] ?? 'h2', [ 'h1', 'h2', 'h3' ], true ) ? $s['title_tag'] : 'h2';
		$items = $s['items'] ?? [];

		/*
		 * Resolved colours as inline CSS vars on the wrapper. get_settings_for_display()
		 * resolves global colours → hex so global-bound picks render on the front end
		 * (Elementor drops globals for direct-property custom-control selectors). The
		 * `selectors` on each control drive the editor preview; this inline value wins
		 * on live. CSS consumes each var with a design-system fallback.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'body_bg'            => '--aew-bnv2-body-bg',
				'title_color'        => '--aew-bnv2-title',
				'item_heading_color' => '--aew-bnv2-heading',
				'item_text_color'    => '--aew-bnv2-text',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';
		?>
		<section class="aew-bnv2" data-aew-benefits-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value escaped via esc_attr above ?>>
			<div class="aew-bnv2__inner">

				<?php if ( $title ) : ?>
					<<?php echo esc_html( $tag ); ?> class="aew-bnv2__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $items ) ) : ?>
					<ul class="aew-bnv2__grid">
						<?php foreach ( $items as $item ) :
							$img     = $item['image'] ?? [];
							$img_url = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
							$img_id  = is_array( $img ) ? (int) ( $img['id'] ?? 0 ) : 0;
							$alt     = (string) ( $item['image_alt'] ?? '' );
							$heading = (string) ( $item['heading'] ?? '' );
							$text    = (string) ( $item['text'] ?? '' );

							/*
							 * Intrinsic width/height so the browser reserves the box
							 * before the lazy image loads (Lighthouse "unsized image
							 * element" CLS culprit). Media-library images resolve via
							 * attachment meta; the plugin-shipped defaults via the file.
							 */
							$dims = '';
							if ( $img_id ) {
								$src = wp_get_attachment_image_src( $img_id, 'full' );
								if ( $src && ! empty( $src[1] ) && ! empty( $src[2] ) ) {
									$dims = sprintf( ' width="%d" height="%d"', $src[1], $src[2] );
								}
							} elseif ( '' !== $img_url && defined( 'AEW_PLUGIN_URL' ) && str_starts_with( $img_url, AEW_PLUGIN_URL ) ) {
								$file = AEW_PLUGIN_DIR . rawurldecode( substr( $img_url, strlen( AEW_PLUGIN_URL ) ) );
								$size = is_readable( $file ) ? getimagesize( $file ) : false;
								if ( $size ) {
									$dims = sprintf( ' width="%d" height="%d"', $size[0], $size[1] );
								}
							}
						?>
							<li class="aew-bnv2__item">
								<?php if ( $img_url ) : ?>
									<div class="aew-bnv2__media">
										<img class="aew-bnv2__img"
											src="<?php echo esc_url( $img_url ); ?>"
											alt="<?php echo esc_attr( $alt ); ?>"<?php echo $dims; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integers formatted via sprintf ?>
											loading="lazy"
											decoding="async" />
									</div>
								<?php endif; ?>
								<?php if ( $heading ) : ?>
									<h3 class="aew-bnv2__heading"><?php echo esc_html( $heading ); ?></h3>
								<?php endif; ?>
								<?php if ( $text ) : ?>
									<p class="aew-bnv2__text"><?php echo esc_html( $text ); ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div><!-- /.aew-bnv2__inner -->
		</section>
		<?php
	}
}
