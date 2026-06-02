<?php
/**
 * Lead Store — backend for the self-contained Consultation Form V2 widget.
 *
 * Responsibilities:
 *   - install/maintain a custom `{$prefix}notched_leads` table
 *   - handle the public admin-ajax submission (nonce + validate → email + store)
 *   - expose a small wp-admin screen to review leads
 *
 * This is deliberately self-contained: the Consultation Form V2 widget renders
 * a real <form> that posts here, so no Elementor Pro Form is required.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

final class Lead_Store {

	private const DB_VERSION     = '1';
	private const DB_VERSION_KEY = 'aew_leads_db_version';
	private const AJAX_ACTION    = 'aew_consultation_submit';
	private const NONCE_ACTION   = 'aew_consultation_form';

	public static function init(): void {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ __CLASS__, 'handle_submit' ] );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, [ __CLASS__, 'handle_submit' ] );
		add_action( 'admin_menu', [ __CLASS__, 'register_admin_page' ] );
		// Self-heal the table if the plugin was updated without re-activation.
		add_action( 'admin_init', [ __CLASS__, 'maybe_install' ] );
	}

	public static function ajax_action(): string  { return self::AJAX_ACTION; }
	public static function nonce_action(): string { return self::NONCE_ACTION; }

	// ── Table ────────────────────────────────────────────────────────────────

	private static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'notched_leads';
	}

	public static function install(): void {
		global $wpdb;
		$table           = self::table();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			created_at DATETIME NOT NULL,
			name VARCHAR(190) NOT NULL DEFAULT '',
			email VARCHAR(190) NOT NULL DEFAULT '',
			phone VARCHAR(60) NOT NULL DEFAULT '',
			zip VARCHAR(20) NOT NULL DEFAULT '',
			message TEXT NULL,
			source_url VARCHAR(255) NOT NULL DEFAULT '',
			ip VARCHAR(45) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			KEY created_at (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( self::DB_VERSION_KEY, self::DB_VERSION );
	}

	public static function maybe_install(): void {
		if ( get_option( self::DB_VERSION_KEY ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	// ── Submission handler ─────────────────────────────────────────────────────

	public static function handle_submit(): void {
		// Verify nonce (sent as `_aew_nonce`).
		$nonce = isset( $_POST['_aew_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_aew_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed. Please reload and try again.', 'agency-elementor-widgets' ) ], 400 );
		}

		// Honeypot — bots fill hidden fields; humans never do.
		if ( ! empty( $_POST['website'] ) ) {
			wp_send_json_success( [ 'message' => self::success_message() ] ); // pretend success
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$zip     = isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$consent = ! empty( $_POST['consent'] );

		$errors = [];
		if ( '' === $name )                 { $errors[] = __( 'Please enter your name.', 'agency-elementor-widgets' ); }
		if ( '' === $email || ! is_email( $email ) ) { $errors[] = __( 'Please enter a valid email.', 'agency-elementor-widgets' ); }
		if ( '' === $phone )                { $errors[] = __( 'Please enter your phone number.', 'agency-elementor-widgets' ); }
		if ( '' === $zip )                  { $errors[] = __( 'Please enter your zip code.', 'agency-elementor-widgets' ); }
		if ( '' === $message )              { $errors[] = __( 'Please tell us a bit about your project.', 'agency-elementor-widgets' ); }
		if ( ! $consent )                   { $errors[] = __( 'Please agree to be contacted.', 'agency-elementor-widgets' ); }

		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'message' => implode( ' ', $errors ) ], 422 );
		}

		$source_url = isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : '';
		$ip         = self::client_ip();

		// Store.
		global $wpdb;
		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			self::table(),
			[
				'created_at' => current_time( 'mysql' ),
				'name'       => $name,
				'email'      => $email,
				'phone'      => $phone,
				'zip'        => $zip,
				'message'    => $message,
				'source_url' => $source_url,
				'ip'         => $ip,
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
		);

		// Email (only if a recipient is configured on the widget; passed through).
		$recipient = isset( $_POST['notify_email'] ) ? sanitize_email( wp_unslash( $_POST['notify_email'] ) ) : '';
		if ( '' !== $recipient && is_email( $recipient ) ) {
			self::send_notification( $recipient, compact( 'name', 'email', 'phone', 'zip', 'message', 'source_url' ) );
		}

		wp_send_json_success( [ 'message' => self::success_message() ] );
	}

	private static function success_message(): string {
		return __( "Thanks! We've got your details and will be in touch ASAP.", 'agency-elementor-widgets' );
	}

	private static function send_notification( string $recipient, array $lead ): void {
		$subject = sprintf( /* translators: %s site name */ __( 'New consultation request — %s', 'agency-elementor-widgets' ), get_bloginfo( 'name' ) );
		$lines   = [
			__( 'New consultation request:', 'agency-elementor-widgets' ),
			'',
			'Name: ' . $lead['name'],
			'Email: ' . $lead['email'],
			'Phone: ' . $lead['phone'],
			'Zip: ' . $lead['zip'],
			'Project: ' . $lead['message'],
			'',
			'Page: ' . $lead['source_url'],
		];
		$headers = [];
		if ( is_email( $lead['email'] ) ) {
			$headers[] = 'Reply-To: ' . $lead['name'] . ' <' . $lead['email'] . '>';
		}
		wp_mail( $recipient, $subject, implode( "\n", $lines ), $headers );
	}

	private static function client_ip(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return substr( $ip, 0, 45 );
	}

	// ── Admin review screen ────────────────────────────────────────────────────

	public static function register_admin_page(): void {
		add_menu_page(
			__( 'Notched Leads', 'agency-elementor-widgets' ),
			__( 'Notched Leads', 'agency-elementor-widgets' ),
			'manage_options',
			'aew-notched-leads',
			[ __CLASS__, 'render_admin_page' ],
			'dashicons-email-alt',
			58
		);
	}

	public static function render_admin_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wpdb;
		$table = self::table();

		$per_page = 50;
		$paged    = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$offset   = ( $paged - 1 ) * $per_page;

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset ) ); // phpcs:ignore WordPress.DB

		echo '<div class="wrap"><h1>' . esc_html__( 'Notched Leads', 'agency-elementor-widgets' ) . '</h1>';
		echo '<p>' . sprintf( /* translators: %d count */ esc_html__( '%d total submissions.', 'agency-elementor-widgets' ), $total ) . '</p>';

		if ( empty( $rows ) ) {
			echo '<p>' . esc_html__( 'No leads yet.', 'agency-elementor-widgets' ) . '</p></div>';
			return;
		}

		echo '<table class="widefat fixed striped"><thead><tr>';
		foreach ( [ 'Date', 'Name', 'Email', 'Phone', 'Zip', 'Project', 'Page' ] as $h ) {
			echo '<th>' . esc_html( $h ) . '</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ( $rows as $r ) {
			echo '<tr>';
			echo '<td>' . esc_html( $r->created_at ) . '</td>';
			echo '<td>' . esc_html( $r->name ) . '</td>';
			echo '<td><a href="mailto:' . esc_attr( $r->email ) . '">' . esc_html( $r->email ) . '</a></td>';
			echo '<td>' . esc_html( $r->phone ) . '</td>';
			echo '<td>' . esc_html( $r->zip ) . '</td>';
			echo '<td>' . esc_html( wp_trim_words( (string) $r->message, 24 ) ) . '</td>';
			echo '<td>' . ( $r->source_url ? '<a href="' . esc_url( $r->source_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'view', 'agency-elementor-widgets' ) . '</a>' : '—' ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';

		$pages = (int) ceil( $total / $per_page );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo wp_kses_post( paginate_links( [
				'base'    => add_query_arg( 'paged', '%#%' ),
				'format'  => '',
				'current' => $paged,
				'total'   => $pages,
			] ) );
			echo '</div></div>';
		}
		echo '</div>';
	}
}
