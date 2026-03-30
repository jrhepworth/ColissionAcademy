<?php
/**
 * Collision Academy — Generic Page Template
 *
 * Used for all static pages (Privacy Policy, Terms, etc.) that don't
 * have a dedicated template file. Provides a clean, readable layout
 * with the page title and content area.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-page-layout">

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ca-page-article' ); ?>>

			<header class="ca-page-header">
				<h1 class="ca-page-header__title"><?php the_title(); ?></h1>
			</header>

			<div class="ca-page-content entry-content">
				<?php
				the_content();

				wp_link_pages( array(
					'before'      => '<nav class="ca-page-links"><span class="ca-page-links__label">' . esc_html__( 'Pages:', 'collision-academy' ) . '</span>',
					'after'       => '</nav>',
					'link_before' => '<span class="ca-page-links__link">',
					'link_after'  => '</span>',
				) );
				?>
			</div>

		</article>

	<?php endwhile; ?>

</div><!-- .ca-container -->

<?php get_footer(); ?>
