<?php
/**
 * Collision Academy — Single Post Template
 *
 * The most feature-rich template in the theme. Renders a full article with:
 * - Reading progress bar (CSS/JS, controlled via body class)
 * - Full-width featured image hero
 * - Article metadata: author, date, category, reading time
 * - Long-form article body with styled typography
 * - Social share buttons (Twitter/X, LinkedIn, copy link — no external JS)
 * - Author bio box
 * - Related articles (same category)
 * - Native WordPress comments
 *
 * @package CollisionAcademy
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ca-single-article' ); ?>>

		<!-- Article Hero — Featured Image -->
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="ca-article-hero">
				<?php
				the_post_thumbnail( 'ca-hero', array(
					'class'   => 'ca-article-hero__img',
					'loading' => 'eager', // Above the fold — load immediately, not lazily.
					'alt'     => esc_attr( get_the_title() ),
				) );
				?>
			</div>
		<?php endif; ?>

		<!-- Article Content Wrapper -->
		<div class="ca-container">
			<div class="ca-article-body">

				<!-- Article Header -->
				<header class="ca-article-header">

					<!-- Categories -->
					<div class="ca-article-header__cats">
						<?php echo collision_academy_get_category_list(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — function returns escaped HTML ?>
					</div>

					<!-- Title -->
					<h1 class="ca-article-header__title"><?php the_title(); ?></h1>

					<!-- Meta row: author, date, read time -->
					<div class="ca-article-meta">

						<!-- Author Avatar + Name -->
						<div class="ca-article-meta__author">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 36, '', '', array( 'class' => 'ca-article-meta__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="ca-article-meta__author-name">
								<?php the_author(); ?>
							</a>
						</div>

						<span class="ca-article-meta__sep" aria-hidden="true">&middot;</span>

						<!-- Publication Date -->
						<?php echo collision_academy_posted_on(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — function returns escaped HTML ?>

						<span class="ca-article-meta__sep" aria-hidden="true">&middot;</span>

						<!-- Reading Time -->
						<span class="ca-article-meta__read-time">
							<?php echo esc_html( collision_academy_reading_time() ); ?>
						</span>

					</div><!-- .ca-article-meta -->

				</header><!-- .ca-article-header -->

				<!-- Article Content -->
				<div class="ca-article-content entry-content">
					<?php
					the_content(
						sprintf(
							wp_kses(
								/* translators: %s: Name of current post */
								__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'collision-academy' ),
								array( 'span' => array( 'class' => array() ) )
							),
							wp_kses_post( get_the_title() )
						)
					);

					// If the post content has a <!--nextpage--> tag, this outputs
					// the navigation links between the pages.
					wp_link_pages( array(
						'before'      => '<nav class="ca-page-links"><span class="ca-page-links__label">' . esc_html__( 'Pages:', 'collision-academy' ) . '</span>',
						'after'       => '</nav>',
						'link_before' => '<span class="ca-page-links__link">',
						'link_after'  => '</span>',
					) );
					?>
				</div><!-- .ca-article-content -->

				<!-- Article Footer: Tags -->
				<?php
				$tags = get_the_tags();
				if ( $tags ) :
					?>
					<footer class="ca-article-footer">
						<div class="ca-article-tags">
							<span class="ca-article-tags__label"><?php esc_html_e( 'Tags:', 'collision-academy' ); ?></span>
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="ca-tag">
									<?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</footer>
				<?php endif; ?>

				<!-- Social Share Buttons -->
				<div class="ca-share" aria-label="<?php esc_attr_e( 'Share this article', 'collision-academy' ); ?>">
					<span class="ca-share__label"><?php esc_html_e( 'Share:', 'collision-academy' ); ?></span>

					<?php
					$share_url   = rawurlencode( get_permalink() );
					$share_title = rawurlencode( get_the_title() );
					?>

					<!-- Twitter / X -->
					<a
						href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
						class="ca-share__btn ca-share__btn--twitter"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="<?php esc_attr_e( 'Share on Twitter / X', 'collision-academy' ); ?>"
					>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
						</svg>
						<span class="ca-share__text"><?php esc_html_e( 'Twitter', 'collision-academy' ); ?></span>
					</a>

					<!-- LinkedIn -->
					<a
						href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>"
						class="ca-share__btn ca-share__btn--linkedin"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'collision-academy' ); ?>"
					>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
							<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
						</svg>
						<span class="ca-share__text"><?php esc_html_e( 'LinkedIn', 'collision-academy' ); ?></span>
					</a>

					<!-- Copy Link (handled by JS in main.js) -->
					<button
						class="ca-share__btn ca-share__btn--copy"
						data-url="<?php echo esc_attr( get_permalink() ); ?>"
						aria-label="<?php esc_attr_e( 'Copy article link to clipboard', 'collision-academy' ); ?>"
					>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
							<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
							<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
						</svg>
						<span class="ca-share__text"><?php esc_html_e( 'Copy link', 'collision-academy' ); ?></span>
					</button>

				</div><!-- .ca-share -->

			</div><!-- .ca-article-body -->
		</div><!-- .ca-container -->

	</article><!-- .ca-single-article -->

	<!-- Author Bio -->
	<div class="ca-container">
		<?php get_template_part( 'template-parts/author-bio' ); ?>
	</div>

	<!-- Related Articles -->
	<?php get_template_part( 'template-parts/related-posts' ); ?>

	<!-- Comments -->
	<div class="ca-container">
		<div class="ca-article-body">
			<?php
			// comments_template() loads comments.php if comments are open
			// or if there are existing comments on the post.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
