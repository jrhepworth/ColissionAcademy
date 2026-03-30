<?php
/**
 * Collision Academy — Fallback Template
 *
 * WordPress's ultimate fallback. This file renders if no other template
 * in the hierarchy matches the current request. In a complete theme like
 * this one, you should rarely (if ever) see this template in use.
 *
 * Template hierarchy reference: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-archive-layout">
	<div class="ca-archive-main">

		<?php if ( have_posts() ) : ?>

			<header class="ca-archive-header">
				<h1 class="ca-archive-header__title">
					<?php
					if ( is_home() ) {
						esc_html_e( 'Latest Articles', 'collision-academy' );
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						esc_html_e( 'Articles', 'collision-academy' );
					}
					?>
				</h1>
			</header>

			<div class="ca-post-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card-post' );
				}
				?>
			</div>

			<?php collision_academy_pagination(); ?>

		<?php else : ?>

			<div class="ca-no-results">
				<h1 class="ca-no-results__title"><?php esc_html_e( 'Nothing found', 'collision-academy' ); ?></h1>
				<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Try searching below.', 'collision-academy' ); ?></p>
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</div><!-- .ca-archive-main -->

	<?php get_sidebar(); ?>

</div><!-- .ca-container -->

<?php get_footer(); ?>
