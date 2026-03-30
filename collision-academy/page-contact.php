<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 *
 * Collision Academy — Contact Page Template
 *
 * Features a custom PHP/AJAX contact form with:
 * - Server-side validation
 * - Honeypot spam protection
 * - Rate limiting (same mechanism as newsletter — max 3/hour/IP)
 * - Audit logging of failed submissions
 * - GDPR privacy notice
 *
 * The form submits to the AJAX handler in inc/newsletter.php.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-page-contact">

	<!-- Page Header -->
	<header class="ca-page-header ca-page-header--contact">
		<span class="ca-eyebrow"><?php esc_html_e( 'Get in touch', 'collision-academy' ); ?></span>
		<h1 class="ca-page-header__title"><?php esc_html_e( 'Contact Us', 'collision-academy' ); ?></h1>
		<p class="ca-page-header__sub">
			<?php esc_html_e( 'Whether you have a technical question, a contribution to submit, or a media or legal enquiry — use the form below. We aim to respond within five working days.', 'collision-academy' ); ?>
		</p>
	</header>

	<div class="ca-contact-layout">

		<!-- Contact Form -->
		<div class="ca-contact-form-wrap">

			<!-- Success / Error message area (populated by JS) -->
			<div id="ca-contact-msg" class="ca-form-msg" role="alert" aria-live="polite"></div>

			<form
				id="ca-contact-form"
				class="ca-contact-form"
				novalidate
				aria-label="<?php esc_attr_e( 'Contact form', 'collision-academy' ); ?>"
			>
				<?php
				/*
				 * Nonce field — WordPress security token.
				 * The JS will include this in the AJAX POST request.
				 * The server verifies it in inc/newsletter.php via check_ajax_referer().
				 */
				?>
				<input type="hidden" name="ca_contact_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ca_contact_action' ) ); ?>">
				<input type="hidden" name="action" value="ca_contact_submit">

				<!--
					HONEYPOT FIELD — do not remove.
					Real users never see or fill this field (hidden via CSS).
					If this field is filled, the server treats the submission as spam.
					We use a different field name from the newsletter honeypot.
				-->
				<div class="ca-honeypot" aria-hidden="true">
					<label for="ca_website_c"><?php esc_html_e( 'Leave this empty', 'collision-academy' ); ?></label>
					<input type="text" id="ca_website_c" name="ca_website_c" autocomplete="off" tabindex="-1">
				</div>

				<!-- Name -->
				<div class="ca-form-field">
					<label for="ca-contact-name" class="ca-form-field__label">
						<?php esc_html_e( 'Full name', 'collision-academy' ); ?>
						<span class="ca-form-field__required" aria-hidden="true">*</span>
					</label>
					<input
						type="text"
						id="ca-contact-name"
						name="ca_name"
						class="ca-form-field__input"
						autocomplete="name"
						required
						aria-required="true"
					>
					<span class="ca-form-field__error" id="ca-contact-name-error" role="alert"></span>
				</div>

				<!-- Email -->
				<div class="ca-form-field">
					<label for="ca-contact-email" class="ca-form-field__label">
						<?php esc_html_e( 'Email address', 'collision-academy' ); ?>
						<span class="ca-form-field__required" aria-hidden="true">*</span>
					</label>
					<input
						type="email"
						id="ca-contact-email"
						name="ca_email"
						class="ca-form-field__input"
						autocomplete="email"
						required
						aria-required="true"
					>
					<span class="ca-form-field__error" id="ca-contact-email-error" role="alert"></span>
				</div>

				<!-- Organisation -->
				<div class="ca-form-field">
					<label for="ca-contact-org" class="ca-form-field__label">
						<?php esc_html_e( 'Organisation', 'collision-academy' ); ?>
						<span class="ca-form-field__optional"><?php esc_html_e( '(optional)', 'collision-academy' ); ?></span>
					</label>
					<input
						type="text"
						id="ca-contact-org"
						name="ca_organisation"
						class="ca-form-field__input"
						autocomplete="organization"
					>
				</div>

				<!-- Subject Dropdown -->
				<div class="ca-form-field">
					<label for="ca-contact-subject" class="ca-form-field__label">
						<?php esc_html_e( 'Subject', 'collision-academy' ); ?>
						<span class="ca-form-field__required" aria-hidden="true">*</span>
					</label>
					<select
						id="ca-contact-subject"
						name="ca_subject"
						class="ca-form-field__select"
						required
						aria-required="true"
					>
						<option value=""><?php esc_html_e( '— Please select —', 'collision-academy' ); ?></option>
						<option value="general"><?php esc_html_e( 'General Enquiry', 'collision-academy' ); ?></option>
						<option value="editorial"><?php esc_html_e( 'Editorial / Article Submission', 'collision-academy' ); ?></option>
						<option value="expert"><?php esc_html_e( 'Expert Witness Enquiry', 'collision-academy' ); ?></option>
						<option value="technical"><?php esc_html_e( 'Technical Question', 'collision-academy' ); ?></option>
						<option value="advertising"><?php esc_html_e( 'Advertising / Partnership', 'collision-academy' ); ?></option>
						<option value="other"><?php esc_html_e( 'Other', 'collision-academy' ); ?></option>
					</select>
				</div>

				<!-- Message -->
				<div class="ca-form-field">
					<label for="ca-contact-message" class="ca-form-field__label">
						<?php esc_html_e( 'Message', 'collision-academy' ); ?>
						<span class="ca-form-field__required" aria-hidden="true">*</span>
					</label>
					<textarea
						id="ca-contact-message"
						name="ca_message"
						class="ca-form-field__textarea"
						rows="7"
						required
						aria-required="true"
					></textarea>
					<span class="ca-form-field__error" id="ca-contact-message-error" role="alert"></span>
				</div>

				<!-- GDPR Notice -->
				<p class="ca-form-gdpr">
					<?php
					printf(
						/* translators: %s = link to privacy policy */
						esc_html__( 'By submitting this form, you consent to us processing your information to respond to your enquiry. See our %s for details.', 'collision-academy' ),
						'<a href="' . esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'collision-academy' ) . '</a>'
					);
					?>
				</p>

				<!-- Submit -->
				<button
					type="submit"
					class="ca-btn ca-btn--primary"
					data-loading="<?php esc_attr_e( 'Sending…', 'collision-academy' ); ?>"
					data-original="<?php esc_attr_e( 'Send message', 'collision-academy' ); ?>"
				>
					<?php esc_html_e( 'Send message', 'collision-academy' ); ?>
				</button>

			</form>

		</div><!-- .ca-contact-form-wrap -->

		<!-- Contact Info Sidebar -->
		<aside class="ca-contact-info" aria-label="<?php esc_attr_e( 'Contact information', 'collision-academy' ); ?>">
			<div class="ca-contact-info__block">
				<h2 class="ca-contact-info__title"><?php esc_html_e( 'Response times', 'collision-academy' ); ?></h2>
				<p><?php esc_html_e( 'We aim to respond to all enquiries within five working days. For urgent legal matters, please indicate this in your subject line.', 'collision-academy' ); ?></p>
			</div>

			<div class="ca-contact-info__block">
				<h2 class="ca-contact-info__title"><?php esc_html_e( 'Editorial submissions', 'collision-academy' ); ?></h2>
				<p><?php esc_html_e( 'We welcome contributions from practitioners, academics, and legal professionals. Articles should be evidence-based, technically accurate, and follow our editorial guidelines.', 'collision-academy' ); ?></p>
			</div>

			<div class="ca-contact-info__block">
				<h2 class="ca-contact-info__title"><?php esc_html_e( 'Expert witness enquiries', 'collision-academy' ); ?></h2>
				<p><?php esc_html_e( 'Solicitors and legal teams seeking expert witness services should select the relevant subject option and provide brief case details. We operate under Civil Procedure Rules Part 35 and the Criminal Procedure Rules.', 'collision-academy' ); ?></p>
			</div>
		</aside>

	</div><!-- .ca-contact-layout -->

</div><!-- .ca-container -->

<?php get_footer(); ?>
