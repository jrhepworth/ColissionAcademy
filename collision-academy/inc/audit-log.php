<?php
/**
 * Collision Academy — Audit Log
 *
 * Records security-relevant events to the wp_ca_audit_log database table.
 * Given the professional and legal nature of this publication's audience,
 * lightweight audit logging provides accountability without privacy concerns.
 *
 * What is logged:
 * - Failed newsletter form submissions (honeypot, rate limit, validation)
 * - Failed contact form submissions (same reasons)
 * - Admin CSV export actions (user ID + timestamp)
 *
 * What is NOT logged:
 * - Successful form submissions (those go to wp_ca_subscribers)
 * - Page views or browsing behaviour
 * - Passwords or raw personal data
 *
 * Retention: Records are automatically purged after 90 days by a WP-Cron
 * job registered in functions.php.
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// CORE LOGGING FUNCTION
// =============================================================================

/**
 * collision_academy_log_event()
 *
 * Writes a single audit log entry to the database.
 *
 * @param string $event_type  Short identifier for the event type.
 *                            Examples: 'newsletter_fail', 'contact_fail', 'csv_export'.
 * @param string $hashed_ip   SHA-256 hash of the visitor's IP (never raw IP).
 *                            Pass an empty string if not applicable (e.g. admin actions).
 * @param string $detail      Human-readable description of what happened.
 *                            Examples: 'honeypot triggered', 'rate limit exceeded',
 *                            'invalid email format', 'export by user ID 1'.
 */
function collision_academy_log_event( $event_type, $hashed_ip = '', $detail = '' ) {
	global $wpdb;

	$table = $wpdb->prefix . 'ca_audit_log';

	$wpdb->insert(
		$table,
		array(
			'event_type' => sanitize_text_field( $event_type ),
			'hashed_ip'  => sanitize_text_field( $hashed_ip ),
			'detail'     => sanitize_text_field( $detail ),
			'created_at' => current_time( 'mysql', true ), // UTC time
		),
		array( '%s', '%s', '%s', '%s' )
	);
}

// =============================================================================
// ADMIN MENU & PAGE
// =============================================================================

/**
 * collision_academy_audit_admin_menu()
 *
 * Adds the Audit Log page to the WordPress admin sidebar.
 * It appears as a submenu under the main "Collision Academy" menu
 * (registered in newsletter.php, which loads first).
 *
 * Only users with 'manage_options' capability (Administrators) can see this.
 */
function collision_academy_audit_admin_menu() {
	add_submenu_page(
		'collision-academy',                           // Parent menu slug (from newsletter.php)
		esc_html__( 'Audit Log', 'collision-academy' ),
		esc_html__( 'Audit Log', 'collision-academy' ),
		'manage_options',
		'collision-academy-audit',
		'collision_academy_audit_admin_page'
	);
}
add_action( 'admin_menu', 'collision_academy_audit_admin_menu' );

/**
 * collision_academy_audit_admin_page()
 *
 * Renders the Audit Log admin page. Shows a paginated table of log entries
 * and provides a button to purge all records.
 */
function collision_academy_audit_admin_page() {
	// Check permissions — always verify before rendering admin pages.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'collision-academy' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'ca_audit_log';

	// --- Handle Purge Action ---
	if (
		isset( $_POST['ca_purge_audit'] ) &&
		check_admin_referer( 'ca_purge_audit_action', 'ca_purge_audit_nonce' )
	) {
		$wpdb->query( "TRUNCATE TABLE {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Audit log has been purged.', 'collision-academy' ) . '</p></div>';
	}

	// --- Fetch Records ---
	$per_page     = 25;
	$current_page = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$offset       = ( $current_page - 1 ) * $per_page;

	$total   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$records = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->prepare(
			"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
			$per_page,
			$offset
		)
	);

	$total_pages = ceil( $total / $per_page );

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Audit Log', 'collision-academy' ); ?></h1>

		<p>
			<?php
			printf(
				/* translators: 1: record count, 2: retention days */
				esc_html__( 'Showing the most recent security and admin events. %1$s total records. Records are automatically purged after %2$s days.', 'collision-academy' ),
				'<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>',
				'<strong>90</strong>'
			);
			?>
		</p>

		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Time (UTC)', 'collision-academy' ); ?></th>
					<th><?php esc_html_e( 'Event Type', 'collision-academy' ); ?></th>
					<th><?php esc_html_e( 'Hashed IP', 'collision-academy' ); ?></th>
					<th><?php esc_html_e( 'Detail', 'collision-academy' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $records ) ) : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No audit log entries found.', 'collision-academy' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $records as $record ) : ?>
						<tr>
							<td><?php echo esc_html( $record->created_at ); ?></td>
							<td><code><?php echo esc_html( $record->event_type ); ?></code></td>
							<td><code title="<?php esc_attr_e( 'SHA-256 hash — not a raw IP address', 'collision-academy' ); ?>"><?php echo esc_html( substr( $record->hashed_ip, 0, 16 ) . '…' ); ?></code></td>
							<td><?php echo esc_html( $record->detail ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php
					echo paginate_links( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'base'    => add_query_arg( 'paged', '%#%' ),
						'format'  => '',
						'current' => $current_page,
						'total'   => $total_pages,
					) );
					?>
				</div>
			</div>
		<?php endif; ?>

		<hr>
		<h2><?php esc_html_e( 'Purge Audit Log', 'collision-academy' ); ?></h2>
		<p><?php esc_html_e( 'This will permanently delete all audit log entries. Use with caution. Records are auto-purged after 90 days so manual purging is usually unnecessary.', 'collision-academy' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'ca_purge_audit_action', 'ca_purge_audit_nonce' ); ?>
			<?php submit_button( esc_html__( 'Purge All Audit Records', 'collision-academy' ), 'delete', 'ca_purge_audit', false ); ?>
		</form>
	</div>
	<?php
}
