<?php
/**
 * Testimonial custom post type for Testimonial Grid widget.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Registers bh_testimonial CPT and admin meta.
 */
final class Cpt_Testimonial {

	public const POST_TYPE = 'bh_testimonial';

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', [ self::class, 'register_post_type' ] );
		add_action( 'init', [ self::class, 'register_meta' ] );
		add_action( 'add_meta_boxes', [ self::class, 'register_meta_boxes' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ self::class, 'save_meta' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_admin_scripts' ] );
	}

	/**
	 * @param string $hook_suffix Admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_scripts( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
	}

	/**
	 * @return void
	 */
	public static function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'              => [
					'name'               => esc_html__( 'Testimonials', 'agency-elementor-widgets' ),
					'singular_name'      => esc_html__( 'Testimonial', 'agency-elementor-widgets' ),
					'add_new'            => esc_html__( 'Add New', 'agency-elementor-widgets' ),
					'add_new_item'       => esc_html__( 'Add New Testimonial', 'agency-elementor-widgets' ),
					'edit_item'          => esc_html__( 'Edit Testimonial', 'agency-elementor-widgets' ),
					'new_item'           => esc_html__( 'New Testimonial', 'agency-elementor-widgets' ),
					'view_item'          => esc_html__( 'View Testimonial', 'agency-elementor-widgets' ),
					'search_items'       => esc_html__( 'Search Testimonials', 'agency-elementor-widgets' ),
					'not_found'          => esc_html__( 'No testimonials found', 'agency-elementor-widgets' ),
					'not_found_in_trash' => esc_html__( 'No testimonials found in Trash', 'agency-elementor-widgets' ),
				],
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-format-quote',
				'menu_position'       => 26,
				'has_archive'         => false,
				'rewrite'             => [ 'slug' => 'testimonial' ],
				'supports'            => [ 'title', 'thumbnail' ],
				'capability_type'     => 'post',
			]
		);
	}

	/**
	 * @return void
	 */
	public static function register_meta(): void {
		register_post_meta(
			self::POST_TYPE,
			'bh_profile_image_id',
			[
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_posts' );
				},
			]
		);

		foreach ( [ 'bh_reviewer_name', 'bh_project_meta', 'bh_quote_title' ] as $key ) {
			register_post_meta(
				self::POST_TYPE,
				$key,
				[
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => static function (): bool {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}

		register_post_meta(
			self::POST_TYPE,
			'bh_quote_body',
			[
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => [ Rich_Text::class, 'sanitize' ],
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	/**
	 * @return void
	 */
	public static function register_meta_boxes(): void {
		add_meta_box(
			'bh_testimonial_details',
			esc_html__( 'Testimonial details', 'agency-elementor-widgets' ),
			[ self::class, 'render_meta_box' ],
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'bh_testimonial_meta_save', 'bh_testimonial_meta_nonce' );

		$profile_id   = (int) get_post_meta( $post->ID, 'bh_profile_image_id', true );
		$reviewer     = (string) get_post_meta( $post->ID, 'bh_reviewer_name', true );
		$project_meta = (string) get_post_meta( $post->ID, 'bh_project_meta', true );
		$quote_title  = (string) get_post_meta( $post->ID, 'bh_quote_title', true );
		$quote_body   = (string) get_post_meta( $post->ID, 'bh_quote_body', true );
		$profile_url  = $profile_id > 0 ? wp_get_attachment_image_url( $profile_id, 'thumbnail' ) : '';
		?>
		<p>
			<label for="bh_profile_image_id"><strong><?php esc_html_e( 'Profile image', 'agency-elementor-widgets' ); ?></strong></label><br />
			<input type="hidden" id="bh_profile_image_id" name="bh_profile_image_id" value="<?php echo esc_attr( (string) $profile_id ); ?>" />
			<button type="button" class="button" id="bh_profile_image_select"><?php esc_html_e( 'Select image', 'agency-elementor-widgets' ); ?></button>
			<button type="button" class="button" id="bh_profile_image_clear"><?php esc_html_e( 'Remove', 'agency-elementor-widgets' ); ?></button>
			<span id="bh_profile_image_preview" style="display:block;margin-top:8px;">
				<?php if ( $profile_url ) : ?>
					<img src="<?php echo esc_url( $profile_url ); ?>" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;" />
				<?php endif; ?>
			</span>
		</p>
		<p>
			<label for="bh_reviewer_name"><strong><?php esc_html_e( 'Reviewer name', 'agency-elementor-widgets' ); ?></strong></label><br />
			<input type="text" class="widefat" id="bh_reviewer_name" name="bh_reviewer_name" value="<?php echo esc_attr( $reviewer ); ?>" placeholder="Mandi J." />
		</p>
		<p>
			<label for="bh_project_meta"><strong><?php esc_html_e( 'Project / location', 'agency-elementor-widgets' ); ?></strong></label><br />
			<input type="text" class="widefat" id="bh_project_meta" name="bh_project_meta" value="<?php echo esc_attr( $project_meta ); ?>" placeholder="Whole Home Remodel, Draper, Utah County" />
			<span class="description"><?php esc_html_e( 'Shown after the name, separated by |', 'agency-elementor-widgets' ); ?></span>
		</p>
		<p>
			<label for="bh_quote_title"><strong><?php esc_html_e( 'Quote title', 'agency-elementor-widgets' ); ?></strong></label><br />
			<input type="text" class="widefat" id="bh_quote_title" name="bh_quote_title" value="<?php echo esc_attr( $quote_title ); ?>" />
		</p>
		<p>
			<label for="bh_quote_body"><strong><?php esc_html_e( 'Quote body', 'agency-elementor-widgets' ); ?></strong></label>
		</p>
		<?php
		wp_editor(
			$quote_body,
			'bh_quote_body',
			[
				'textarea_name' => 'bh_quote_body',
				'textarea_rows' => 6,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => true,
			]
		);
		?>
		<p class="description"><?php esc_html_e( 'Set the Featured Image as the large project photo for this card.', 'agency-elementor-widgets' ); ?></p>
		<script>
		(function ($) {
			var frame;
			$('#bh_profile_image_select').on('click', function (e) {
				e.preventDefault();
				if (frame) {
					frame.open();
					return;
				}
				frame = wp.media({
					title: '<?php echo esc_js( __( 'Select profile image', 'agency-elementor-widgets' ) ); ?>',
					button: { text: '<?php echo esc_js( __( 'Use image', 'agency-elementor-widgets' ) ); ?>' },
					multiple: false
				});
				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					$('#bh_profile_image_id').val(attachment.id);
					$('#bh_profile_image_preview').html('<img src="' + attachment.url + '" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;" />');
				});
				frame.open();
			});
			$('#bh_profile_image_clear').on('click', function (e) {
				e.preventDefault();
				$('#bh_profile_image_id').val('');
				$('#bh_profile_image_preview').empty();
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public static function save_meta( int $post_id, \WP_Post $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['bh_testimonial_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bh_testimonial_meta_nonce'] ) ), 'bh_testimonial_meta_save' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$profile_id = isset( $_POST['bh_profile_image_id'] ) ? absint( $_POST['bh_profile_image_id'] ) : 0;
		update_post_meta( $post_id, 'bh_profile_image_id', $profile_id );

		$fields = [
			'bh_reviewer_name' => 'sanitize_text_field',
			'bh_project_meta'  => 'sanitize_text_field',
			'bh_quote_title'   => 'sanitize_text_field',
			'bh_quote_body'    => [ Rich_Text::class, 'sanitize' ],
		];

		foreach ( $fields as $key => $callback ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$value = call_user_func( $callback, wp_unslash( $_POST[ $key ] ) );
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * @return array<int, string> Post ID => title.
	 */
	public static function get_post_options(): array {
		$posts = get_posts(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);

		$options = [];
		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}
}
