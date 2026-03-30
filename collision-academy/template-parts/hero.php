<?php
/**
 * Collision Academy — Homepage Hero Template Part
 *
 * Full-width hero section with bold headline, subheadline, and a primary CTA.
 * Called from front-page.php via: get_template_part( 'template-parts/hero' )
 *
 * The headline and subheadline can be customised via the WordPress Customizer
 * (Appearance > Customize). If not set, defaults to the taglines below.
 *
 * @package CollisionAcademy
 */

// Allow the hero text to be customised via WordPress options.
// To set custom text, add:  update_option( 'ca_hero_headline', 'Your Headline' );
// in wp-admin, or use the Customizer (future enhancement).
$headline    = get_option( 'ca_hero_headline', '' );
$subheadline = get_option( 'ca_hero_subheadline', '' );
$cta_text    = get_option( 'ca_hero_cta_text', '' );
$cta_url     = get_option( 'ca_hero_cta_url', '' );

// Default content.
if ( ! $headline ) {
	$headline = __( 'Forensic Collision Intelligence.', 'collision-academy' );
}
if ( ! $subheadline ) {
	$subheadline = __( 'In-depth analysis of collision investigation, vehicle dynamics, and expert witness practice — written by practitioners, for practitioners.', 'collision-academy' );
}
if ( ! $cta_text ) {
	$cta_text = __( 'Explore articles', 'collision-academy' );
}
if ( ! $cta_url ) {
	$cta_url = get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/#articles' );
}
?>

<section class="ca-hero" aria-label="<?php esc_attr_e( 'Site introduction', 'collision-academy' ); ?>">
	<div class="ca-container ca-hero__inner">

		<div class="ca-hero__content">
			<span class="ca-hero__eyebrow ca-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<h1 class="ca-hero__title"><?php echo esc_html( $headline ); ?></h1>
			<p class="ca-hero__sub"><?php echo esc_html( $subheadline ); ?></p>
			<div class="ca-hero__actions">
				<a href="<?php echo esc_url( $cta_url ); ?>" class="ca-btn ca-btn--primary ca-btn--large">
					<?php echo esc_html( $cta_text ); ?>
					<span aria-hidden="true"> &darr;</span>
				</a>
			</div>
		</div>

		<!-- Decorative accent — a subtle typographic block -->
		<div class="ca-hero__accent" aria-hidden="true">
			<span class="ca-hero__accent-line">Vehicle Dynamics</span>
			<span class="ca-hero__accent-line">Crash Reconstruction</span>
			<span class="ca-hero__accent-line">Evidence &amp; Documentation</span>
			<span class="ca-hero__accent-line">Expert Witness</span>
			<span class="ca-hero__accent-line">Vehicle Technology</span>
		</div>

	</div>
</section>
