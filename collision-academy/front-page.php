<?php
/**
 * Collision Academy — Home Page Template
 *
 * WordPress loads this template when the site's front page is set to
 * a static page (Settings > Reading > Your homepage displays: A static page).
 *
 * Sections:
 * 1. Hero — bold headline, subheadline, CTA button
 * 2. Featured Articles — up to 6 latest posts in a card grid
 * 3. About Teaser — brief mission statement with link to About page
 * 4. Categories — visual links to key topic areas
 * 5. Newsletter Signup — inline, prominent
 *
 * @package CollisionAcademy
 */

get_header();
?>

<!-- ============================================================
     SECTION 1: HERO
     ============================================================ -->
<?php get_template_part( 'template-parts/hero' ); ?>

<!-- ============================================================
     SECTION 2: FEATURED / LATEST ARTICLES
     ============================================================ -->
<section id="articles" class="ca-section ca-section--articles" aria-label="<?php esc_attr_e( 'Latest articles', 'collision-academy' ); ?>">
	<div class="ca-container">

		<header class="ca-section-header">
			<h2 class="ca-section-header__title"><?php esc_html_e( 'Latest Articles', 'collision-academy' ); ?></h2>
			<a href="<?php echo esc_url( collision_academy_get_articles_url() ); ?>" class="ca-section-header__link">
				<?php esc_html_e( 'View all articles', 'collision-academy' ); ?>
				<span aria-hidden="true"> &rarr;</span>
			</a>
		</header>

		<?php
		// Fetch the 6 most recent published posts.
		$featured_args  = array(
			'posts_per_page' => 6,
			'post_status'    => 'publish',
			'no_found_rows'  => true, // Performance: skip counting total rows when not paginating.
		);
		$featured_query = new WP_Query( $featured_args );
		?>

		<?php if ( $featured_query->have_posts() ) : ?>
			<div class="ca-post-grid ca-post-grid--home">
				<?php
				while ( $featured_query->have_posts() ) {
					$featured_query->the_post();
					get_template_part( 'template-parts/card-post' );
				}
				wp_reset_postdata(); // Restore global $post after custom query.
				?>
			</div>
		<?php else : ?>
			<p class="ca-no-content">
				<?php esc_html_e( 'No articles published yet. Check back soon.', 'collision-academy' ); ?>
			</p>
		<?php endif; ?>

	</div>
</section>

<!-- ============================================================
     SECTION 3: ABOUT TEASER
     ============================================================ -->
<section class="ca-section ca-section--about ca-section--alt" aria-label="<?php esc_attr_e( 'About Collision Academy', 'collision-academy' ); ?>">
	<div class="ca-container ca-about-teaser">
		<div class="ca-about-teaser__content">
			<span class="ca-eyebrow"><?php esc_html_e( 'About', 'collision-academy' ); ?></span>
			<h2 class="ca-about-teaser__title"><?php esc_html_e( 'Built for forensic professionals.', 'collision-academy' ); ?></h2>
			<p class="ca-about-teaser__text">
				<?php esc_html_e( 'Collision Academy is an independent publication dedicated to advancing knowledge in forensic collision investigation. We publish evidence-based analysis, technical commentary, and expert insight for collision investigators, forensic practitioners, legal professionals, and engineers.', 'collision-academy' ); ?>
			</p>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="ca-btn ca-btn--secondary">
				<?php esc_html_e( 'Learn more about us', 'collision-academy' ); ?>
			</a>
		</div>
		<div class="ca-about-teaser__visual" aria-hidden="true">
			<!-- Decorative visual block — add an image via the Customizer or hardcode src here -->
			<div class="ca-about-teaser__graphic">
				<span class="ca-about-teaser__stat">
					<strong><?php esc_html_e( 'Expert', 'collision-academy' ); ?></strong>
					<?php esc_html_e( 'Analysis', 'collision-academy' ); ?>
				</span>
				<span class="ca-about-teaser__stat">
					<strong><?php esc_html_e( 'Evidence', 'collision-academy' ); ?></strong>
					<?php esc_html_e( 'Based', 'collision-academy' ); ?>
				</span>
				<span class="ca-about-teaser__stat">
					<strong><?php esc_html_e( 'Independent', 'collision-academy' ); ?></strong>
					<?php esc_html_e( 'Publication', 'collision-academy' ); ?>
				</span>
			</div>
		</div>
	</div>
</section>

<!-- ============================================================
     SECTION 4: TOPIC CATEGORIES
     ============================================================ -->
<section class="ca-section ca-section--categories" aria-label="<?php esc_attr_e( 'Browse by topic', 'collision-academy' ); ?>">
	<div class="ca-container">

		<header class="ca-section-header">
			<h2 class="ca-section-header__title"><?php esc_html_e( 'Explore by Topic', 'collision-academy' ); ?></h2>
		</header>

		<?php
		// Fetch categories that have at least one published post.
		$cats = get_categories( array(
			'orderby'    => 'count',
			'order'      => 'DESC',
			'hide_empty' => true,
			'number'     => 6,
		) );

		if ( $cats ) :
			?>
			<ul class="ca-category-grid" role="list">
				<?php foreach ( $cats as $cat ) : ?>
					<li class="ca-category-card">
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="ca-category-card__link">
							<span class="ca-category-card__name"><?php echo esc_html( $cat->name ); ?></span>
							<span class="ca-category-card__count">
								<?php
								printf(
									/* translators: %d = number of articles */
									esc_html( _n( '%d article', '%d articles', $cat->count, 'collision-academy' ) ),
									esc_html( $cat->count )
								);
								?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>

<!-- ============================================================
     SECTION 5: NEWSLETTER SIGNUP
     ============================================================ -->
<section class="ca-section ca-section--newsletter" id="newsletter" aria-label="<?php esc_attr_e( 'Newsletter signup', 'collision-academy' ); ?>">
	<div class="ca-container ca-nl-section">
		<div class="ca-nl-section__text">
			<h2 class="ca-nl-section__title"><?php esc_html_e( 'Forensic intelligence, delivered.', 'collision-academy' ); ?></h2>
			<p class="ca-nl-section__sub"><?php esc_html_e( 'Join practitioners, investigators, and legal professionals who rely on Collision Academy for expert analysis. No spam. Unsubscribe any time.', 'collision-academy' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/newsletter-form' ); ?>
	</div>
</section>

<?php get_footer(); ?>
