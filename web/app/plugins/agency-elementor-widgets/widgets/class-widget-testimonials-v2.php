<?php
/**
 * Testimonials V2 — Notched brand.
 *
 * "Notched Client Testimonials" section: a centered gold star eyebrow over a
 * section title, then a grid of testimonial cards. Each card shows a rounded
 * project image, a circular avatar (image or initial-letter fallback), a gold
 * star rating, a client name and a review paragraph.
 * 3 columns desktop → 2 tablet → 1 mobile. Sits on the light page background.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Testimonials_V2 extends Widget_Base {

	private const ASSET_SLUG = 'testimonials-v2';

	public function get_name(): string      { return 'agency-testimonials-v2'; }
	public function get_title(): string     { return esc_html__( 'Testimonials V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-testimonial'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'testimonials', 'notched', 'reviews', 'clients', 'stars' ]; }

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
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-tsv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_stars();
		$this->style_title();
		$this->style_name();
		$this->style_review();
	}

	private function controls_heading(): void {
		$this->start_controls_section( 's_heading', [ 'label' => 'Section title' ] );
		$this->add_control( 'show_eyebrow', [
			'label'        => 'Show star eyebrow',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'title', [
			'label'       => 'Title',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Notched Client Testimonials',
			'label_block' => true,
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
		$this->start_controls_section( 's_items', [ 'label' => 'Testimonials' ] );

		$rep = new Repeater();
		$rep->add_control( 'image', [
			'label' => 'Project image',
			'type'  => Controls_Manager::MEDIA,
		] );
		$rep->add_control( 'image_alt', [
			'label' => 'Project image alt text',
			'type'  => Controls_Manager::TEXT,
		] );
		$rep->add_control( 'avatar', [
			'label'       => 'Avatar image',
			'type'        => Controls_Manager::MEDIA,
			'description' => 'Optional. If empty, a circle with the first letter of the name is shown.',
		] );
		$rep->add_control( 'stars', [
			'label'   => 'Star rating',
			'type'    => Controls_Manager::SELECT,
			'default' => '5',
			'options' => [ '5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1' ],
		] );
		$rep->add_control( 'name', [
			'label'       => 'Client name',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Client Name',
			'label_block' => true,
		] );
		$rep->add_control( 'review', [
			'label'   => 'Review',
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 5,
			'default' => '',
		] );

		$this->add_control( 'items', [
			'label'       => 'Testimonials',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ name }}}',
			'default'     => [
				[
					'stars'  => '5',
					'name'   => 'Skeeter Draper',
					'review' => 'The Notched team built our pergola one afternoon a little over 2 years ago and we have been very happy with the fit, finish, and utility of its form and function. I would chose to work with Notched again!',
				],
				[
					'stars'  => '5',
					'name'   => 'Ty Tillotson',
					'review' => 'We have been working with Notched for years! Jardin and his team have always treated us well and done fantastic work for us. Very professional and our clients are always thrilled with their work!',
				],
				[
					'stars'  => '5',
					'name'   => 'Mandy Taylor',
					'review' => 'Notched built us a Zen Den, and it\'s our favorite place to gather outside! Provides awesome shade and still looks amazing! They were very easy to work with and responsive! Highly recommend!',
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
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-tsv2-body-bg: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'body_max_w', [
			'label'      => 'Inner max width',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1440 ],
			'selectors'  => [ '{{WRAPPER}} .aew-tsv2__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_stars(): void {
		$this->start_controls_section( 'ss_stars', [ 'label' => 'Stars', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'star_color', [
			'label'     => 'Star color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-tsv2-star: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => 'Section title', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'title_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-tsv2-title: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-tsv2__title',
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

	private function style_name(): void {
		$this->start_controls_section( 'ss_name', [ 'label' => 'Client name', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'name_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-tsv2-name: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'name_typo',
			'selector' => '{{WRAPPER}} .aew-tsv2__name',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 24 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_review(): void {
		$this->start_controls_section( 'ss_review', [ 'label' => 'Review text', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'review_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-tsv2-review: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'review_typo',
			'selector' => '{{WRAPPER}} .aew-tsv2__review',
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

	/**
	 * Inline SVG for a single filled star. Uses currentColor so the star colour
	 * is driven by the parent's `color` (set from --aew-tsv2-star).
	 */
	private function star_svg(): string {
		return '<svg class="aew-tsv2__star" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2.5l2.9 5.88 6.49.94-4.7 4.58 1.11 6.46L12 17.77 6.2 20.36l1.11-6.46-4.7-4.58 6.49-.94L12 2.5z"/></svg>';
	}

	private function stars_row( int $count, string $label ): string {
		$count = max( 0, min( 5, $count ) );
		$out   = '<div class="aew-tsv2__stars" role="img" aria-label="' . esc_attr( $label ) . '">';
		for ( $i = 0; $i < $count; $i++ ) {
			$out .= $this->star_svg();
		}
		$out .= '</div>';
		return $out;
	}

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$title    = (string) ( $s['title'] ?? '' );
		$tag      = in_array( $s['title_tag'] ?? 'h2', [ 'h1', 'h2', 'h3' ], true ) ? $s['title_tag'] : 'h2';
		$eyebrow  = ( $s['show_eyebrow'] ?? 'yes' ) === 'yes';
		$items    = $s['items'] ?? [];

		/*
		 * Resolved colours as inline CSS vars on the wrapper. Color_Vars::build()
		 * emits the global reference for global-bound picks (Elementor drops those
		 * from front-end CSS for direct-property custom-control selectors) and the
		 * hex for plain picks. The `selectors` on each control drive the editor
		 * preview; this inline value wins on live. CSS consumes each var with a
		 * design-system fallback.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'body_bg'      => '--aew-tsv2-body-bg',
				'star_color'   => '--aew-tsv2-star',
				'title_color'  => '--aew-tsv2-title',
				'name_color'   => '--aew-tsv2-name',
				'review_color' => '--aew-tsv2-review',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';
		?>
		<section class="aew-tsv2" data-aew-testimonials-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value escaped via esc_attr above ?>>
			<div class="aew-tsv2__inner">

				<?php if ( $eyebrow ) : ?>
					<?php echo $this->stars_row( 5, '5 out of 5 stars' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG, label escaped ?>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<<?php echo esc_html( $tag ); ?> class="aew-tsv2__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $items ) ) : ?>
					<ul class="aew-tsv2__grid">
						<?php foreach ( $items as $item ) :
							$img      = $item['image'] ?? [];
							$img_url  = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
							$alt      = (string) ( $item['image_alt'] ?? '' );
							$av       = $item['avatar'] ?? [];
							$av_url   = is_array( $av ) ? ( $av['url'] ?? '' ) : '';
							$stars    = (int) ( $item['stars'] ?? 5 );
							$name     = (string) ( $item['name'] ?? '' );
							$review   = (string) ( $item['review'] ?? '' );
							$initial  = '' !== trim( $name ) ? mb_strtoupper( mb_substr( trim( $name ), 0, 1 ) ) : '';
						?>
							<li class="aew-tsv2__card">

								<?php if ( $img_url ) : ?>
									<div class="aew-tsv2__media">
										<img class="aew-tsv2__img"
											src="<?php echo esc_url( $img_url ); ?>"
											alt="<?php echo esc_attr( $alt ); ?>"
											loading="lazy"
											decoding="async" />
									</div>
								<?php endif; ?>

								<div class="aew-tsv2__body">

									<div class="aew-tsv2__avatar">
										<?php if ( $av_url ) : ?>
											<img class="aew-tsv2__avatar-img"
												src="<?php echo esc_url( $av_url ); ?>"
												alt=""
												loading="lazy"
												decoding="async" />
										<?php else : ?>
											<span class="aew-tsv2__avatar-initial" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
										<?php endif; ?>
									</div>

									<?php
									$plural = 1 === $stars ? 'star' : 'stars';
									echo $this->stars_row( $stars, $stars . ' out of 5 ' . $plural ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG, label escaped
									?>

									<?php if ( $name ) : ?>
										<p class="aew-tsv2__name"><?php echo esc_html( $name ); ?></p>
									<?php endif; ?>

									<?php if ( $review ) : ?>
										<p class="aew-tsv2__review"><?php echo esc_html( $review ); ?></p>
									<?php endif; ?>

								</div><!-- /.aew-tsv2__body -->
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div><!-- /.aew-tsv2__inner -->
		</section>
		<?php
	}
}
