<?php
/**
 * Collision Academy — Author Bio Template Part
 *
 * Shown below the article body on single posts.
 * Displays the author's avatar, display name, biographical description,
 * and a link to all their articles.
 *
 * Only shown if the author has filled in their biographical info in
 * wp-admin > Users > Your Profile > Biographical Info.
 *
 * Called via: get_template_part( 'template-parts/author-bio' )
 *
 * @package CollisionAcademy
 */

// Get the post author's ID.
$author_id = get_the_author_meta( 'ID' );
$bio       = get_the_author_meta( 'description', $author_id );

// Don't show the box if the author hasn't written a bio.
if ( ! $bio ) {
	return;
}

$author_name = get_the_author_meta( 'display_name', $author_id );
$author_url  = get_author_posts_url( $author_id );
?>

<div class="ca-author-bio" aria-label="<?php esc_attr_e( 'About the author', 'collision-academy' ); ?>">

	<!-- Avatar -->
	<div class="ca-author-bio__avatar">
		<?php echo get_avatar( $author_id, 80, '', esc_attr( $author_name ), array( 'class' => 'ca-author-bio__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="ca-author-bio__content">
		<span class="ca-eyebrow"><?php esc_html_e( 'About the author', 'collision-academy' ); ?></span>

		<a href="<?php echo esc_url( $author_url ); ?>" class="ca-author-bio__name">
			<?php echo esc_html( $author_name ); ?>
		</a>

		<p class="ca-author-bio__text"><?php echo esc_html( $bio ); ?></p>

		<a href="<?php echo esc_url( $author_url ); ?>" class="ca-author-bio__link">
			<?php
			printf(
				/* translators: %s = author display name */
				esc_html__( 'More articles by %s &rarr;', 'collision-academy' ),
				esc_html( $author_name )
			);
			?>
		</a>
	</div>

</div><!-- .ca-author-bio -->
