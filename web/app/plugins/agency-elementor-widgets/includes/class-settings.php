<?php
/**
 * Admin settings for design tokens.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

/**
 * Settings page under Settings menu.
 */
final class Settings {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_menu', [ self::class, 'add_menu' ] );
		add_action( 'admin_init', [ self::class, 'register_settings' ] );
	}

	/**
	 * @return void
	 */
	public static function add_menu(): void {
		add_options_page(
			__( 'Agency Widgets', 'agency-elementor-widgets' ),
			__( 'Agency Widgets', 'agency-elementor-widgets' ),
			'manage_options',
			'aew-settings',
			[ self::class, 'render_page' ]
		);
	}

	/**
	 * @return void
	 */
	public static function register_settings(): void {
		register_setting(
			'aew_settings_group',
			Design_Tokens::OPTION_KEY,
			[
				'type'              => 'array',
				'sanitize_callback' => [ self::class, 'sanitize_tokens' ],
				'default'           => Design_Tokens::defaults(),
			]
		);
	}

	/**
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	public static function sanitize_tokens( $input ): array {
		if ( ! is_array( $input ) ) {
			return Design_Tokens::defaults();
		}

		$clean = [];
		foreach ( Design_Tokens::defaults() as $key => $default ) {
			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}
			$value = sanitize_text_field( (string) $input[ $key ] );
			if ( str_starts_with( $key, 'color_' ) ) {
				$value = sanitize_hex_color( $value ) ?: $default;
			}
			$clean[ $key ] = $value;
		}

		return array_merge( Design_Tokens::defaults(), $clean );
	}

	/**
	 * @return void
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tokens = Design_Tokens::get();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Agency Widgets — Design Tokens', 'agency-elementor-widgets' ); ?></h1>
			<p><?php esc_html_e( 'Override colors and typography for this site. Widgets use CSS variables generated from these values.', 'agency-elementor-widgets' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'aew_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( self::fields() as $key => $label ) : ?>
						<tr>
							<th scope="row"><label for="aew-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<input
									id="aew-<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( Design_Tokens::OPTION_KEY . '[' . $key . ']' ); ?>"
									type="text"
									class="regular-text"
									value="<?php echo esc_attr( $tokens[ $key ] ?? '' ); ?>"
								/>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * @return array<string, string>
	 */
	private static function fields(): array {
		return [
			'color_blue_dark'   => __( 'Blue Dark', 'agency-elementor-widgets' ),
			'color_blue'        => __( 'Blue', 'agency-elementor-widgets' ),
			'color_blue_light'  => __( 'Blue Light', 'agency-elementor-widgets' ),
			'color_yellow'      => __( 'Yellow', 'agency-elementor-widgets' ),
			'color_cream'       => __( 'Light Cream', 'agency-elementor-widgets' ),
			'color_white'       => __( 'White', 'agency-elementor-widgets' ),
			'color_gray'        => __( 'Light Gray', 'agency-elementor-widgets' ),
			'font_heading'      => __( 'Heading font family', 'agency-elementor-widgets' ),
			'font_body'         => __( 'Body font family', 'agency-elementor-widgets' ),
			'text_h1_size'      => __( 'H1 size', 'agency-elementor-widgets' ),
			'text_h1_lh'        => __( 'H1 line-height', 'agency-elementor-widgets' ),
			'text_h2_size'      => __( 'H2 size', 'agency-elementor-widgets' ),
			'text_h2_lh'        => __( 'H2 line-height', 'agency-elementor-widgets' ),
			'text_h3_size'      => __( 'H3 size', 'agency-elementor-widgets' ),
			'text_h3_lh'        => __( 'H3 line-height', 'agency-elementor-widgets' ),
			'text_h4_size'      => __( 'H4 size', 'agency-elementor-widgets' ),
			'text_h4_lh'        => __( 'H4 line-height', 'agency-elementor-widgets' ),
			'text_h5_size'      => __( 'H5 size', 'agency-elementor-widgets' ),
			'text_h5_lh'        => __( 'H5 line-height', 'agency-elementor-widgets' ),
			'text_subhead_size' => __( 'Sub-head size', 'agency-elementor-widgets' ),
			'text_subhead_lh'   => __( 'Sub-head line-height', 'agency-elementor-widgets' ),
			'text_p_size'       => __( 'Paragraph size', 'agency-elementor-widgets' ),
			'text_p_lh'         => __( 'Paragraph line-height', 'agency-elementor-widgets' ),
			'text_p2_size'      => __( 'Paragraph 2 size', 'agency-elementor-widgets' ),
			'text_p2_lh'        => __( 'Paragraph 2 line-height', 'agency-elementor-widgets' ),
		];
	}
}
