<?php
/**
 * Collision Academy — SEO & Analytics
 *
 * Outputs Open Graph meta tags, Twitter Card meta tags, canonical URLs,
 * and analytics scripts in the <head> of every page.
 *
 * This file handles SEO foundations. For advanced SEO (XML sitemaps, schema
 * markup, keyword analysis), install the Yoast SEO plugin — see README for
 * instructions. This file's meta tags play nicely alongside Yoast.
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// META TAGS — OPEN GRAPH & TWITTER CARDS
// =============================================================================

/**
 * collision_academy_meta_tags()
 *
 * Outputs Open Graph (used by Facebook, LinkedIn, Slack, etc.) and
 * Twitter Card meta tags in the <head>. Also outputs the canonical URL
 * to prevent duplicate content issues.
 *
 * Hooked to wp_head with priority 5 so it runs before most plugins.
 */
function collision_academy_meta_tags() {
	global $post;

	// --- Determine page-specific values ---

	if ( is_singular() && isset( $post ) ) {
		// Single post or page.
		$title       = get_the_title( $post->ID );
		$description = collision_academy_get_description( $post );
		$image       = collision_academy_get_og_image( $post->ID );
		$url         = get_permalink( $post->ID );
		$type        = is_singular( 'post' ) ? 'article' : 'website';

	} elseif ( is_home() || is_front_page() ) {
		// Blog index or front page.
		$title       = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$image       = collision_academy_get_default_og_image();
		$url         = home_url( '/' );
		$type        = 'website';

	} elseif ( is_archive() ) {
		// Category, tag, or other archive.
		$title       = get_the_archive_title();
		$description = get_the_archive_description();
		if ( ! $description ) {
			$description = get_bloginfo( 'description' );
		}
		$image = collision_academy_get_default_og_image();
		$url   = get_the_permalink();
		$type  = 'website';

	} else {
		// Fallback for search results, 404, etc.
		$title       = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$image       = collision_academy_get_default_og_image();
		$url         = home_url( '/' );
		$type        = 'website';
	}

	// Ensure values are strings and sanitized.
	$site_name   = esc_attr( get_bloginfo( 'name' ) );
	$title       = esc_attr( wp_strip_all_tags( $title ) );
	$description = esc_attr( wp_trim_words( wp_strip_all_tags( $description ), 30 ) );
	$url         = esc_url( $url );
	$image       = esc_url( $image );

	?>
	<!-- Collision Academy: Open Graph / Social Sharing Meta Tags -->
	<meta property="og:type"        content="<?php echo esc_attr( $type ); ?>" />
	<meta property="og:site_name"   content="<?php echo $site_name; ?>" />
	<meta property="og:title"       content="<?php echo $title; ?>" />
	<meta property="og:description" content="<?php echo $description; ?>" />
	<meta property="og:url"         content="<?php echo $url; ?>" />
	<?php if ( $image ) : ?>
	<meta property="og:image"       content="<?php echo $image; ?>" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="630" />
	<?php endif; ?>

	<!-- Twitter / X Card -->
	<meta name="twitter:card"        content="summary_large_image" />
	<meta name="twitter:title"       content="<?php echo $title; ?>" />
	<meta name="twitter:description" content="<?php echo $description; ?>" />
	<?php if ( $image ) : ?>
	<meta name="twitter:image"       content="<?php echo $image; ?>" />
	<?php endif; ?>

	<!-- Description meta tag (used by Google in search results snippets) -->
	<?php if ( $description ) : ?>
	<meta name="description" content="<?php echo $description; ?>" />
	<?php endif; ?>

	<!-- Canonical URL (prevents duplicate content penalties) -->
	<link rel="canonical" href="<?php echo $url; ?>" />
	<?php

	// For single posts, also add article-specific Open Graph tags.
	if ( is_singular( 'post' ) && isset( $post ) ) {
		$published = esc_attr( get_the_date( 'c', $post->ID ) );
		$modified  = esc_attr( get_the_modified_date( 'c', $post->ID ) );
		echo '<meta property="article:published_time" content="' . $published . '" />' . "\n";
		echo '<meta property="article:modified_time" content="' . $modified . '" />' . "\n";
	}
}
add_action( 'wp_head', 'collision_academy_meta_tags', 5 );

// =============================================================================
// HELPER FUNCTIONS FOR SEO
// =============================================================================

/**
 * collision_academy_get_description()
 *
 * Generates a meta description for a post.
 * Uses the manual excerpt if set, otherwise falls back to auto-generated excerpt.
 *
 * @param WP_Post $post The post object.
 * @return string Plain-text description (no HTML).
 */
function collision_academy_get_description( $post ) {
	if ( $post->post_excerpt ) {
		return wp_strip_all_tags( $post->post_excerpt );
	}

	// Auto-generate from content.
	$content = wp_strip_all_tags( $post->post_content );
	return wp_trim_words( $content, 25 );
}

/**
 * collision_academy_get_og_image()
 *
 * Returns the URL of the image to use for Open Graph / Twitter Cards.
 * Prefers the post's featured image, falls back to a default.
 *
 * @param int $post_id Post ID.
 * @return string Image URL, or empty string if none found.
 */
function collision_academy_get_og_image( $post_id ) {
	// Use the featured image if available.
	if ( has_post_thumbnail( $post_id ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'ca-wide' );
		if ( $image ) {
			return $image[0];
		}
	}

	return collision_academy_get_default_og_image();
}

/**
 * collision_academy_get_default_og_image()
 *
 * Returns the URL of the default Open Graph image — used when a post
 * has no featured image, or for non-post pages (archives, homepage).
 *
 * TO SET YOUR DEFAULT OG IMAGE:
 * Upload a 1200×630px image to your Media Library, then add its URL to
 * the theme settings or place it at: /assets/images/og-default.jpg
 * and set that URL below.
 *
 * @return string Image URL, or empty string if not configured.
 */
function collision_academy_get_default_og_image() {
	// Check for a custom option first (can be set via theme options).
	$custom = get_option( 'collision_academy_og_image' );
	if ( $custom ) {
		return esc_url( $custom );
	}

	// Fall back to the bundled default image in the theme's assets folder.
	$default = get_template_directory_uri() . '/assets/images/og-default.jpg';

	return $default;
}

// =============================================================================
// ANALYTICS — PLAUSIBLE (DEFAULT)
// =============================================================================

/**
 * collision_academy_analytics()
 *
 * Outputs the Plausible Analytics script tag.
 *
 * WHY PLAUSIBLE?
 * Plausible Analytics is privacy-friendly by design. It does not use cookies,
 * does not track individuals across sites, and does not require a cookie
 * consent banner under UK GDPR or EU GDPR. This is ideal for a professional
 * UK publication with a legal and forensic audience.
 *
 * HOW TO ENABLE:
 * 1. Create an account at https://plausible.io
 * 2. Add your domain (collisionacademy.co.uk)
 * 3. Go to WordPress Admin > Settings > General, or add this line to
 *    wp-config.php:  define( 'CA_PLAUSIBLE_DOMAIN', 'collisionacademy.co.uk' );
 *
 * HOW TO DISABLE:
 * Set the constant to an empty string or remove it from wp-config.php.
 *
 * GOOGLE ANALYTICS 4 ALTERNATIVE:
 * See the README for instructions on adding GA4 via this same hook.
 */
function collision_academy_analytics() {
	// Get the domain from a constant (set in wp-config.php) or option.
	$domain = '';

	if ( defined( 'CA_PLAUSIBLE_DOMAIN' ) && CA_PLAUSIBLE_DOMAIN ) {
		$domain = CA_PLAUSIBLE_DOMAIN;
	} elseif ( get_option( 'collision_academy_plausible_domain' ) ) {
		$domain = get_option( 'collision_academy_plausible_domain' );
	}

	// Only output the script if a domain is configured.
	if ( ! $domain ) {
		return;
	}

	// Never load analytics when logged in as admin (keeps your own visits out of stats).
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	$domain = esc_attr( sanitize_text_field( $domain ) );
	?>
	<!-- Plausible Analytics — privacy-friendly, no cookies, no consent banner needed -->
	<script defer data-domain="<?php echo $domain; ?>" src="https://plausible.io/js/script.js"></script>
	<?php
}
add_action( 'wp_head', 'collision_academy_analytics', 99 ); // Priority 99 = near end of <head>
