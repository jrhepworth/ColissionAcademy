<?php
/**
 * Collision Academy — Post Card Template Part
 *
 * Reusable article card used on: archive, front-page, search, related posts.
 * Called via: get_template_part( 'template-parts/card-post' )
 *
 * Displays: featured image, category tags, title, excerpt, author, date, read time.
 * All images use native lazy loading (loading="lazy") for performance.
 *
 * @package CollisionAcademy
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ca-card' ); ?>>

	<!-- Featured Image -->
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="ca-card__image-link" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail( 'ca-card', array(
				'class'   => 'ca-card__image',
				'loading' => 'lazy',
				'alt'     => '',  // Decorative — title below is the accessible label.
			) );
			?>
		</a>
	<?php endif; ?>

	<!-- Card Body -->
	<div class="ca-card__body">

		<!-- Category tags -->
		<?php
		$category_list = collision_academy_get_category_list();
		if ( $category_list ) :
			echo '<div class="ca-card__cats">' . $category_list . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — function returns escaped HTML
		endif;
		?>

		<!-- Title -->
		<h2 class="ca-card__title">
			<a href="<?php the_permalink(); ?>" class="ca-card__title-link">
				<?php the_title(); ?>
			</a>
		</h2>

		<!-- Excerpt -->
		<p class="ca-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

		<!-- Meta: author, date, read time -->
		<footer class="ca-card__meta">
			<span class="ca-card__author">
				<?php the_author(); ?>
			</span>
			<span class="ca-card__meta-sep" aria-hidden="true">&middot;</span>
			<time class="ca-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( 'j M Y' ) ); ?>
			</time>
			<span class="ca-card__meta-sep" aria-hidden="true">&middot;</span>
			<span class="ca-card__read-time">
				<?php echo esc_html( collision_academy_reading_time() ); ?>
			</span>
		</footer>

	</div><!-- .ca-card__body -->

</article>
