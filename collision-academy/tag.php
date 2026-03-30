<?php
/**
 * Collision Academy — Tag Archive Template
 *
 * Displays a filtered list of posts with a specific tag.
 * Same layout as category.php with the tag name shown at top.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-archive-layout<?php echo CA_SIDEBAR_ENABLED ? ' ca-archive-layout--with-sidebar' : ''; ?>">
	<div class="ca-archive-main">

		<header class="ca-archive-header">
			<span class="ca-eyebrow"><?php esc_html_e( 'Tag', 'collision-academy' ); ?></span>
			<h1 class="ca-archive-header__title"><?php echo esc_html( single_tag_title( '', false ) ); ?></h1>
			<?php
			$tag_description = tag_description();
			if ( $tag_description ) :
				echo '<div class="ca-archive-header__desc">' . wp_kses_post( $tag_description ) . '</div>';
			endif;
			?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="ca-post-grid">
				<?php while ( have_posts() ) { the_post(); get_template_part( 'template-parts/card-post' ); } ?>
			</div>
			<?php collision_academy_pagination(); ?>
		<?php else : ?>
			<div class="ca-no-results">
				<p><?php esc_html_e( 'No articles found with this tag.', 'collision-academy' ); ?></p>
				<a href="<?php echo esc_url( collision_academy_get_articles_url() ); ?>" class="ca-btn ca-btn--secondary">
					<span aria-hidden="true">&larr;</span>
					<?php esc_html_e( 'Back to all articles', 'collision-academy' ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
