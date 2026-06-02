<?php
/**
 * Post Engagement — backend for the Post Archive V2 widget.
 *
 * Responsibilities:
 *   - Track post VIEWS: increment `_aew_views` meta once per single-post load
 *     (deduped per visitor with a short-lived cookie so a refresh storm doesn't
 *     inflate the count).
 *   - Handle LIKES: a public admin-ajax toggle (`aew_post_like`) that stores the
 *     total in `_aew_likes` and remembers the visitor's own likes in a cookie so
 *     the heart renders filled on return and a second click un-likes.
 *   - Serve INFINITE SCROLL: a public admin-ajax endpoint (`aew_load_posts`)
 *     that returns the next page of cards as rendered HTML, using the SAME card
 *     renderer the widget uses so markup never drifts.
 *
 * Counts live in post meta (no custom table) — likes/views are cheap scalars and
 * benefit from WP's object cache. Mirrors the self-contained pattern established
 * by Lead_Store.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use WP_Query;

final class Post_Engagement {

	public const VIEWS_META = '_aew_views';
	public const LIKES_META = '_aew_likes';

	private const NONCE_ACTION   = 'aew_post_engagement';
	private const LIKE_ACTION    = 'aew_post_like';
	private const LOAD_ACTION    = 'aew_load_posts';
	private const VIEW_COOKIE    = 'aew_viewed';
	private const LIKE_COOKIE    = 'aew_liked';
	private const VIEW_TTL       = DAY_IN_SECONDS;       // de-dupe window per post.
	private const LIKE_TTL       = YEAR_IN_SECONDS;      // remember a like ~indefinitely.

	public static function init(): void {
		add_action( 'wp_ajax_' . self::LIKE_ACTION, [ __CLASS__, 'handle_like' ] );
		add_action( 'wp_ajax_nopriv_' . self::LIKE_ACTION, [ __CLASS__, 'handle_like' ] );

		add_action( 'wp_ajax_' . self::LOAD_ACTION, [ __CLASS__, 'handle_load' ] );
		add_action( 'wp_ajax_nopriv_' . self::LOAD_ACTION, [ __CLASS__, 'handle_load' ] );

		// Count a view on the front-end singular post render.
		add_action( 'wp', [ __CLASS__, 'maybe_count_view' ] );
	}

	public static function nonce_action(): string { return self::NONCE_ACTION; }
	public static function like_action(): string  { return self::LIKE_ACTION; }
	public static function load_action(): string  { return self::LOAD_ACTION; }

	// ── Counts ─────────────────────────────────────────────────────────────────

	public static function views( int $post_id ): int {
		return max( 0, (int) get_post_meta( $post_id, self::VIEWS_META, true ) );
	}

	public static function likes( int $post_id ): int {
		return max( 0, (int) get_post_meta( $post_id, self::LIKES_META, true ) );
	}

	public static function comments( int $post_id ): int {
		return (int) get_comments_number( $post_id );
	}

	/**
	 * Has THIS visitor liked the post (per their cookie)?
	 */
	public static function visitor_liked( int $post_id ): bool {
		$liked = self::cookie_ids( self::LIKE_COOKIE );
		return in_array( $post_id, $liked, true );
	}

	// ── View tracking ────────────────────────────────────────────────────────────

	public static function maybe_count_view(): void {
		if ( is_admin() || ! is_singular( 'post' ) ) {
			return;
		}
		// Don't count bots, feeds, or previews.
		if ( is_feed() || is_preview() || is_robots() ) {
			return;
		}

		$post_id = (int) get_queried_object_id();
		if ( $post_id <= 0 ) {
			return;
		}

		$viewed = self::cookie_ids( self::VIEW_COOKIE );
		if ( in_array( $post_id, $viewed, true ) ) {
			return; // already counted within the TTL window.
		}

		$current = self::views( $post_id );
		update_post_meta( $post_id, self::VIEWS_META, $current + 1 );

		$viewed[] = $post_id;
		self::set_cookie( self::VIEW_COOKIE, $viewed, self::VIEW_TTL );
	}

	// ── Like toggle (AJAX) ───────────────────────────────────────────────────────

	public static function handle_like(): void {
		self::verify_nonce();

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$post    = $post_id ? get_post( $post_id ) : null;
		if ( ! $post || 'publish' !== get_post_status( $post ) ) {
			wp_send_json_error( [ 'message' => __( 'Post not found.', 'agency-elementor-widgets' ) ], 404 );
		}

		$liked_ids = self::cookie_ids( self::LIKE_COOKIE );
		$already   = in_array( $post_id, $liked_ids, true );
		$count     = self::likes( $post_id );

		if ( $already ) {
			$count = max( 0, $count - 1 );
			$liked_ids = array_values( array_diff( $liked_ids, [ $post_id ] ) );
			$liked = false;
		} else {
			$count++;
			$liked_ids[] = $post_id;
			$liked = true;
		}

		update_post_meta( $post_id, self::LIKES_META, $count );
		self::set_cookie( self::LIKE_COOKIE, $liked_ids, self::LIKE_TTL );

		wp_send_json_success( [
			'liked' => $liked,
			'count' => $count,
		] );
	}

	// ── Infinite scroll (AJAX) ───────────────────────────────────────────────────

	public static function handle_load(): void {
		self::verify_nonce();

		$page     = isset( $_POST['page'] ) ? max( 1, absint( wp_unslash( $_POST['page'] ) ) ) : 1;
		$per_page = isset( $_POST['per_page'] ) ? absint( wp_unslash( $_POST['per_page'] ) ) : 6;
		$per_page = min( 24, max( 1, $per_page ) );
		$category = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';

		$query = self::build_query( $page, $per_page, $category );

		$html = '';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$html .= self::render_card( get_the_ID() );
			}
		}
		wp_reset_postdata();

		wp_send_json_success( [
			'html'     => $html,
			'has_more' => $page < (int) $query->max_num_pages,
			'page'     => $page,
		] );
	}

	/**
	 * Shared post query so the widget's first page and the AJAX pages match.
	 *
	 * @param int    $page     1-based page.
	 * @param int    $per_page Posts per page.
	 * @param string $category Category slug filter, or '' for all.
	 */
	public static function build_query( int $page, int $per_page, string $category = '' ): WP_Query {
		$args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false, // we need max_num_pages for "has_more".
		];
		if ( '' !== $category ) {
			$args['category_name'] = $category;
		}

		return new WP_Query( $args );
	}

	// ── Card renderer (shared by widget + AJAX) ──────────────────────────────────

	/**
	 * Render one blog card. Loop context (the_post) must be set, OR pass a valid
	 * published post id and we set it up internally is NOT done here — callers
	 * inside the loop pass get_the_ID().
	 *
	 * @param int $post_id Published post id (loop already on this post).
	 */
	public static function render_card( int $post_id ): string {
		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$date      = get_the_date( '', $post_id );
		$read      = self::read_time( $post_id );
		$excerpt   = self::excerpt( $post_id );
		$img       = get_the_post_thumbnail_url( $post_id, 'large' );

		$views    = self::views( $post_id );
		$likes    = self::likes( $post_id );
		$comments = self::comments( $post_id );
		$liked    = self::visitor_liked( $post_id );

		ob_start();
		?>
		<article class="aew-pav2__card" data-aew-pav2-card data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
			<a class="aew-pav2__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
				<?php if ( $img ) : ?>
					<img class="aew-pav2__img" src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" decoding="async" />
				<?php else : ?>
					<span class="aew-pav2__img aew-pav2__img--placeholder" aria-hidden="true"></span>
				<?php endif; ?>
			</a>

			<div class="aew-pav2__body">
				<div class="aew-pav2__top">
					<p class="aew-pav2__meta">
						<span class="aew-pav2__date"><?php echo esc_html( $date ); ?></span>
						<span class="aew-pav2__dot" aria-hidden="true">·</span>
						<span class="aew-pav2__read"><?php
							/* translators: %d minutes */
							echo esc_html( sprintf( _n( '%d min read', '%d min read', $read, 'agency-elementor-widgets' ), $read ) );
						?></span>
					</p>

					<div class="aew-pav2__share" data-aew-pav2-share>
						<button class="aew-pav2__share-btn" type="button"
							aria-haspopup="true" aria-expanded="false"
							aria-label="<?php esc_attr_e( 'More options', 'agency-elementor-widgets' ); ?>">
							<span class="aew-pav2__dots" aria-hidden="true">
								<span></span><span></span><span></span>
							</span>
						</button>
						<div class="aew-pav2__menu" role="menu" hidden>
							<button class="aew-pav2__menu-item" type="button" role="menuitem"
								data-aew-pav2-copy data-url="<?php echo esc_url( $permalink ); ?>">
								<?php esc_html_e( 'Share', 'agency-elementor-widgets' ); ?>
							</button>
						</div>
					</div>
				</div>

				<h3 class="aew-pav2__title">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
				</h3>

				<?php if ( '' !== $excerpt ) : ?>
					<p class="aew-pav2__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<div class="aew-pav2__footer">
					<button class="aew-pav2__stat aew-pav2__like<?php echo $liked ? ' is-liked' : ''; ?>"
						type="button"
						data-aew-pav2-like
						aria-pressed="<?php echo $liked ? 'true' : 'false'; ?>"
						aria-label="<?php esc_attr_e( 'Like this post', 'agency-elementor-widgets' ); ?>">
						<svg class="aew-pav2__icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M12 20.25l-1.45-1.32C5.4 14.25 2 11.16 2 7.5 2 4.42 4.42 2 7.5 2c1.74 0 3.41.81 4.5 2.09C13.09 2.81 14.76 2 16.5 2 19.58 2 22 4.42 22 7.5c0 3.66-3.4 6.75-8.55 11.43L12 20.25z"/>
						</svg>
						<span class="aew-pav2__stat-num" data-aew-pav2-like-count><?php echo esc_html( number_format_i18n( $likes ) ); ?></span>
					</button>

					<span class="aew-pav2__stat aew-pav2__views" aria-label="<?php esc_attr_e( 'Views', 'agency-elementor-widgets' ); ?>">
						<svg class="aew-pav2__icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M12 5c-5 0-9.3 3.1-11 7 1.7 3.9 6 7 11 7s9.3-3.1 11-7c-1.7-3.9-6-7-11-7zm0 11.5A4.5 4.5 0 1 1 12 7.5a4.5 4.5 0 0 1 0 9zm0-7A2.5 2.5 0 1 0 12 14.5 2.5 2.5 0 0 0 12 9.5z"/>
						</svg>
						<span class="aew-pav2__stat-num"><?php echo esc_html( number_format_i18n( $views ) ); ?></span>
					</span>

					<a class="aew-pav2__stat aew-pav2__comments" href="<?php echo esc_url( $permalink ); ?>#comments"
						aria-label="<?php esc_attr_e( 'Comments', 'agency-elementor-widgets' ); ?>">
						<svg class="aew-pav2__icon" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M4 4h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V6a2 2 0 0 1 2-2z"/>
						</svg>
						<span class="aew-pav2__stat-num"><?php echo esc_html( number_format_i18n( $comments ) ); ?></span>
					</a>
				</div>
			</div>
		</article>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Estimate read time in whole minutes (>=1) at ~200 wpm.
	 */
	public static function read_time( int $post_id ): int {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return 1;
		}
		$words = str_word_count( wp_strip_all_tags( (string) $post->post_content ) );
		return max( 1, (int) ceil( $words / 200 ) );
	}

	/**
	 * Trimmed excerpt suitable for a card (no markup, ~28 words).
	 */
	public static function excerpt( int $post_id ): string {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}
		$raw = has_excerpt( $post_id ) ? $post->post_excerpt : $post->post_content;
		$raw = wp_strip_all_tags( strip_shortcodes( (string) $raw ) );
		return wp_trim_words( $raw, 28, '…' );
	}

	// ── Cookie helpers ───────────────────────────────────────────────────────────

	/**
	 * @return int[] Post ids stored in the named cookie.
	 */
	private static function cookie_ids( string $name ): array {
		if ( empty( $_COOKIE[ $name ] ) ) {
			return [];
		}
		$raw = sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) );
		$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
		return array_values( array_unique( $ids ) );
	}

	/**
	 * @param int[] $ids Post ids to persist (capped to keep the cookie small).
	 */
	private static function set_cookie( string $name, array $ids, int $ttl ): void {
		$ids = array_slice( array_values( array_unique( array_map( 'absint', $ids ) ) ), -200 );
		$val = implode( ',', $ids );
		// headers_sent() guard: AJAX handlers run before output, single-view runs
		// on `wp` (also before headers) — but stay defensive.
		if ( ! headers_sent() ) {
			setcookie( $name, $val, time() + $ttl, COOKIEPATH ?: '/', COOKIE_DOMAIN ?: '', is_ssl(), false );
		}
		$_COOKIE[ $name ] = $val; // reflect immediately for same-request reads.
	}

	private static function verify_nonce(): void {
		$nonce = isset( $_POST['_aew_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_aew_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed. Please reload and try again.', 'agency-elementor-widgets' ) ], 400 );
		}
	}
}
