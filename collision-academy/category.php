<?php
/**
 * Collision Academy — Category Archive Template
 *
 * Displays a filtered list of posts in a specific category.
 * Shows the category name and description at the top.
 *
 * Extends the same layout as archive.php.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-archive-layout<?php echo CA_SIDEBAR_ENABLED ? ' ca-archive-layout--with-sidebar' : ''; ?>">
	<div class="ca-archive-main">

		<!-- Category Header -->
		<header class="ca-archive-header">
			<span class="ca-eyebrow"><?php esc_html_e( 'Category', 'collision-academy' ); ?></span>
			<?php single_cat_title( '<h1 class="ca-archive-header__title">', '</h1>' ); ?>
			<?php
			$cat_description = category_description();
			if ( $cat_description ) :
				echo '<div class="ca-archive-header__desc">' . wp_kses_post( $cat_description ) . '</div>';
			endif;
			?>
		</header>

		<!-- Category Filter Navigation -->
		<nav class="ca-category-filter" aria-label="<?php esc_attr_e( 'Filter by category', 'collision-academy' ); ?>">
			<ul class="ca-category-filter__list">
				<li class="ca-category-filter__item">
					<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>" class="ca-category-filter__link">
						<?php esc_html_e( 'All', 'collision-academy' ); ?>
					</a>
				</li>
				<?php
				$cats = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true, 'number' => 10 ) );
				foreach ( $cats as $cat ) :
					$is_active = is_category( $cat->term_id );
					?>
					<li class="ca-category-filter__item<?php echo $is_active ? ' is-active' : ''; ?>">
						<a
							href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
							class="ca-category-filter__link"
							<?php echo $is_active ? 'aria-current="page"' : ''; ?>
						>
							<?php echo esc_html( $cat->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<!-- Post Grid -->
		<?php if ( have_posts() ) : ?>
			<div class="ca-post-grid">
				<?php while ( have_posts() ) { the_post(); get_template_part( 'template-parts/card-post' ); } ?>
			</div>
			<?php collision_academy_pagination(); ?>
		<?php else : ?>
			<div class="ca-no-results">
				<p><?php esc_html_e( 'No articles found in this category.', 'collision-academy' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-btn ca-btn--secondary"><?php esc_html_e( '&larr; Back to all articles', 'collision-academy' ); ?></a>
			</div>
		<?php endif; ?>

	</div><!-- .ca-archive-main -->
	<?php get_sidebar(); ?>
</div><!-- .ca-container -->

<?php get_footer(); ?>
