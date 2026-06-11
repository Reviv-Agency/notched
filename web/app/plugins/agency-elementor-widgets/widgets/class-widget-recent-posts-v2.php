<?php
/**
 * Recent Posts V2 — [Company] ("Recent Posts" / related posts row).
 *
 * A "Recent Posts  ·  See All" header above a compact grid of post cards
 * (image on top, title + views/comments/like beneath). On a single post it
 * excludes the current post; elsewhere it shows the latest N posts.
 *
 * Reuses Post_Engagement for the per-post counts; the card markup here is the
 * compact (vertical) variant, distinct from the archive's horizontal card.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use WP_Query;

class Widget_Recent_Posts_V2 extends Widget_Base {

	private const ASSET_SLUG = 'recent-posts-v2';

	public function get_name(): string      { return 'agency-recent-posts-v2'; }
	public function get_title(): string     { return esc_html__( 'Recent Posts V2', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-posts-grid'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'recent', 'related', 'posts', 'blog' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-rpv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ── CONTROLS ────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_content();
		$this->style_section();
		$this->style_heading();
		$this->style_card();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'Recent Posts', 'agency-elementor-widgets' ),
		] );
		$this->add_control( 'see_all_text', [
			'label' => esc_html__( '"See All" label', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'See All', 'agency-elementor-widgets' ),
		] );
		$this->add_control( 'see_all_link', [
			'label' => esc_html__( '"See All" link', 'agency-elementor-widgets' ), 'type' => Controls_Manager::URL,
			'default' => [ 'url' => '/blog/' ], 'placeholder' => esc_html__( '/blog/', 'agency-elementor-widgets' ),
		] );
		$this->add_control( 'count', [
			'label' => esc_html__( 'Number of posts', 'agency-elementor-widgets' ), 'type' => Controls_Manager::NUMBER,
			'min' => 1, 'max' => 12, 'default' => 3,
		] );
		$this->add_responsive_control( 'columns', [
			'label' => esc_html__( 'Columns', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SELECT,
			'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1',
			'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			'selectors' => [ '{{WRAPPER}} .aew-rpv2__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ],
		] );

		$this->end_controls_section();
	}

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label' => esc_html__( 'Background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-bg: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'grid_gap', [
			'label' => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default' => [ 'unit' => 'px', 'size' => 24 ],
			'selectors' => [ '{{WRAPPER}} .aew-rpv2__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label' => esc_html__( 'Heading color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-heading: {{VALUE}};' ],
		] );
		$this->add_control( 'see_all_color', [
			'label' => esc_html__( '"See All" color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-see-all: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'heading_typo', 'selector' => '{{WRAPPER}} .aew-rpv2__heading',
			'fields_options' => [
				'font_family' => [ 'default' => 'Teko' ], 'font_weight' => [ 'default' => '600' ],
				'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 28 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label' => esc_html__( 'Card background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-card-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'card_border', [
			'label' => esc_html__( 'Card border', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-card-border: {{VALUE}};' ],
		] );
		$this->add_control( 'title_color', [
			'label' => esc_html__( 'Title color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-title: {{VALUE}};' ],
		] );
		$this->add_control( 'stat_color', [
			'label' => esc_html__( 'Stat color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-stat: {{VALUE}};' ],
		] );
		$this->add_control( 'like_color', [
			'label' => esc_html__( 'Liked heart color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#D2453F', 'selectors' => [ '{{WRAPPER}}' => '--aew-rpv2-like: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// ── helpers ─────────────────────────────────────────────────────────────────

	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) { return [ 'url' => '', 'target' => '', 'rel' => '' ]; }
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) { $r .= ' nofollow'; }
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}

	// ── RENDER ────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$heading  = (string) ( $s['heading'] ?? 'Recent Posts' );
		$see_lbl  = (string) ( $s['see_all_text'] ?? 'See All' );
		$see_link = $this->parse_link( $s['see_all_link'] ?? [] );
		$count    = max( 1, min( 12, (int) ( $s['count'] ?? 3 ) ) );

		$current = (int) get_the_ID();
		$args    = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		];
		if ( $current > 0 && 'post' === get_post_type( $current ) ) {
			$args['post__not_in'] = [ $current ];
		}
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return;
		}

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-rpv2-bg',
			'heading_color' => '--aew-rpv2-heading',
			'see_all_color' => '--aew-rpv2-see-all',
			'card_bg'       => '--aew-rpv2-card-bg',
			'card_border'   => '--aew-rpv2-card-border',
			'title_color'   => '--aew-rpv2-title',
			'stat_color'    => '--aew-rpv2-stat',
			'like_color'    => '--aew-rpv2-like',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$cfg = wp_json_encode( [
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( Post_Engagement::nonce_action() ),
			'likeAction' => Post_Engagement::like_action(),
		] );
		?>
		<section class="aew-rpv2" data-aew-recent-posts-v2 data-config="<?php echo esc_attr( (string) $cfg ); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="aew-rpv2__inner">
				<div class="aew-rpv2__head">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<h2 class="aew-rpv2__heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== trim( $see_lbl ) && '' !== $see_link['url'] ) : ?>
						<a class="aew-rpv2__see-all" href="<?php echo esc_url( $see_link['url'] ); ?>"
							<?php echo $see_link['target'] ? 'target="' . esc_attr( $see_link['target'] ) . '"' : ''; ?>
							<?php echo $see_link['rel'] ? 'rel="' . esc_attr( $see_link['rel'] ) . '"' : ''; ?>>
							<?php echo esc_html( $see_lbl ); ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="aew-rpv2__grid">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$pid       = get_the_ID();
						$permalink = get_permalink( $pid );
						$img       = get_the_post_thumbnail_url( $pid, 'medium_large' );
						$views     = Post_Engagement::views( $pid );
						$likes     = Post_Engagement::likes( $pid );
						$comments  = Post_Engagement::comments( $pid );
						$liked     = Post_Engagement::visitor_liked( $pid );
						?>
						<article class="aew-rpv2__card">
							<a class="aew-rpv2__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
								<?php if ( $img ) : ?>
									<img class="aew-rpv2__img" src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" decoding="async" />
								<?php else : ?>
									<span class="aew-rpv2__img aew-rpv2__img--placeholder" aria-hidden="true"></span>
								<?php endif; ?>
							</a>
							<div class="aew-rpv2__body">
								<h3 class="aew-rpv2__title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h3>
								<div class="aew-rpv2__footer">
									<span class="aew-rpv2__stat aew-rpv2__views" aria-label="<?php esc_attr_e( 'Views', 'agency-elementor-widgets' ); ?>">
										<svg class="aew-rpv2__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c-5 0-9.3 3.1-11 7 1.7 3.9 6 7 11 7s9.3-3.1 11-7c-1.7-3.9-6-7-11-7zm0 11.5A4.5 4.5 0 1 1 12 7.5a4.5 4.5 0 0 1 0 9zm0-7A2.5 2.5 0 1 0 12 14.5 2.5 2.5 0 0 0 12 9.5z"/></svg>
										<span class="aew-rpv2__stat-num"><?php echo esc_html( number_format_i18n( $views ) ); ?></span>
									</span>
									<a class="aew-rpv2__stat aew-rpv2__comments" href="<?php echo esc_url( $permalink ); ?>#comments" aria-label="<?php esc_attr_e( 'Comments', 'agency-elementor-widgets' ); ?>">
										<svg class="aew-rpv2__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V6a2 2 0 0 1 2-2z"/></svg>
										<span class="aew-rpv2__stat-num"><?php echo esc_html( number_format_i18n( $comments ) ); ?></span>
									</a>
									<button class="aew-rpv2__stat aew-rpv2__like<?php echo $liked ? ' is-liked' : ''; ?>" type="button"
										data-aew-rpv2-like data-post-id="<?php echo esc_attr( (string) $pid ); ?>"
										aria-pressed="<?php echo $liked ? 'true' : 'false'; ?>"
										aria-label="<?php esc_attr_e( 'Like this post', 'agency-elementor-widgets' ); ?>">
										<svg class="aew-rpv2__icon aew-rpv2__heart" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.25l-1.45-1.32C5.4 14.25 2 11.16 2 7.5 2 4.42 4.42 2 7.5 2c1.74 0 3.41.81 4.5 2.09C13.09 2.81 14.76 2 16.5 2 19.58 2 22 4.42 22 7.5c0 3.66-3.4 6.75-8.55 11.43L12 20.25z"/></svg>
										<span class="aew-rpv2__stat-num" data-aew-rpv2-like-count><?php echo esc_html( number_format_i18n( $likes ) ); ?></span>
									</button>
								</div>
							</div>
						</article>
						<?php
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
