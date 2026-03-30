<?php
/**
 * Collision Academy — 404 Not Found Template
 *
 * Shown when WordPress cannot find the page or post being requested.
 * Provides helpful navigation back to key areas rather than a dead end.
 *
 * @package CollisionAcademy
 */

get_header();
?>

<div class="ca-container ca-404-layout">
	<div class="ca-404">

		<div class="ca-404__content">
			<span class="ca-404__code" aria-hidden="true">404</span>
			<h1 class="ca-404__title"><?php esc_html_e( 'Page not found', 'collision-academy' ); ?></h1>
			<p class="ca-404__text">
				<?php esc_html_e( 'The page you are looking for may have been moved, renamed, or is temporarily unavailable. Use the search below or return to the homepage.', 'collision-academy' ); ?>
			</p>

			<!-- Search Form -->
			<form role="search" method="get" class="ca-404__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="ca-404-search" class="screen-reader-text"><?php esc_html_e( 'Search for articles', 'collision-academy' ); ?></label>
				<div class="ca-search-inline">
					<input
						type="search"
						id="ca-404-search"
						class="ca-search-inline__input"
						name="s"
						placeholder="<?php esc_attr_e( 'Search articles…', 'collision-academy' ); ?>"
					>
					<button type="submit" class="ca-btn ca-btn--primary"><?php esc_html_e( 'Search', 'collision-academy' ); ?></button>
				</div>
			</form>

			<!-- Navigation Options -->
			<nav class="ca-404__nav" aria-label="<?php esc_attr_e( 'Helpful links', 'collision-academy' ); ?>">
				<ul class="ca-404__nav-list">
					<li>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-btn ca-btn--secondary">
							<?php esc_html_e( '&larr; Return to Home', 'collision-academy' ); ?>
						</a>
					</li>
					<?php
					// Show links to categories with posts.
					$cats = get_categories( array( 'hide_empty' => true, 'number' => 4 ) );
					foreach ( $cats as $cat ) :
						?>
						<li>
							<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="ca-btn ca-btn--outline">
								<?php echo esc_html( $cat->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>

	</div>
</div>

<?php get_footer(); ?>
