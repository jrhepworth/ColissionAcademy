<?php
/**
 * Collision Academy — Sidebar Template
 *
 * Optional sidebar shown on archive and blog listing pages.
 * Enable or disable by changing the CA_SIDEBAR_ENABLED constant in functions.php.
 *
 * To add widgets to this sidebar, go to wp-admin > Appearance > Widgets
 * and add items to the "Main Sidebar" widget area.
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only render the sidebar if it's enabled and has content.
if ( ! CA_SIDEBAR_ENABLED ) {
	return;
}
?>

<aside id="ca-sidebar" class="ca-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'collision-academy' ); ?>">

	<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-main' ); ?>
	<?php else : ?>
		<!--
			No widgets have been added to the sidebar yet.
			Go to wp-admin > Appearance > Widgets to add content here.
			Common choices: Tag Cloud, Recent Posts, Search.
		-->

		<!-- Default: Tag Cloud -->
		<div class="ca-widget">
			<h3 class="ca-widget__title"><?php esc_html_e( 'Browse by Topic', 'collision-academy' ); ?></h3>
			<div class="ca-tag-cloud">
				<?php
				wp_tag_cloud( array(
					'smallest'  => 12,
					'largest'   => 18,
					'unit'      => 'px',
					'number'    => 30,
					'format'    => 'flat',
					'separator' => ' ',
					'orderby'   => 'count',
					'order'     => 'DESC',
				) );
				?>
			</div>
		</div>

		<!-- Default: Recent Posts -->
		<div class="ca-widget">
			<h3 class="ca-widget__title"><?php esc_html_e( 'Recent Articles', 'collision-academy' ); ?></h3>
			<ul class="ca-widget-posts">
				<?php
				$recent_args  = array(
					'posts_per_page' => 5,
					'post_status'    => 'publish',
				);
				$recent_query = new WP_Query( $recent_args );

				if ( $recent_query->have_posts() ) :
					while ( $recent_query->have_posts() ) :
						$recent_query->the_post();
						?>
						<li class="ca-widget-posts__item">
							<a href="<?php the_permalink(); ?>" class="ca-widget-posts__link">
								<?php the_title(); ?>
							</a>
							<span class="ca-widget-posts__date"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></span>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</ul>
		</div>

	<?php endif; ?>

</aside><!-- .ca-sidebar -->
