<?php
/**
 * Comments V2 — [Company].
 *
 * Renders the native WordPress comment list + form for the current post,
 * brand-styled, inside a card. Uses wp_list_comments() + comment_form() so
 * comments are real WP comments (stored, moderatable in wp-admin, and counted
 * by Post_Engagement::comments()).
 *
 * Theme-builder safe: resolves the current post id and primes the comment query
 * even when rendered through an Elementor single template.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Comments_V2 extends Widget_Base {

	private const ASSET_SLUG = 'comments-v2';

	public function get_name(): string      { return 'agency-comments-v2'; }
	public function get_title(): string     { return esc_html__( 'Comments V2', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-comments'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'comments', 'discussion', 'blog' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ 'comment-reply', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-cmv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->style_card();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'Comments', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label' => esc_html__( 'Background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-bg: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card & text', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label' => esc_html__( 'Card background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-card-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'card_border', [
			'label' => esc_html__( 'Card border', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-card-border: {{VALUE}};' ],
		] );
		$this->add_control( 'heading_color', [
			'label' => esc_html__( 'Heading color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-heading: {{VALUE}};' ],
		] );
		$this->add_control( 'text_color', [
			'label' => esc_html__( 'Text color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-text: {{VALUE}};' ],
		] );
		$this->add_control( 'field_border', [
			'label' => esc_html__( 'Field border', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-field-border: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg', [
			'label' => esc_html__( 'Button background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-btn-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg_hover', [
			'label' => esc_html__( 'Button background (hover)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR,
			'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-cmv2-btn-bg-hover: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'heading_typo', 'selector' => '{{WRAPPER}} .aew-cmv2__heading',
			'fields_options' => [
				'font_family' => [ 'default' => 'Teko' ], 'font_weight' => [ 'default' => '600' ],
				'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 28 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
			],
		] );

		$this->end_controls_section();
	}

	// ── RENDER ────────────────────────────────────────────────────────────────

	private function resolve_post_id(): int {
		$id = (int) get_the_ID();
		if ( $id > 0 && post_type_supports( get_post_type( $id ), 'comments' ) ) {
			return $id;
		}
		return 0;
	}

	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$heading = (string) ( $s['heading'] ?? 'Comments' );
		$post_id = $this->resolve_post_id();

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'    => '--aew-cmv2-bg',
			'card_bg'       => '--aew-cmv2-card-bg',
			'card_border'   => '--aew-cmv2-card-border',
			'heading_color' => '--aew-cmv2-heading',
			'text_color'    => '--aew-cmv2-text',
			'field_border'  => '--aew-cmv2-field-border',
			'btn_bg'        => '--aew-cmv2-btn-bg',
			'btn_bg_hover'  => '--aew-cmv2-btn-bg-hover',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		// In the editor (no real single-post context) show a static preview.
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		$cfg = wp_json_encode( [
			'postUrl'     => $post_id ? get_permalink( $post_id ) : '',
			'moderation'  => (bool) get_option( 'comment_moderation' ),
			'pending'     => esc_html__( 'Thanks! Your comment is awaiting moderation.', 'agency-elementor-widgets' ),
			'posting'     => esc_html__( 'Posting…', 'agency-elementor-widgets' ),
			'errorMsg'    => esc_html__( 'Something went wrong. Please try again.', 'agency-elementor-widgets' ),
		] );
		?>
		<section class="aew-cmv2" data-aew-comments-v2 data-config="<?php echo esc_attr( (string) $cfg ); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="aew-cmv2__inner">
				<div class="aew-cmv2__card" id="comments">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<h2 class="aew-cmv2__heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<div class="aew-cmv2__wp">
						<?php
						if ( $post_id > 0 && ! $is_editor ) {
							$this->render_native_comments( $post_id );
						} else {
							$this->render_preview();
						}
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Render the real WP comment list + form for the given post.
	 */
	private function render_native_comments( int $post_id ): void {
		// Prime the global $post + comment query so wp_list_comments/comment_form
		// work inside an Elementor theme template (outside the main comments.php).
		global $post, $wp_query, $withcomments;
		$saved_post  = $post;
		$saved_query = $wp_query;

		$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post );
		$withcomments = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		$comments = get_comments( [ 'post_id' => $post_id, 'status' => 'approve', 'order' => 'ASC' ] );

		if ( ! empty( $comments ) ) {
			echo '<ol class="aew-cmv2__list">';
			wp_list_comments(
				[
					'style'       => 'ol',
					'avatar_size' => 44,
					'short_ping'  => true,
				],
				$comments
			);
			echo '</ol>';
		}

		comment_form(
			[
				'class_form'           => 'aew-cmv2__form',
				'title_reply'          => '',
				'title_reply_before'   => '',
				'title_reply_after'    => '',
				// Hide the "Logged in as … Log out?" / required-fields notice line.
				'logged_in_as'         => '',
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'comment_field'        => sprintf(
					'<p class="aew-cmv2__field"><textarea id="comment" name="comment" class="aew-cmv2__textarea" rows="4" placeholder="%s" required></textarea></p>',
					esc_attr__( 'Write a comment...', 'agency-elementor-widgets' )
				),
				'label_submit'         => esc_html__( 'Post', 'agency-elementor-widgets' ),
				'class_submit'         => 'aew-cmv2__submit',
			],
			$post_id
		);

		// Restore globals.
		$post     = $saved_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$wp_query = $saved_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		wp_reset_postdata();
	}

	/**
	 * Static, non-functional preview for the Elementor editor.
	 */
	private function render_preview(): void {
		?>
		<form class="aew-cmv2__form" onsubmit="return false">
			<p class="aew-cmv2__field">
				<textarea class="aew-cmv2__textarea" rows="4" placeholder="<?php esc_attr_e( 'Write a comment...', 'agency-elementor-widgets' ); ?>"></textarea>
			</p>
			<p class="aew-cmv2__form-submit">
				<button type="button" class="aew-cmv2__submit"><?php esc_html_e( 'Post', 'agency-elementor-widgets' ); ?></button>
			</p>
		</form>
		<p class="aew-cmv2__note"><?php esc_html_e( 'Live comments appear here on the published post.', 'agency-elementor-widgets' ); ?></p>
		<?php
	}
}
