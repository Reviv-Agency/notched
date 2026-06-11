<?php
/**
 * Post Archive V2 — [Company] blog index ("All Posts").
 *
 * Renders a vertical list of blog cards (image left, copy right) with date +
 * read-time, title, excerpt, and a like / view / comment stat row plus a 3-dot
 * "Share" menu. New cards stream in on scroll via admin-ajax (infinite load).
 *
 * Counts and the AJAX endpoints live in includes/class-post-engagement.php; the
 * card markup is rendered there too so the first page (PHP) and later pages
 * (AJAX) are byte-for-byte identical.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Post_Archive_V2 extends Widget_Base {

	private const ASSET_SLUG = 'post-archive-v2';

	public function get_name(): string      { return 'agency-post-archive-v2'; }
	public function get_title(): string     { return esc_html__( 'Post Archive V2', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-posts-grid'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'posts', 'blog', 'archive', 'all posts' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY — the
	 * stylesheet owns responsive X padding (build guide §5, gotcha #16).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-pav2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_content();

		$this->style_section();
		$this->style_heading();
		$this->style_card();
		$this->style_title();
		$this->style_excerpt();
		$this->style_meta();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'All Posts', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'category', [
			'label'       => esc_html__( 'Category', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => '',
			'options'     => $this->category_options(),
			'description' => esc_html__( 'Limit to one category, or leave blank for all posts.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'per_page', [
			'label'   => esc_html__( 'Posts per load', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::NUMBER,
			'min'     => 1,
			'max'     => 24,
			'step'    => 1,
			'default' => 6,
		] );

		$this->add_control( 'infinite', [
			'label'        => esc_html__( 'Infinite scroll', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'agency-elementor-widgets' ),
			'label_off'    => esc_html__( 'Off', 'agency-elementor-widgets' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => esc_html__( 'When off, shows a "Load more" button instead.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	/**
	 * @return array<string, string> slug => name (blank entry first = "All").
	 */
	private function category_options(): array {
		$options = [ '' => esc_html__( '— All categories —', 'agency-elementor-widgets' ) ];
		$terms   = get_terms( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( $term instanceof \WP_Term ) {
					$options[ $term->slug ] = $term->name;
				}
			}
		}
		return $options;
	}

	// ── STYLE ──────────────────────────────────────────────────────────────────

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-bg: {{VALUE}};' ],
		] );


		$this->add_responsive_control( 'card_gap', [
			'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 40 ],
			'selectors'  => [ '{{WRAPPER}} .aew-pav2__list' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-pav2__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 40 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 28 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-card-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_border', [
			'label'     => esc_html__( 'Card border', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-card-border: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'card_radius', [
			'label'      => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-pav2__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => esc_html__( 'Post Title', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-title: {{VALUE}};' ],
		] );

		$this->add_control( 'title_hover', [
			'label'     => esc_html__( 'Color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-title-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'title_typo',
			'selector'       => '{{WRAPPER}} .aew-pav2__title',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 28 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 22 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_excerpt(): void {
		$this->start_controls_section( 'ss_excerpt', [ 'label' => esc_html__( 'Excerpt', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'excerpt_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-excerpt: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'excerpt_typo',
			'selector'       => '{{WRAPPER}} .aew-pav2__excerpt',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 14 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_meta(): void {
		$this->start_controls_section( 'ss_meta', [ 'label' => esc_html__( 'Meta & Stats', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'meta_color', [
			'label'     => esc_html__( 'Date / read-time color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-meta: {{VALUE}};' ],
		] );

		$this->add_control( 'stat_color', [
			'label'     => esc_html__( 'Stat color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-stat: {{VALUE}};' ],
		] );

		$this->add_control( 'like_color', [
			'label'     => esc_html__( 'Liked heart color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#D2453F',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-like: {{VALUE}};' ],
		] );

		$this->add_control( 'divider_color', [
			'label'     => esc_html__( 'Divider color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-pav2-divider: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$heading  = (string) ( $s['heading'] ?? esc_html__( 'All Posts', 'agency-elementor-widgets' ) );
		$category = sanitize_title( (string) ( $s['category'] ?? '' ) );
		$per_page = max( 1, min( 24, (int) ( $s['per_page'] ?? 6 ) ) );
		$infinite = 'yes' === ( $s['infinite'] ?? 'yes' );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-pav2-bg',
			'heading_color' => '--aew-pav2-heading',
			'card_bg'       => '--aew-pav2-card-bg',
			'card_border'   => '--aew-pav2-card-border',
			'title_color'   => '--aew-pav2-title',
			'title_hover'   => '--aew-pav2-title-hover',
			'excerpt_color' => '--aew-pav2-excerpt',
			'meta_color'    => '--aew-pav2-meta',
			'stat_color'    => '--aew-pav2-stat',
			'like_color'    => '--aew-pav2-like',
			'divider_color' => '--aew-pav2-divider',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$query = Post_Engagement::build_query( 1, $per_page, $category );
		$has_more = 1 < (int) $query->max_num_pages;

		$ajax_url = admin_url( 'admin-ajax.php' );
		$nonce    = wp_create_nonce( Post_Engagement::nonce_action() );

		$cfg = wp_json_encode( [
			'ajaxUrl'    => $ajax_url,
			'nonce'      => $nonce,
			'likeAction' => Post_Engagement::like_action(),
			'loadAction' => Post_Engagement::load_action(),
			'category'   => $category,
			'perPage'    => $per_page,
			'infinite'   => $infinite,
			'hasMore'    => $has_more,
			'copied'     => esc_html__( 'Link copied!', 'agency-elementor-widgets' ),
		] );
		?>
		<section class="aew-pav2" data-aew-post-archive-v2 data-config="<?php echo esc_attr( (string) $cfg ); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="aew-pav2__inner">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<h2 class="aew-pav2__heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<div class="aew-pav2__list" data-aew-pav2-list>
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							echo Post_Engagement::render_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in renderer.
						}
						wp_reset_postdata();
					} else {
						echo '<p class="aew-pav2__empty">' . esc_html__( 'No posts yet.', 'agency-elementor-widgets' ) . '</p>';
					}
					?>
				</div>

				<?php if ( $has_more ) : ?>
					<div class="aew-pav2__more" data-aew-pav2-more>
						<button class="aew-pav2__more-btn" type="button" data-aew-pav2-load<?php echo $infinite ? ' hidden' : ''; ?>>
							<?php esc_html_e( 'Load more', 'agency-elementor-widgets' ); ?>
						</button>
						<div class="aew-pav2__spinner" data-aew-pav2-spinner hidden aria-hidden="true">
							<span></span><span></span><span></span>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
