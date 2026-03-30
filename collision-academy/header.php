<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--
		Google Fonts Preconnect
		These two lines establish early connections to Google's font servers,
		reducing the time it takes to load our fonts (Inter + Source Serif 4).
		They must appear BEFORE the Google Fonts stylesheet link (which
		wp_enqueue_style() will output via wp_head below).
	-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<?php
	/*
	 * wp_head() is the critical WordPress hook that outputs:
	 * - Enqueued stylesheets (including our fonts + main.css)
	 * - Enqueued scripts (where applicable)
	 * - Meta tags from plugins and theme (our SEO tags from seo.php)
	 * - Any other content hooked to wp_head
	 *
	 * NEVER remove this — plugins depend on it for essential functionality.
	 */
	wp_head();
	?>
</head>

<body <?php body_class(); ?>>

<?php
/*
 * wp_body_open() fires the 'wp_body_open' action, used by plugins
 * that need to inject content immediately after <body>.
 * Required since WordPress 5.2.
 */
wp_body_open();
?>

<!--
	Reading Progress Bar
	Only visible on single article pages (CSS hides it everywhere else).
	JavaScript in main.js sets its width based on scroll position.
-->
<div id="ca-progress-bar" aria-hidden="true"></div>

<!-- Skip to content link — for keyboard and screen reader users -->
<a class="ca-skip-link screen-reader-text" href="#ca-main">
	<?php esc_html_e( 'Skip to content', 'collision-academy' ); ?>
</a>

<header id="ca-header" class="ca-header" role="banner">
	<div class="ca-container ca-header__inner">

		<!-- Logo / Site Identity -->
		<div class="ca-header__logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-logo-text" rel="home">
					<span class="ca-logo-text__name"><?php bloginfo( 'name' ); ?></span>
					<span class="ca-logo-text__tagline"><?php bloginfo( 'description' ); ?></span>
				</a>
			<?php endif; ?>
		</div><!-- .ca-header__logo -->

		<!-- Primary Navigation -->
		<nav id="ca-primary-nav" class="ca-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'collision-academy' ); ?>">
			<?php
			/*
			 * wp_nav_menu() outputs the navigation menu assigned to the
			 * 'primary' location in wp-admin > Appearance > Menus.
			 * If no menu is assigned, it falls back to listing all pages.
			 */
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'ca-nav__list',
				'container'      => false,
				'fallback_cb'    => 'collision_academy_nav_fallback',
			) );
			?>
		</nav><!-- .ca-nav -->

		<!-- Header Actions (Search + Mobile Toggle) -->
		<div class="ca-header__actions">

			<!-- Expandable Search -->
			<div class="ca-search-wrap" role="search">
				<button
					id="ca-search-toggle"
					class="ca-search-toggle"
					aria-expanded="false"
					aria-controls="ca-search-form"
					aria-label="<?php esc_attr_e( 'Open search', 'collision-academy' ); ?>"
				>
					<!-- Search icon (inline SVG — no external request needed) -->
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
						<path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16ZM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>

				<form
					id="ca-search-form"
					class="ca-search-form"
					method="get"
					action="<?php echo esc_url( home_url( '/' ) ); ?>"
					aria-hidden="true"
				>
					<label for="ca-search-input" class="screen-reader-text">
						<?php esc_html_e( 'Search articles', 'collision-academy' ); ?>
					</label>
					<input
						type="search"
						id="ca-search-input"
						class="ca-search-form__input"
						name="s"
						placeholder="<?php esc_attr_e( 'Search articles…', 'collision-academy' ); ?>"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						tabindex="-1"
					>
					<button type="submit" class="ca-search-form__submit" aria-label="<?php esc_attr_e( 'Submit search', 'collision-academy' ); ?>">
						<svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
							<path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16ZM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
				</form>
			</div><!-- .ca-search-wrap -->

			<!-- Hamburger Menu Toggle (visible on mobile only) -->
			<button
				id="ca-nav-toggle"
				class="ca-nav-toggle"
				aria-expanded="false"
				aria-controls="ca-primary-nav"
				aria-label="<?php esc_attr_e( 'Open navigation menu', 'collision-academy' ); ?>"
			>
				<!-- Three-line hamburger icon -->
				<span class="ca-nav-toggle__bar"></span>
				<span class="ca-nav-toggle__bar"></span>
				<span class="ca-nav-toggle__bar"></span>
			</button>

		</div><!-- .ca-header__actions -->

	</div><!-- .ca-header__inner -->
</header><!-- .ca-header -->

<main id="ca-main" class="ca-main" tabindex="-1">
<?php
// Note: </main> is closed in footer.php

/**
 * collision_academy_nav_fallback()
 *
 * Shown when no menu has been assigned to the Primary location.
 * Lists all pages so the site is never left without navigation.
 */
function collision_academy_nav_fallback() {
	?>
	<ul class="ca-nav__list">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'collision-academy' ); ?></a></li>
		<?php
		wp_list_pages( array(
			'title_li' => '',
			'echo'     => true,
		) );
		?>
	</ul>
	<?php
}
