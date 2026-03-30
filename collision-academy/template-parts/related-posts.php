<?php
/**
 * Collision Academy — Related Posts Template Part
 *
 * Shown after the author bio on single posts.
 * Fetches up to 3 posts from the same primary category, excluding the current post.
 *
 * Called via: get_template_part( 'template-parts/related-posts' )
 *
 * @package CollisionAcademy
 */

// Get the current post's categories.
$current_id   = get_the_ID();
$categories   = get_the_category( $current_id );

if ( empty( $categories ) ) {
	return; // No categories — can't find related posts.
}

// Use the first (primary) category for matching.
$primary_cat_id = $categories[0]->term_id;

// Query related posts.
$related_args = array(
	'posts_per_page'      => 3,
	'post_status'         => 'publish',
	'post__not_in'        => array( $current_id ),  // Exclude the current post.
	'no_found_rows'       => true,                  // Performance: skip counting total rows.
	'ignore_sticky_posts' => true,
	'category__in'        => array( $primary_cat_id ),
	'orderby'             => 'date',
	'order'               => 'DESC',
);

$related_query = new WP_Query( $related_args );

// Don't render the section if there are no related posts.
if ( ! $related_query->have_posts() ) {
	return;
}
?>

<section class="ca-related" aria-label="<?php esc_attr_e( 'Related articles', 'collision-academy' ); ?>">
	<div class="ca-container">

		<h2 class="ca-related__title"><?php esc_html_e( 'Related Articles', 'collision-academy' ); ?></h2>

		<div class="ca-related__grid">
			<?php
			while ( $related_query->have_posts() ) {
				$related_query->the_post();
				?>
				<article class="ca-related-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="ca-related-card__image-link" tabindex="-1" aria-hidden="true">
							<?php the_post_thumbnail( 'ca-card', array(
								'class'   => 'ca-related-card__image',
								'loading' => 'lazy',
								'alt'     => '',
							) ); ?>
						</a>
					<?php endif; ?>
					<div class="ca-related-card__body">
						<div class="ca-related-card__cats">
							<?php echo collision_academy_get_category_list(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<h3 class="ca-related-card__title">
							<a href="<?php the_permalink(); ?>" class="ca-related-card__link">
								<?php the_title(); ?>
							</a>
						</h3>
						<div class="ca-related-card__meta">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date( 'j M Y' ) ); ?>
							</time>
							<span aria-hidden="true">&middot;</span>
							<span><?php echo esc_html( collision_academy_reading_time() ); ?></span>
						</div>
					</div>
				</article>
			<?php } ?>
		</div><!-- .ca-related__grid -->

	</div><!-- .ca-container -->
</section><!-- .ca-related -->

<?php
// Restore the global $post variable to the main post (current article).
// This is critical after a custom WP_Query — failure to reset can cause
// subsequent template code to display the wrong post data.
wp_reset_postdata();
