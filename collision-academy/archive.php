<?php
/**
 * Collision Academy — Archive / Blog Listing Template
 *
 * Displays the blog index (all posts) and serves as the base for
 * category.php and tag.php (which extend this layout).
 *
 * Features:
 * - Category filter navigation bar
 * - Post card grid using card-post.php template part
 * - Optional sidebar (controlled by CA_SIDEBAR_ENABLED constant)
 * - Numbered pagination
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-archive-layout<?php echo CA_SIDEBAR_ENABLED ? ' ca-archive-layout--with-sidebar' : ''; ?>">

	<div class="ca-archive-main">

		<!-- Archive Header -->
		<header class="ca-archive-header">
			<?php if ( is_home() && ! is_front_page() ) : ?>
				<h1 class="ca-archive-header__title"><?php esc_html_e( 'Articles', 'collision-academy' ); ?></h1>
				<p class="ca-archive-header__sub"><?php esc_html_e( 'In-depth analysis of forensic collision investigation, vehicle dynamics, and expert witness practice.', 'collision-academy' ); ?></p>
			<?php elseif ( is_archive() ) : ?>
				<?php the_archive_title( '<h1 class="ca-archive-header__title">', '</h1>' ); ?>
				<?php the_archive_description( '<p class="ca-archive-header__desc">', '</p>' ); ?>
			<?php endif; ?>
		</header>

		<!-- Category Filter Navigation -->
		<nav class="ca-category-filter" aria-label="<?php esc_attr_e( 'Filter by category', 'collision-academy' ); ?>">
			<ul class="ca-category-filter__list">
				<li class="ca-category-filter__item<?php echo ( ! is_category() ) ? ' is-active' : ''; ?>">
					<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>" class="ca-category-filter__link">
						<?php esc_html_e( 'All', 'collision-academy' ); ?>
					</a>
				</li>
				<?php
				// List all categories that have published posts.
				$cats = get_categories( array(
					'orderby'    => 'count',
					'order'      => 'DESC',
					'hide_empty' => true,
					'number'     => 10,
				) );

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
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card-post' );
				}
				?>
			</div>

			<!-- Numbered Pagination -->
			<?php collision_academy_pagination(); ?>

		<?php else : ?>

			<div class="ca-no-results">
				<p class="ca-no-results__title"><?php esc_html_e( 'No articles found in this category.', 'collision-academy' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-btn ca-btn--secondary">
					<?php esc_html_e( '&larr; Back to all articles', 'collision-academy' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</div><!-- .ca-archive-main -->

	<?php get_sidebar(); ?>

</div><!-- .ca-container -->

<?php get_footer(); ?>
