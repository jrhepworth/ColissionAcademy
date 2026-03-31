<?php
/**
 * Collision Academy — Helper Functions
 *
 * Utility functions used across multiple templates and feature files.
 * All functions are prefixed with collision_academy_ to avoid naming conflicts.
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// READING TIME
// =============================================================================

/**
 * collision_academy_reading_time()
 *
 * Calculates the estimated reading time for a post based on word count.
 * Uses an average reading speed of 200 words per minute, which is
 * conservative for technical content (general text averages ~238 wpm).
 *
 * @param int $post_id The ID of the post to calculate reading time for.
 * @return string Formatted string like "8 min read".
 */
function collision_academy_reading_time( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Get the raw post content.
	$content = get_post_field( 'post_content', $post_id );

	// Strip HTML tags so we only count actual words.
	$text = wp_strip_all_tags( $content );

	// Count the words.
	$word_count = str_word_count( $text );

	// Calculate minutes at 200 words per minute, minimum 1 minute.
	$minutes = max( 1, (int) round( $word_count / 200 ) );

	// Return a translatable string.
	return sprintf(
		/* translators: %d = number of minutes */
		_n( '%d min read', '%d min read', $minutes, 'collision-academy' ),
		$minutes
	);
}

// =============================================================================
// IP HASHING — GDPR DATA MINIMISATION
// =============================================================================

/**
 * collision_academy_hash_ip()
 *
 * Creates a SHA-256 hash of the visitor's IP address combined with a
 * server-side salt. This allows us to detect repeated abuse (rate limiting)
 * without storing the raw IP address, which is personal data under UK GDPR.
 *
 * IMPORTANT: We use $_SERVER['REMOTE_ADDR'] — the actual connecting IP.
 * We deliberately do NOT use HTTP_X_FORWARDED_FOR or similar headers,
 * because these can be trivially spoofed by attackers to bypass rate limiting.
 * If you are behind a trusted reverse proxy (e.g. Cloudflare, load balancer),
 * see the README for how to configure trusted proxy IP forwarding safely.
 *
 * @return string 64-character hex SHA-256 hash.
 */
function collision_academy_hash_ip() {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	return hash( 'sha256', $ip . COLLISION_ACADEMY_IP_SALT );
}

// =============================================================================
// RATE LIMITING (SHARED BY NEWSLETTER + CONTACT FORMS)
// =============================================================================

/**
 * collision_academy_check_and_increment_rate_limit()
 *
 * Checks the rate limit for the current IP and form, then immediately
 * increments the counter — all in one call. Combining check and increment
 * minimises the TOCTOU (Time-of-Check / Time-of-Use) window that existed
 * when these were two separate functions.
 *
 * Returns true if the request should be blocked (limit already reached
 * BEFORE this attempt), false if the request should be allowed.
 *
 * The counter is stored as a WordPress transient and expires automatically.
 * The expiry is reset on each attempt (simple fixed-window, not sliding).
 *
 * @param string $form_key Short identifier for the form, e.g. 'nl' or 'cf'.
 * @param int    $limit    Maximum allowed attempts before blocking. Default 3.
 * @param int    $window   Expiry window in seconds. Default 3600 (1 hour).
 * @return bool True if limit exceeded (block the request), false if allowed.
 */
function collision_academy_check_and_increment_rate_limit( $form_key, $limit = 3, $window = HOUR_IN_SECONDS ) {
	$ip_hash   = collision_academy_hash_ip();
	$trans_key = 'ca_' . $form_key . '_ratelimit_' . $ip_hash;

	// Read the current attempt count (0 if the transient doesn't exist yet).
	$attempts = (int) get_transient( $trans_key );

	// Increment immediately — before returning — so this attempt is always
	// counted regardless of whether we allow or block the request.
	set_transient( $trans_key, $attempts + 1, $window );

	// Block if the count BEFORE this attempt was already at the limit.
	return $attempts >= $limit;
}

// =============================================================================
// TEMPLATE UTILITY FUNCTIONS
// =============================================================================

/**
 * collision_academy_get_articles_url()
 *
 * Returns the URL of the site-wide articles listing page.
 * If a dedicated Posts page is assigned in Settings > Reading, we use it.
 * Otherwise we fall back to the site's home URL.
 *
 * @return string Articles index URL.
 */
function collision_academy_get_articles_url() {
	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ( $page_for_posts ) {
		$url = get_permalink( $page_for_posts );
		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/' );
}

/**
 * collision_academy_get_category_list()
 *
 * Returns a formatted list of category links for a post.
 * Used in post cards and single post headers.
 *
 * @param int $post_id Post ID. Defaults to current post.
 * @return string HTML string of category links, or empty string if none.
 */
function collision_academy_get_category_list( $post_id = 0 ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) ) {
		return '';
	}

	$links = array();
	foreach ( $categories as $category ) {
		$links[] = sprintf(
			'<a href="%s" class="ca-category-tag">%s</a>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_html( $category->name )
		);
	}

	return implode( ' ', $links );
}

/**
 * collision_academy_posted_on()
 *
 * Outputs a formatted publication date with schema.org microdata for SEO.
 *
 * @param int $post_id Post ID. Defaults to current post.
 * @return string HTML string with date markup.
 */
function collision_academy_posted_on( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$date_iso   = get_the_date( 'c', $post_id );         // ISO 8601 for datetime attribute
	$date_human = get_the_date( 'j F Y', $post_id );     // Human-readable: "14 March 2025"

	return sprintf(
		'<time class="ca-post-date" datetime="%s">%s</time>',
		esc_attr( $date_iso ),
		esc_html( $date_human )
	);
}

/**
 * collision_academy_get_author_info()
 *
 * Returns an array of author information for the current post.
 * Used in post cards, single posts, and the author bio template part.
 *
 * @param int $post_id Post ID. Defaults to current post.
 * @return array Associative array with keys: name, url, bio, avatar.
 */
function collision_academy_get_author_info( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$author_id = get_post_field( 'post_author', $post_id );

	return array(
		'name'   => get_the_author_meta( 'display_name', $author_id ),
		'url'    => get_author_posts_url( $author_id ),
		'bio'    => get_the_author_meta( 'description', $author_id ),
		'avatar' => get_avatar( $author_id, 80, '', '', array( 'class' => 'ca-author-avatar' ) ),
	);
}

/**
 * collision_academy_pagination()
 *
 * Outputs numbered pagination links for archive pages.
 * Uses WordPress's built-in paginate_links() with custom styling.
 *
 * @param WP_Query|null $query Optional custom query object. Defaults to main query.
 */
function collision_academy_pagination( $query = null ) {
	if ( null === $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	$total_pages = $query->max_num_pages;

	if ( $total_pages <= 1 ) {
		return; // No pagination needed.
	}

	$current_page = max( 1, get_query_var( 'paged' ) );

	$links = paginate_links( array(
		'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
		'format'    => '?paged=%#%',
		'current'   => $current_page,
		'total'     => $total_pages,
		'prev_text' => '<span aria-hidden="true">&larr;</span> ' . esc_html__( 'Previous', 'collision-academy' ),
		'next_text' => esc_html__( 'Next', 'collision-academy' ) . ' <span aria-hidden="true">&rarr;</span>',
		'type'      => 'array', // Returns an array so we can wrap each link ourselves.
	) );

	if ( ! $links ) {
		return;
	}

	echo '<nav class="ca-pagination" aria-label="' . esc_attr__( 'Article pages', 'collision-academy' ) . '">';
	echo '<ul class="ca-pagination__list">';
	foreach ( $links as $link ) {
		echo '<li class="ca-pagination__item">' . $link . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — paginate_links() returns trusted HTML
	}
	echo '</ul>';
	echo '</nav>';
}
