<?php
/**
 * Single Post Content V2 — Notched.
 *
 * Renders the CURRENT post for an Elementor "Single Post" theme template:
 * author avatar + name · date · read-time, a 3-dot Share menu, the title, an
 * "Updated:" line, the full post body, and a footer row with social share
 * buttons (Facebook / X / LinkedIn / copy-link) plus views, comments and a
 * working like heart (shared with Post_Engagement).
 *
 * In the Elementor editor (no real post in context) it falls back to the most
 * recent published post so the widget previews with real content.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Single_Post_V2 extends Widget_Base {

	private const ASSET_SLUG = 'single-post-v2';

	public function get_name(): string      { return 'agency-single-post-v2'; }
	public function get_title(): string     { return esc_html__( 'Single Post Content V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-post-content'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'single', 'post', 'content', 'blog', 'notched' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background.
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-spv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_card();
		$this->style_title();
		$this->style_body();
		$this->style_meta();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'show_author', [
			'label' => esc_html__( 'Show author', 'agency-elementor-widgets' ),
			'type'  => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes',
		] );
		$this->add_control( 'show_updated', [
			'label' => esc_html__( 'Show "Updated:" line', 'agency-elementor-widgets' ),
			'type'  => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes',
		] );
		$this->add_control( 'show_share', [
			'label' => esc_html__( 'Show share / stats footer', 'agency-elementor-widgets' ),
			'type'  => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes',
		] );

		$this->end_controls_section();
	}

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label' => esc_html__( 'Background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#F6F0EC', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-bg: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label' => esc_html__( 'Card background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-card-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'card_border', [
			'label' => esc_html__( 'Card border', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#BFC0BF', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-card-border: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'card_radius', [
			'label' => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default' => [ 'unit' => 'px', 'size' => 24 ],
			'selectors' => [ '{{WRAPPER}} .aew-spv2__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => esc_html__( 'Title', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [
			'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#2A4F41', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-title: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typo', 'selector' => '{{WRAPPER}} .aew-spv2__title',
			'fields_options' => [
				'font_family' => [ 'default' => 'Teko' ], 'font_weight' => [ 'default' => '600' ],
				'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 40 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 28 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => esc_html__( 'Body text', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'body_color', [
			'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#141C19', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-body: {{VALUE}};' ],
		] );
		$this->add_control( 'link_color', [
			'label' => esc_html__( 'Link color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#AA7D44', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-link: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'body_typo', 'selector' => '{{WRAPPER}} .aew-spv2__content',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ], 'font_weight' => [ 'default' => '400' ],
				'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 18 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 16 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 160 ] ],
			],
		] );
		$this->add_control( 'media_radius', [
			'label' => esc_html__( 'Image corner radius', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors' => [ '{{WRAPPER}} .aew-spv2__content img' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_meta(): void {
		$this->start_controls_section( 'ss_meta', [ 'label' => esc_html__( 'Meta & Stats', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'meta_color', [
			'label' => esc_html__( 'Meta text color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#3B413F', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-meta: {{VALUE}};' ],
		] );
		$this->add_control( 'stat_color', [
			'label' => esc_html__( 'Stat / icon color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#3B413F', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-stat: {{VALUE}};' ],
		] );
		$this->add_control( 'like_color', [
			'label' => esc_html__( 'Liked heart color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#D2453F', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-like: {{VALUE}};' ],
		] );
		$this->add_control( 'divider_color', [
			'label' => esc_html__( 'Divider color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#BFC0BF', 'selectors' => [ '{{WRAPPER}}' => '--aew-spv2-divider: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Resolve the post to render: the queried post on a single view, else the
	 * latest published post (editor / preview fallback).
	 */
	private function resolve_post_id(): int {
		$id = (int) get_the_ID();
		if ( $id > 0 && 'post' === get_post_type( $id ) ) {
			return $id;
		}
		$latest = get_posts( [ 'post_type' => 'post', 'numberposts' => 1, 'post_status' => 'publish', 'fields' => 'ids' ] );
		return ! empty( $latest ) ? (int) $latest[0] : 0;
	}

	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$post_id = $this->resolve_post_id();
		if ( $post_id <= 0 ) {
			echo '<p class="aew-spv2__empty">' . esc_html__( 'No post to display.', 'agency-elementor-widgets' ) . '</p>';
			return;
		}

		$show_author  = 'yes' === ( $s['show_author'] ?? 'yes' );
		$show_updated = 'yes' === ( $s['show_updated'] ?? 'yes' );
		$show_share   = 'yes' === ( $s['show_share'] ?? 'yes' );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-spv2-bg',
			'card_bg'       => '--aew-spv2-card-bg',
			'card_border'   => '--aew-spv2-card-border',
			'title_color'   => '--aew-spv2-title',
			'body_color'    => '--aew-spv2-body',
			'link_color'    => '--aew-spv2-link',
			'meta_color'    => '--aew-spv2-meta',
			'stat_color'    => '--aew-spv2-stat',
			'like_color'    => '--aew-spv2-like',
			'divider_color' => '--aew-spv2-divider',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$title      = get_the_title( $post_id );
		$date       = get_the_date( '', $post_id );
		$read       = Post_Engagement::read_time( $post_id );
		$author_id  = (int) get_post_field( 'post_author', $post_id );
		$author     = get_the_author_meta( 'display_name', $author_id );
		$avatar     = get_avatar( $author_id, 64, '', $author, [ 'class' => 'aew-spv2__avatar-img' ] );
		$modified   = get_the_modified_date( '', $post_id );
		$published  = get_the_date( 'Y-m-d', $post_id );
		$modified_y = get_the_modified_date( 'Y-m-d', $post_id );
		$permalink  = get_permalink( $post_id );

		// Post body — run the_content filters for a single post in/out of the loop.
		$post_obj = get_post( $post_id );
		$content  = apply_filters( 'the_content', $post_obj ? $post_obj->post_content : '' );

		$views    = Post_Engagement::views( $post_id );
		$likes    = Post_Engagement::likes( $post_id );
		$comments = Post_Engagement::comments( $post_id );
		$liked    = Post_Engagement::visitor_liked( $post_id );

		$ajax_url = admin_url( 'admin-ajax.php' );
		$nonce    = wp_create_nonce( Post_Engagement::nonce_action() );

		$cfg = wp_json_encode( [
			'ajaxUrl'    => $ajax_url,
			'nonce'      => $nonce,
			'likeAction' => Post_Engagement::like_action(),
			'postId'     => $post_id,
			'copied'     => esc_html__( 'Link copied!', 'agency-elementor-widgets' ),
		] );

		$share_url   = rawurlencode( $permalink );
		$share_title = rawurlencode( $title );
		?>
		<section class="aew-spv2" data-aew-single-post-v2 data-config="<?php echo esc_attr( (string) $cfg ); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="aew-spv2__inner">
				<article class="aew-spv2__card">

					<header class="aew-spv2__head">
						<?php if ( $show_author ) : ?>
							<div class="aew-spv2__byline">
								<span class="aew-spv2__avatar"><?php echo get_avatar( $author_id, 36, '', $author ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<p class="aew-spv2__meta">
									<span class="aew-spv2__author"><?php echo esc_html( $author ); ?></span>
									<span class="aew-spv2__dot" aria-hidden="true">·</span>
									<span class="aew-spv2__date"><?php echo esc_html( $date ); ?></span>
									<span class="aew-spv2__dot" aria-hidden="true">·</span>
									<span class="aew-spv2__read"><?php
										echo esc_html( sprintf( _n( '%d min read', '%d min read', $read, 'agency-elementor-widgets' ), $read ) );
									?></span>
								</p>
								<div class="aew-spv2__share-menu" data-aew-spv2-share>
									<button class="aew-spv2__dots-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-label="<?php esc_attr_e( 'More options', 'agency-elementor-widgets' ); ?>">
										<span class="aew-spv2__dots" aria-hidden="true"><span></span><span></span><span></span></span>
									</button>
									<div class="aew-spv2__menu" role="menu" hidden>
										<button class="aew-spv2__menu-item" type="button" role="menuitem" data-aew-spv2-copy data-url="<?php echo esc_url( $permalink ); ?>">
											<?php esc_html_e( 'Share', 'agency-elementor-widgets' ); ?>
										</button>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<h1 class="aew-spv2__title"><?php echo esc_html( $title ); ?></h1>

						<?php if ( $show_updated && $modified_y !== $published ) : ?>
							<p class="aew-spv2__updated"><?php
								/* translators: %s modified date */
								echo esc_html( sprintf( __( 'Updated: %s', 'agency-elementor-widgets' ), $modified ) );
							?></p>
						<?php endif; ?>
					</header>

					<div class="aew-spv2__content">
						<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content already filtered/sanitized. ?>
					</div>

					<?php if ( $show_share ) : ?>
						<footer class="aew-spv2__footer">
							<div class="aew-spv2__social">
								<a class="aew-spv2__social-btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on Facebook', 'agency-elementor-widgets' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 9h3l.5-3H14V4.5c0-.8.3-1.5 1.5-1.5H17V.2C16.6.1 15.6 0 14.5 0 12 0 10.5 1.5 10.5 4v2H8v3h2.5v9H14V9z"/></svg>
								</a>
								<a class="aew-spv2__social-btn" href="https://twitter.com/intent/tweet?url=<?php echo $share_url; // phpcs:ignore ?>&text=<?php echo $share_title; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on X', 'agency-elementor-widgets' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.5 3h3l-6.6 7.5L21.7 21h-5.9l-4.6-6-5.3 6H3l7-8L2.5 3h6l4.1 5.4L17.5 3zm-1 16h1.6L7.6 4.7H5.9L16.5 19z"/></svg>
								</a>
								<a class="aew-spv2__social-btn" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'agency-elementor-widgets' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3V9zm6 0h3.8v1.7h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.3c0-1.3 0-2.9-1.8-2.9s-2 1.4-2 2.8V21H9V9z"/></svg>
								</a>
								<button class="aew-spv2__social-btn" type="button" data-aew-spv2-copy data-url="<?php echo esc_url( $permalink ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'agency-elementor-widgets' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.6 13.4a1 1 0 0 0 1.4 0l5-5a3 3 0 0 0-4.2-4.2l-2 2 1.4 1.4 2-2a1 1 0 0 1 1.4 1.4l-5 5a1 1 0 0 0 0 1.4zm2.8-2.8a1 1 0 0 0-1.4 0l-5 5a3 3 0 0 0 4.2 4.2l2-2-1.4-1.4-2 2a1 1 0 0 1-1.4-1.4l5-5a1 1 0 0 0 0-1.4z"/></svg>
								</button>
							</div>

							<div class="aew-spv2__stats">
								<span class="aew-spv2__stat aew-spv2__views" aria-label="<?php esc_attr_e( 'Views', 'agency-elementor-widgets' ); ?>">
									<span class="aew-spv2__stat-num"><?php echo esc_html( number_format_i18n( $views ) ); ?></span> <?php esc_html_e( 'views', 'agency-elementor-widgets' ); ?>
								</span>
								<a class="aew-spv2__stat aew-spv2__comments" href="#comments" aria-label="<?php esc_attr_e( 'Comments', 'agency-elementor-widgets' ); ?>">
									<span class="aew-spv2__stat-num"><?php echo esc_html( number_format_i18n( $comments ) ); ?></span> <?php esc_html_e( 'comments', 'agency-elementor-widgets' ); ?>
								</a>
								<button class="aew-spv2__stat aew-spv2__like<?php echo $liked ? ' is-liked' : ''; ?>" type="button" data-aew-spv2-like aria-pressed="<?php echo $liked ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'Like this post', 'agency-elementor-widgets' ); ?>">
									<span class="aew-spv2__stat-num" data-aew-spv2-like-count><?php echo esc_html( number_format_i18n( $likes ) ); ?></span>
									<svg class="aew-spv2__heart" viewBox="0 0 24 24" aria-hidden="true">
										<path d="M12 20.25l-1.45-1.32C5.4 14.25 2 11.16 2 7.5 2 4.42 4.42 2 7.5 2c1.74 0 3.41.81 4.5 2.09C13.09 2.81 14.76 2 16.5 2 19.58 2 22 4.42 22 7.5c0 3.66-3.4 6.75-8.55 11.43L12 20.25z"/>
									</svg>
								</button>
							</div>
						</footer>
					<?php endif; ?>

				</article>
			</div>
		</section>
		<?php
	}
}
