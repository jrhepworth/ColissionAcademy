<?php
/**
 * Collision Academy — Newsletter Signup Form Template Part
 *
 * Reusable newsletter form used in:
 * - Footer (footer.php)
 * - Homepage newsletter section (front-page.php)
 * - About page CTA section (page-about.php)
 *
 * This form is intentionally kept in its own template part so it can be
 * easily swapped for a Mailchimp/ConvertKit embed in the future with
 * minimal code changes — just replace the content of this file.
 *
 * The form submits via AJAX to the handler in inc/newsletter.php.
 *
 * Called via: get_template_part( 'template-parts/newsletter-form' )
 *
 * @package CollisionAcademy
 */

$newsletter_id = wp_unique_id( 'ca-nl-' );
?>

<div class="ca-nl-form-wrap">

	<!-- Success / error message area (populated by JS) -->
	<div class="ca-nl-msg" role="alert" aria-live="polite"></div>

	<form
		class="ca-nl-form"
		novalidate
		aria-label="<?php esc_attr_e( 'Newsletter signup', 'collision-academy' ); ?>"
	>
		<!--
			Nonce — WordPress security token. Verified server-side.
			Using wp_create_nonce() here rather than wp_nonce_field()
			so we can control the exact markup.
		-->
		<input type="hidden" name="ca_nl_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ca_newsletter_action' ) ); ?>">
		<input type="hidden" name="action" value="ca_newsletter_signup">

		<!--
			HONEYPOT FIELD — do not remove.
			Hidden from real users via CSS (.ca-honeypot { position: absolute; left: -9999px; opacity: 0; }).
			If a bot fills this field, the server silently discards the submission.
		-->
		<div class="ca-honeypot" aria-hidden="true">
			<label for="<?php echo esc_attr( $newsletter_id ); ?>-website"><?php esc_html_e( 'Leave this empty', 'collision-academy' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $newsletter_id ); ?>-website" name="ca_website" autocomplete="off" tabindex="-1">
		</div>

		<!-- Name Field -->
		<div class="ca-nl-form__field">
			<label for="<?php echo esc_attr( $newsletter_id ); ?>-name" class="ca-nl-form__label">
				<?php esc_html_e( 'First name', 'collision-academy' ); ?>
			</label>
			<input
				type="text"
				id="<?php echo esc_attr( $newsletter_id ); ?>-name"
				name="ca_name"
				class="ca-nl-form__input"
				placeholder="<?php esc_attr_e( 'Your name', 'collision-academy' ); ?>"
				autocomplete="given-name"
				required
				aria-required="true"
			>
		</div>

		<!-- Email Field -->
		<div class="ca-nl-form__field">
			<label for="<?php echo esc_attr( $newsletter_id ); ?>-email" class="ca-nl-form__label">
				<?php esc_html_e( 'Email address', 'collision-academy' ); ?>
			</label>
			<input
				type="email"
				id="<?php echo esc_attr( $newsletter_id ); ?>-email"
				name="ca_email"
				class="ca-nl-form__input"
				placeholder="<?php esc_attr_e( 'your@email.com', 'collision-academy' ); ?>"
				autocomplete="email"
				required
				aria-required="true"
			>
		</div>

		<!-- Submit -->
		<button
			type="submit"
			class="ca-btn ca-btn--primary ca-nl-form__submit"
			data-loading="<?php esc_attr_e( 'Subscribing…', 'collision-academy' ); ?>"
			data-original="<?php esc_attr_e( 'Subscribe', 'collision-academy' ); ?>"
		>
			<?php esc_html_e( 'Subscribe', 'collision-academy' ); ?>
		</button>

	</form>

	<p class="ca-nl-form__privacy">
		<?php esc_html_e( 'No spam. Unsubscribe at any time.', 'collision-academy' ); ?>
		<?php
		$privacy_url = get_privacy_policy_url();
		if ( $privacy_url ) :
			echo ' <a href="' . esc_url( $privacy_url ) . '">' . esc_html__( 'Privacy Policy', 'collision-academy' ) . '</a>.';
		endif;
		?>
	</p>

</div><!-- .ca-nl-form-wrap -->
