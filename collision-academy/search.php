<?php
/**
 * Collision Academy — Search Results Template
 *
 * Displays results for site search queries. Shows a results count,
 * a grid of matching post cards, and helpful suggestions if no results found.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-archive-layout">
	<div class="ca-archive-main">

		<?php if ( have_posts() ) : ?>

			<header class="ca-archive-header">
				<span class="ca-eyebrow"><?php esc_html_e( 'Search results for', 'collision-academy' ); ?></span>
				<h1 class="ca-archive-header__title">&ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>
				<p class="ca-archive-header__desc">
					<?php
					printf(
						/* translators: %d = number of results */
						esc_html( _n( '%d result found.', '%d results found.', $wp_query->found_posts, 'collision-academy' ) ),
						esc_html( number_format_i18n( $wp_query->found_posts ) )
					);
					?>
				</p>
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

			<!-- No Results State -->
			<div class="ca-no-results">
				<span class="ca-eyebrow"><?php esc_html_e( 'No results', 'collision-academy' ); ?></span>
				<h1 class="ca-no-results__title">
					<?php
					printf(
						/* translators: %s = the search query */
						esc_html__( 'Nothing found for "%s"', 'collision-academy' ),
						esc_html( get_search_query() )
					);
					?>
				</h1>
				<p class="ca-no-results__text">
					<?php esc_html_e( 'Your search did not match any articles. Try different keywords, or browse by category below.', 'collision-academy' ); ?>
				</p>

				<!-- Search Again -->
				<div class="ca-no-results__search">
					<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label for="ca-no-results-search" class="screen-reader-text"><?php esc_html_e( 'Search again', 'collision-academy' ); ?></label>
						<div class="ca-search-inline">
							<input
								type="search"
								id="ca-no-results-search"
								class="ca-search-inline__input"
								name="s"
								placeholder="<?php esc_attr_e( 'Try another search…', 'collision-academy' ); ?>"
							>
							<button type="submit" class="ca-btn ca-btn--primary"><?php esc_html_e( 'Search', 'collision-academy' ); ?></button>
						</div>
					</form>
				</div>

				<!-- Suggestions -->
				<div class="ca-no-results__suggestions">
					<h2 class="ca-no-results__suggestions-title"><?php esc_html_e( 'Browse by topic', 'collision-academy' ); ?></h2>
					<ul class="ca-no-results__cats">
						<?php
						$cats = get_categories( array( 'hide_empty' => true, 'number' => 6 ) );
						foreach ( $cats as $cat ) :
							?>
							<li>
								<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="ca-btn ca-btn--outline">
									<?php echo esc_html( $cat->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

			</div><!-- .ca-no-results -->

		<?php endif; ?>

	</div><!-- .ca-archive-main -->

	<?php get_sidebar(); ?>

</div><!-- .ca-container -->

<?php get_footer(); ?>
