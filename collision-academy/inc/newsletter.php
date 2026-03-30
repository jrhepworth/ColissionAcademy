<?php
/**
 * Collision Academy — Newsletter & Contact Form Handling
 *
 * This file handles:
 * 1. Newsletter signup AJAX handler (stores to wp_ca_subscribers)
 * 2. Contact form AJAX handler (sends email via wp_mail)
 * 3. Admin menu setup for the Collision Academy section
 * 4. Admin subscriber list page with CSV export
 *
 * Security measures applied to all forms:
 * - WordPress nonce verification
 * - Honeypot field check
 * - IP-based rate limiting (max 3 attempts per hour via transients)
 * - Full server-side sanitization and validation
 * - Audit logging of failed submissions
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// ADMIN MENU
// =============================================================================

/**
 * collision_academy_admin_menu()
 *
 * Creates the main "Collision Academy" admin menu item.
 * Submenus (Subscribers, Audit Log) are added by other functions hooking
 * to the same 'admin_menu' action.
 */
function collision_academy_admin_menu() {
	// Main menu item — links directly to the subscribers page.
	add_menu_page(
		esc_html__( 'Collision Academy', 'collision-academy' ),
		esc_html__( 'Collision Academy', 'collision-academy' ),
		'manage_options',
		'collision-academy',
		'collision_academy_subscribers_page',
		'dashicons-analytics',
		25
	);

	// Explicitly add the subscribers page as a submenu so it has a proper title.
	add_submenu_page(
		'collision-academy',
		esc_html__( 'Newsletter Subscribers', 'collision-academy' ),
		esc_html__( 'Subscribers', 'collision-academy' ),
		'manage_options',
		'collision-academy',
		'collision_academy_subscribers_page'
	);
}
add_action( 'admin_menu', 'collision_academy_admin_menu' );

// =============================================================================
// ADMIN SUBSCRIBERS PAGE
// =============================================================================

/**
 * collision_academy_subscribers_page()
 *
 * Renders the newsletter subscriber list in wp-admin.
 * Displays all subscribers in a table with name, email, and signup date.
 * Provides a CSV export button.
 */
function collision_academy_subscribers_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'collision-academy' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'ca_subscribers';

	// --- Handle CSV Export ---
	if (
		isset( $_POST['ca_export_csv'] ) &&
		check_admin_referer( 'ca_export_csv_action', 'ca_export_csv_nonce' )
	) {
		// Log the export action to the audit log.
		collision_academy_log_event(
			'csv_export',
			'',
			'Exported by user ID ' . get_current_user_id()
		);

		// Fetch all subscribers.
		$all_subscribers = $wpdb->get_results( "SELECT name, email, subscribed_at FROM {$table} ORDER BY subscribed_at DESC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// Output CSV headers.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="collision-academy-subscribers-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, array( 'Name', 'Email', 'Subscribed At (UTC)' ) );

		foreach ( $all_subscribers as $row ) {
			fputcsv( $output, array( $row['name'], $row['email'], $row['subscribed_at'] ) );
		}

		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
		exit;
	}

	// --- Fetch Paginated Records ---
	$per_page     = 20;
	$current_page = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$offset       = ( $current_page - 1 ) * $per_page;

	$total       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$subscribers = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->prepare(
			"SELECT * FROM {$table} ORDER BY subscribed_at DESC LIMIT %d OFFSET %d",
			$per_page,
			$offset
		)
	);

	$total_pages = ceil( $total / $per_page );

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Newsletter Subscribers', 'collision-academy' ); ?></h1>

		<p>
			<?php
			printf(
				esc_html__( 'Total subscribers: %s', 'collision-academy' ),
				'<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>'
			);
			?>
		</p>

		<form method="post" style="display:inline-block; margin-bottom: 15px;">
			<?php wp_nonce_field( 'ca_export_csv_action', 'ca_export_csv_nonce' ); ?>
			<?php submit_button( esc_html__( 'Export as CSV', 'collision-academy' ), 'primary', 'ca_export_csv', false ); ?>
		</form>

		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'collision-academy' ); ?></th>
					<th><?php esc_html_e( 'Email', 'collision-academy' ); ?></th>
					<th><?php esc_html_e( 'Subscribed (UTC)', 'collision-academy' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $subscribers ) ) : ?>
					<tr>
						<td colspan="3"><?php esc_html_e( 'No subscribers yet.', 'collision-academy' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $subscribers as $sub ) : ?>
						<tr>
							<td><?php echo esc_html( $sub->name ); ?></td>
							<td><?php echo esc_html( $sub->email ); ?></td>
							<td><?php echo esc_html( $sub->subscribed_at ); ?></td>
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
	</div>
	<?php
}

// =============================================================================
// NEWSLETTER SIGNUP AJAX HANDLER
// =============================================================================

/**
 * collision_academy_newsletter_signup()
 *
 * Handles newsletter form submissions via AJAX.
 * Called when a POST request is made to admin-ajax.php with action=ca_newsletter_signup.
 *
 * Processing order:
 * 1. Verify nonce (CSRF protection)
 * 2. Check honeypot (bot detection — silent fake success if triggered)
 * 3. Check rate limit (max 3 attempts per IP per hour)
 * 4. Sanitize and validate inputs
 * 5. Check for duplicate email
 * 6. Insert into database
 * 7. Log event and return success response
 */
function collision_academy_newsletter_signup() {

	// Step 1: Nonce verification — protects against Cross-Site Request Forgery.
	// This will wp_die() automatically if the nonce is invalid or expired.
	check_ajax_referer( 'ca_newsletter_action', 'ca_nl_nonce' );

	$ip_hash = collision_academy_hash_ip();

	// Step 2: Honeypot check.
	// The 'ca_website' field is hidden from real users via CSS.
	// If it's filled in, it's almost certainly a bot.
	// We return a fake success to mislead the bot rather than revealing we detected it.
	if ( ! empty( $_POST['ca_website'] ) ) {
		collision_academy_log_event( 'newsletter_fail', $ip_hash, 'honeypot triggered' );
		// Return fake success — don't tell the bot it was caught.
		wp_send_json_success( array( 'message' => esc_html__( "You're subscribed! Thank you.", 'collision-academy' ) ) );
	}

	// Step 3: Rate limit check.
	if ( collision_academy_check_rate_limit( 'nl' ) ) {
		collision_academy_log_event( 'newsletter_fail', $ip_hash, 'rate limit exceeded' );
		wp_send_json_error( array(
			'message' => esc_html__( 'Too many attempts. Please wait an hour and try again.', 'collision-academy' ),
		) );
	}

	// Increment the counter BEFORE validation so failed validation attempts also count.
	// This prevents brute-force enumeration via repeated validation probing.
	collision_academy_increment_rate_limit( 'nl' );

	// Step 4: Sanitize inputs.
	$name  = sanitize_text_field( wp_unslash( isset( $_POST['ca_name'] ) ? $_POST['ca_name'] : '' ) );
	$email = sanitize_email( wp_unslash( isset( $_POST['ca_email'] ) ? $_POST['ca_email'] : '' ) );

	// Validate: name required.
	if ( empty( $name ) ) {
		collision_academy_log_event( 'newsletter_fail', $ip_hash, 'empty name field' );
		wp_send_json_error( array(
			'message' => esc_html__( 'Please enter your name.', 'collision-academy' ),
			'field'   => 'name',
		) );
	}

	// Validate: email required and valid format.
	if ( empty( $email ) || ! is_email( $email ) ) {
		collision_academy_log_event( 'newsletter_fail', $ip_hash, 'invalid email: ' . substr( $email, 0, 20 ) );
		wp_send_json_error( array(
			'message' => esc_html__( 'Please enter a valid email address.', 'collision-academy' ),
			'field'   => 'email',
		) );
	}

	// Step 5: Check for duplicate email.
	global $wpdb;
	$table = $wpdb->prefix . 'ca_subscribers';

	$existing = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s", $email ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	);

	if ( $existing ) {
		// Don't log as a failure — duplicate sign-ups are common and not malicious.
		// Return success to avoid revealing whether an email is subscribed (privacy).
		wp_send_json_success( array(
			'message' => esc_html__( "You're already subscribed. Thank you!", 'collision-academy' ),
		) );
	}

	// Step 6: Insert subscriber.
	$inserted = $wpdb->insert(
		$table,
		array(
			'name'          => $name,
			'email'         => $email,
			'subscribed_at' => current_time( 'mysql', true ), // UTC
			'ip_hash'       => $ip_hash,
		),
		array( '%s', '%s', '%s', '%s' )
	);

	if ( ! $inserted ) {
		wp_send_json_error( array(
			'message' => esc_html__( 'Something went wrong. Please try again.', 'collision-academy' ),
		) );
	}

	// Step 7: Success.
	wp_send_json_success( array(
		'message' => esc_html__( "You're subscribed! Welcome to Collision Academy.", 'collision-academy' ),
	) );
}

// =============================================================================
// CONTACT FORM AJAX HANDLER
// =============================================================================

/**
 * collision_academy_contact_submit()
 *
 * Handles contact form submissions via AJAX.
 * Validates the submission, then sends an email to the site admin.
 *
 * The same security pattern as the newsletter form:
 * nonce → honeypot → rate limit → sanitize → validate → send
 */
function collision_academy_contact_submit() {

	// Nonce verification.
	check_ajax_referer( 'ca_contact_action', 'ca_contact_nonce' );

	$ip_hash = collision_academy_hash_ip();

	// Honeypot check — separate honeypot field name for the contact form.
	if ( ! empty( $_POST['ca_website_c'] ) ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'honeypot triggered' );
		// Fake success.
		wp_send_json_success( array( 'message' => esc_html__( 'Your message has been sent. We\'ll be in touch.', 'collision-academy' ) ) );
	}

	// Rate limit check (separate key 'cf' for contact form, independent from newsletter).
	if ( collision_academy_check_rate_limit( 'cf' ) ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'rate limit exceeded' );
		wp_send_json_error( array(
			'message' => esc_html__( 'Too many attempts. Please wait an hour and try again.', 'collision-academy' ),
		) );
	}

	collision_academy_increment_rate_limit( 'cf' );

	// Sanitize all inputs.
	$name         = sanitize_text_field( wp_unslash( isset( $_POST['ca_name'] ) ? $_POST['ca_name'] : '' ) );
	$email        = sanitize_email( wp_unslash( isset( $_POST['ca_email'] ) ? $_POST['ca_email'] : '' ) );
	$organisation = sanitize_text_field( wp_unslash( isset( $_POST['ca_organisation'] ) ? $_POST['ca_organisation'] : '' ) );
	$subject_raw  = sanitize_text_field( wp_unslash( isset( $_POST['ca_subject'] ) ? $_POST['ca_subject'] : '' ) );
	$message      = sanitize_textarea_field( wp_unslash( isset( $_POST['ca_message'] ) ? $_POST['ca_message'] : '' ) );

	// Valid subject options — we validate against a whitelist to prevent injection.
	$valid_subjects = array(
		'general'      => __( 'General Enquiry', 'collision-academy' ),
		'editorial'    => __( 'Editorial / Article Submission', 'collision-academy' ),
		'expert'       => __( 'Expert Witness Enquiry', 'collision-academy' ),
		'technical'    => __( 'Technical Question', 'collision-academy' ),
		'advertising'  => __( 'Advertising / Partnership', 'collision-academy' ),
		'other'        => __( 'Other', 'collision-academy' ),
	);

	$subject_label = isset( $valid_subjects[ $subject_raw ] ) ? $valid_subjects[ $subject_raw ] : $valid_subjects['general'];

	// Validate required fields.
	if ( empty( $name ) ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'empty name' );
		wp_send_json_error( array( 'message' => esc_html__( 'Please enter your name.', 'collision-academy' ), 'field' => 'name' ) );
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'invalid email' );
		wp_send_json_error( array( 'message' => esc_html__( 'Please enter a valid email address.', 'collision-academy' ), 'field' => 'email' ) );
	}

	if ( empty( $message ) || strlen( $message ) < 10 ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'message too short' );
		wp_send_json_error( array( 'message' => esc_html__( 'Please enter a message (at least 10 characters).', 'collision-academy' ), 'field' => 'message' ) );
	}

	// Build the email.
	$to          = get_option( 'admin_email' );
	$mail_subject = sprintf(
		/* translators: 1: Subject label, 2: Sender name */
		__( '[Collision Academy] %1$s from %2$s', 'collision-academy' ),
		$subject_label,
		$name
	);

	$mail_body = sprintf(
		"Name: %s\nEmail: %s\nOrganisation: %s\nSubject: %s\n\nMessage:\n%s",
		$name,
		$email,
		$organisation ? $organisation : 'Not provided',
		$subject_label,
		$message
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $mail_subject, $mail_body, $headers );

	if ( ! $sent ) {
		collision_academy_log_event( 'contact_fail', $ip_hash, 'wp_mail failed' );
		wp_send_json_error( array(
			'message' => esc_html__( 'Your message could not be sent due to a server error. Please try again or email us directly.', 'collision-academy' ),
		) );
	}

	wp_send_json_success( array(
		'message' => esc_html__( "Thank you for your message. We'll be in touch soon.", 'collision-academy' ),
	) );
}
