<?php
/**
 * Collision Academy — Footer Template
 *
 * Closes the <main> element opened in header.php, then outputs the site footer
 * with navigation, newsletter CTA, and copyright notice.
 * Must call wp_footer() before </body> — plugins depend on this.
 *
 * @package CollisionAcademy
 */
?>

</main><!-- #ca-main -->

<footer id="ca-footer" class="ca-footer" role="contentinfo">

	<!-- Newsletter CTA Strip -->
	<div class="ca-footer-nl">
		<div class="ca-container ca-footer-nl__inner">
			<div class="ca-footer-nl__text">
				<h2 class="ca-footer-nl__title"><?php esc_html_e( 'Stay informed.', 'collision-academy' ); ?></h2>
				<p class="ca-footer-nl__sub"><?php esc_html_e( 'Forensic analysis, vehicle technology, and expert insight — delivered to your inbox.', 'collision-academy' ); ?></p>
			</div>
			<?php get_template_part( 'template-parts/newsletter-form' ); ?>
		</div>
	</div>

	<!-- Footer Main -->
	<div class="ca-footer-main">
		<div class="ca-container ca-footer-main__inner">

			<!-- Brand -->
			<div class="ca-footer-brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-footer-logo" rel="home">
					<span class="ca-footer-logo__name"><?php bloginfo( 'name' ); ?></span>
					<span class="ca-footer-logo__tagline"><?php bloginfo( 'description' ); ?></span>
				</a>
				<p class="ca-footer-brand__strapline">
					<?php esc_html_e( 'A professional publication covering forensic collision investigation, vehicle technology, and expert witness practice.', 'collision-academy' ); ?>
				</p>
			</div>

			<!-- Footer Navigation -->
			<nav class="ca-footer-nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'collision-academy' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'menu_class'     => 'ca-footer-nav__list',
					'container'      => false,
					'depth'          => 1, // Footer menu is single-level only.
					'fallback_cb'    => false, // Don't show anything if no menu assigned.
				) );
				?>
			</nav>

		</div><!-- .ca-footer-main__inner -->
	</div>

	<!-- Footer Bottom Bar -->
	<div class="ca-footer-bottom">
		<div class="ca-container ca-footer-bottom__inner">
			<p class="ca-footer-bottom__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>.
				<?php esc_html_e( 'All rights reserved.', 'collision-academy' ); ?>
			</p>
			<p class="ca-footer-bottom__legal">
				<?php
				// Link to Privacy Policy page if one has been set in Settings > Privacy.
				$privacy_url = get_privacy_policy_url();
				if ( $privacy_url ) :
					?>
					<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'collision-academy' ); ?></a>
					<span aria-hidden="true"> &middot; </span>
				<?php endif; ?>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'collision-academy' ); ?></a>
			</p>
		</div>
	</div>

</footer><!-- .ca-footer -->

<?php
/*
 * wp_footer() is mandatory — it outputs:
 * - Enqueued JavaScript files (including our main.js)
 * - Inline scripts from plugins
 * - Localised script data (our caConfig object)
 * - Any other content hooked to wp_footer
 *
 * NEVER remove this line.
 */
wp_footer();
?>

</body>
</html>
