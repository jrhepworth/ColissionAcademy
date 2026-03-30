<?php
/**
 * Collision Academy — Theme Functions
 *
 * This is the central nervous system of the theme. WordPress loads this file
 * automatically when the theme is active. It sets up theme support, registers
 * menus, enqueues CSS/JS, creates database tables, registers AJAX handlers,
 * and schedules maintenance tasks.
 *
 * ALL function names are prefixed with collision_academy_ to avoid conflicts
 * with WordPress core, plugins, or other themes.
 *
 * @package CollisionAcademy
 * @version 1.0.0
 */

// Prevent direct access to this file — it must only be loaded by WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// CONSTANTS
// =============================================================================

/**
 * Theme version — bump this when you update the theme to bust CSS/JS caches.
 */
define( 'COLLISION_ACADEMY_VERSION', '1.0.0' );

/**
 * IP Hashing Salt — used to anonymise IP addresses before storage (GDPR).
 *
 * HOW TO SET THIS:
 * Add the following line to your wp-config.php file (above "That's all, stop editing!"):
 *   define( 'COLLISION_ACADEMY_IP_SALT', 'your-long-random-string-here' );
 *
 * Generate a random string at: https://api.wordpress.org/secret-key/1.1/salt/
 *
 * If not defined, we fall back to WordPress's built-in auth salt, which is
 * per-install and already secret. This is acceptable but defining your own
 * is recommended for maximum separation of concerns.
 */
if ( ! defined( 'COLLISION_ACADEMY_IP_SALT' ) ) {
	define( 'COLLISION_ACADEMY_IP_SALT', wp_salt( 'auth' ) );
}

/**
 * Sidebar toggle — set to true to show the sidebar on archive/blog pages.
 * You can change this here without touching any template files.
 */
if ( ! defined( 'CA_SIDEBAR_ENABLED' ) ) {
	define( 'CA_SIDEBAR_ENABLED', false );
}

// =============================================================================
// INCLUDE FEATURE FILES
// =============================================================================

/*
 * We split functionality into separate files inside /inc/ to keep this file
 * readable. Each file is responsible for one area of the theme.
 */
require_once get_template_directory() . '/inc/helpers.php';      // Reading time, IP hash util
require_once get_template_directory() . '/inc/cpt.php';          // Custom post types
require_once get_template_directory() . '/inc/audit-log.php';    // Audit logging table + admin UI
require_once get_template_directory() . '/inc/newsletter.php';   // Newsletter signup + admin
require_once get_template_directory() . '/inc/seo.php';          // Open Graph, meta tags, analytics

// =============================================================================
// THEME SETUP
// =============================================================================

/**
 * collision_academy_setup()
 *
 * Runs once WordPress has loaded, on the 'after_setup_theme' hook.
 * Declares what WordPress features this theme supports and registers
 * navigation menu locations.
 */
function collision_academy_setup() {

	/*
	 * Make this theme's strings translatable. The text domain must match
	 * the Text Domain in style.css. Translation files go in /languages/.
	 */
	load_theme_textdomain( 'collision-academy', get_template_directory() . '/languages' );

	/*
	 * Let WordPress manage the page <title> tag automatically.
	 * Without this, you'd need to hardcode a <title> in header.php.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable featured images (post thumbnails) for posts and pages.
	 * These are the images shown in article cards and at the top of single posts.
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Register image sizes used by the theme.
	 * WordPress will create these sizes automatically when images are uploaded.
	 *
	 * Parameters: name, width (px), height (px), crop (true = hard crop)
	 */
	add_image_size( 'ca-card',   600, 400, true );   // Article card thumbnail
	add_image_size( 'ca-hero',  1600, 700, true );   // Single post hero image
	add_image_size( 'ca-wide',  1200, 600, true );   // Featured post (homepage)

	/*
	 * Enable HTML5 markup for WordPress-generated elements.
	 * This gives us cleaner, more semantic output.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	/*
	 * Add support for a custom logo in the WordPress Customizer.
	 * We define acceptable dimensions so the Customizer crops appropriately.
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	/*
	 * Enable selective refresh in the Customizer for widgets.
	 * This lets site owners preview widget changes without a full page reload.
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Register navigation menu locations.
	 * After activating the theme, go to Appearance > Menus in wp-admin
	 * to assign menus to these locations.
	 */
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Navigation', 'collision-academy' ),
		'footer'  => esc_html__( 'Footer Navigation', 'collision-academy' ),
	) );

	/*
	 * Set the content width — this tells WordPress the maximum width (in pixels)
	 * for embeds and images within the theme's content area.
	 */
	if ( ! isset( $content_width ) ) {
		$content_width = 800;
	}
}
add_action( 'after_setup_theme', 'collision_academy_setup' );

// =============================================================================
// ENQUEUE STYLES AND SCRIPTS
// =============================================================================

/**
 * collision_academy_scripts()
 *
 * Properly loads CSS and JavaScript using WordPress's enqueueing system.
 * NEVER hardcode <link> or <script> tags in header.php — this approach
 * allows plugins and other code to interact with our assets correctly.
 */
function collision_academy_scripts() {

	// --- STYLES ---

	/*
	 * Enqueue Google Fonts. We request only the weights we actually use to
	 * minimise the download size. The 'preconnect' links are in header.php.
	 *
	 * Inter: headings, UI, navigation (weights 400, 500, 600, 700)
	 * Source Serif 4: article body text (400, 600, italic 400)
	 */
	wp_enqueue_style(
		'collision-academy-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&display=swap',
		array(),
		null // null = no version, prevents WordPress appending ?ver= to Google's URL
	);

	/*
	 * Enqueue our main stylesheet. The version number ensures browsers
	 * fetch a fresh copy when you update the theme.
	 */
	wp_enqueue_style(
		'collision-academy-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'collision-academy-fonts' ), // depends on fonts being loaded first
		COLLISION_ACADEMY_VERSION
	);

	// --- SCRIPTS ---

	/*
	 * Enqueue our main JavaScript file. 'true' as the last parameter means
	 * it loads before </body> (footer), which is best practice — the HTML
	 * is parsed before JS runs, so we don't need DOMContentLoaded wrappers.
	 *
	 * We set jQuery as a dependency only if needed — for now we use vanilla JS.
	 */
	wp_enqueue_script(
		'collision-academy-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(), // no dependencies
		COLLISION_ACADEMY_VERSION,
		true // load in footer
	);

	/*
	 * Pass data from PHP to JavaScript using wp_localize_script().
	 * This is the correct way to share server-side values (like AJAX URLs
	 * and security nonces) with your JavaScript — never hardcode them.
	 *
	 * In JavaScript, access these as: window.caConfig.ajaxUrl, etc.
	 */
	wp_localize_script( 'collision-academy-main', 'caConfig', array(
		'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		'nlNonce'         => wp_create_nonce( 'ca_newsletter_action' ),
		'contactNonce'    => wp_create_nonce( 'ca_contact_action' ),
		'i18n'            => array(
			'sending'     => esc_html__( 'Sending…', 'collision-academy' ),
			'copied'      => esc_html__( 'Copied!', 'collision-academy' ),
			'copyLink'    => esc_html__( 'Copy link', 'collision-academy' ),
		),
	) );

	/*
	 * Enqueue threaded comments reply script only when needed.
	 * This is a WordPress built-in script that handles the "Reply" links
	 * in nested comment threads.
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'collision_academy_scripts' );

// =============================================================================
// DATABASE TABLE CREATION
// =============================================================================

/**
 * collision_academy_create_tables()
 *
 * Creates the custom database tables used by this theme.
 * Runs automatically when the theme is activated (after_switch_theme hook).
 *
 * We use dbDelta() — WordPress's smart table creation function. It checks
 * whether the table already exists and only creates or updates it as needed,
 * so it's safe to run multiple times without corrupting data.
 *
 * IMPORTANT DBDELTA FORMATTING RULES (these are strict — dbDelta is fussy):
 * - Two spaces before "PRIMARY KEY"
 * - No extra spaces in column definitions
 * - Each column definition must end with a comma (except the last before PRIMARY KEY)
 */
function collision_academy_create_tables() {
	global $wpdb;

	// dbDelta lives in this file — we must load it manually on the front end.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();

	// --- Table 1: Newsletter Subscribers ---
	$subscribers_table = $wpdb->prefix . 'ca_subscribers';
	$sql_subscribers   = "CREATE TABLE {$subscribers_table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(100) NOT NULL,
		email VARCHAR(200) NOT NULL,
		subscribed_at DATETIME NOT NULL,
		ip_hash VARCHAR(64) NOT NULL DEFAULT '',
  PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset_collate};";

	// --- Table 2: Audit Log ---
	// Logs failed form submissions and admin CSV exports for accountability.
	$audit_table = $wpdb->prefix . 'ca_audit_log';
	$sql_audit   = "CREATE TABLE {$audit_table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		event_type VARCHAR(50) NOT NULL,
		hashed_ip VARCHAR(64) NOT NULL DEFAULT '',
		detail TEXT,
		created_at DATETIME NOT NULL,
  PRIMARY KEY  (id),
		KEY event_type (event_type),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql_subscribers );
	dbDelta( $sql_audit );

	// Store the schema version so we can run future upgrades intelligently.
	update_option( 'collision_academy_db_version', '1.0' );
}
// Run table creation when the theme is activated.
add_action( 'after_switch_theme', 'collision_academy_create_tables' );

// =============================================================================
// WP-CRON SCHEDULED TASKS
// =============================================================================

/**
 * collision_academy_schedule_cron()
 *
 * Registers our scheduled maintenance tasks with WordPress's cron system.
 * Runs on theme activation. We check with wp_next_scheduled() to avoid
 * registering duplicate events.
 *
 * WP-Cron runs when WordPress receives a page request after the scheduled
 * time — it's not a true system cron. On low-traffic sites, consider
 * setting up a real cron job to trigger wp-cron.php. Details in README.
 */
function collision_academy_schedule_cron() {
	// Daily cleanup: purge ip_hash from subscribers older than 30 days.
	if ( ! wp_next_scheduled( 'collision_academy_purge_ip_hashes' ) ) {
		wp_schedule_event( time(), 'daily', 'collision_academy_purge_ip_hashes' );
	}

	// Daily cleanup: purge audit log entries older than 90 days.
	if ( ! wp_next_scheduled( 'collision_academy_purge_audit_log' ) ) {
		wp_schedule_event( time(), 'daily', 'collision_academy_purge_audit_log' );
	}
}
add_action( 'after_switch_theme', 'collision_academy_schedule_cron' );

/**
 * collision_academy_unschedule_cron()
 *
 * Removes our scheduled tasks when the theme is deactivated/switched.
 * Good practice — we shouldn't leave orphan cron events behind.
 */
function collision_academy_unschedule_cron() {
	$timestamp = wp_next_scheduled( 'collision_academy_purge_ip_hashes' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'collision_academy_purge_ip_hashes' );
	}

	$timestamp = wp_next_scheduled( 'collision_academy_purge_audit_log' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'collision_academy_purge_audit_log' );
	}
}
add_action( 'switch_theme', 'collision_academy_unschedule_cron' );

/**
 * collision_academy_do_purge_ip_hashes()
 *
 * GDPR data minimisation: after 30 days, the ip_hash in subscriber records
 * is no longer needed for abuse detection, so we clear it.
 * The name and email are retained until the user unsubscribes.
 */
function collision_academy_do_purge_ip_hashes() {
	global $wpdb;
	$table = $wpdb->prefix . 'ca_subscribers';
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET ip_hash = '' WHERE subscribed_at < %s AND ip_hash != ''",
			gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) )
		)
	);
}
add_action( 'collision_academy_purge_ip_hashes', 'collision_academy_do_purge_ip_hashes' );

/**
 * collision_academy_do_purge_audit_log()
 *
 * Audit log entries are retained for 90 days then automatically deleted.
 * This balances accountability with data minimisation requirements.
 */
function collision_academy_do_purge_audit_log() {
	global $wpdb;
	$table = $wpdb->prefix . 'ca_audit_log';
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$table} WHERE created_at < %s",
			gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) )
		)
	);
}
add_action( 'collision_academy_purge_audit_log', 'collision_academy_do_purge_audit_log' );

// =============================================================================
// AJAX ACTION REGISTRATION
// =============================================================================

/*
 * Register AJAX handlers for both forms.
 * 'wp_ajax_' prefix = logged-in users.
 * 'wp_ajax_nopriv_' prefix = logged-out users (visitors).
 * Both forms should work for visitors, so we register both prefixes.
 *
 * The actual handler functions live in inc/newsletter.php (newsletter)
 * and inc/newsletter.php (contact — we keep both in one file for simplicity).
 */
add_action( 'wp_ajax_ca_newsletter_signup', 'collision_academy_newsletter_signup' );
add_action( 'wp_ajax_nopriv_ca_newsletter_signup', 'collision_academy_newsletter_signup' );

add_action( 'wp_ajax_ca_contact_submit', 'collision_academy_contact_submit' );
add_action( 'wp_ajax_nopriv_ca_contact_submit', 'collision_academy_contact_submit' );

// =============================================================================
// BODY CLASSES
// =============================================================================

/**
 * collision_academy_body_classes()
 *
 * Adds custom CSS classes to the <body> tag so we can target specific
 * page types in our stylesheet without duplicating PHP logic in CSS.
 *
 * @param array $classes Existing body classes from WordPress.
 * @return array Modified class list.
 */
function collision_academy_body_classes( $classes ) {
	// Add a class to single posts so we can show the reading progress bar.
	if ( is_singular( 'post' ) ) {
		$classes[] = 'single-article';
	}

	// Add a class when the sidebar is enabled (helps CSS adjust main content width).
	if ( CA_SIDEBAR_ENABLED && ( is_archive() || is_home() ) ) {
		$classes[] = 'has-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'collision_academy_body_classes' );

// =============================================================================
// EXCERPT CUSTOMISATION
// =============================================================================

/**
 * collision_academy_excerpt_length()
 *
 * Controls how many words appear in auto-generated excerpts on archive pages.
 * The default WordPress value is 55 words.
 *
 * @param int $length Default excerpt word count.
 * @return int Our preferred excerpt length.
 */
function collision_academy_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'collision_academy_excerpt_length' );

/**
 * collision_academy_excerpt_more()
 *
 * Replaces the default "[...]" at the end of truncated excerpts with a
 * clean ellipsis. We don't add a "Read more" link here — that's handled
 * in the card template where we have better context.
 *
 * @param string $more The current "more" string.
 * @return string Our replacement.
 */
function collision_academy_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'collision_academy_excerpt_more' );

// =============================================================================
// WIDGET AREAS (SIDEBARS)
// =============================================================================

/**
 * collision_academy_widgets_init()
 *
 * Registers the sidebar widget area. Even if CA_SIDEBAR_ENABLED is false,
 * we still register it so the admin can see it in wp-admin > Appearance > Widgets.
 * The sidebar is simply not displayed in templates when the constant is false.
 */
function collision_academy_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Main Sidebar', 'collision-academy' ),
		'id'            => 'sidebar-main',
		'description'   => esc_html__( 'Widgets shown in the sidebar on archive and blog pages. Enable the sidebar in functions.php by setting CA_SIDEBAR_ENABLED to true.', 'collision-academy' ),
		'before_widget' => '<div id="%1$s" class="ca-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="ca-widget__title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'collision_academy_widgets_init' );

// =============================================================================
// REWRITE RULES (PERMALINK STRUCTURE)
// =============================================================================

/**
 * collision_academy_flush_rewrite_rules()
 *
 * Flushes WordPress's permalink rules when the theme is activated.
 * This is needed so that URLs like /articles/post-slug/ work immediately.
 *
 * WARNING: Never call flush_rewrite_rules() on every page load — it is
 * expensive. Only call it on theme activation (as we do here) and on
 * theme deactivation.
 */
function collision_academy_flush_rewrite_rules() {
	// CPT registration happens on 'init' (loaded from cpt.php),
	// so by the time after_switch_theme fires, rewrite slugs are registered.
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'collision_academy_flush_rewrite_rules', 20 );
// Priority 20 = runs after cpt.php registers CPTs (which hooks on 'init', priority 10).

// =============================================================================
// SECURITY: REMOVE UNNECESSARY WORDPRESS FEATURES
// =============================================================================

/**
 * Remove the WordPress version number from the front-end HTML source.
 * Hiding the version makes it slightly harder for automated scanners to
 * identify the exact WordPress version for targeted exploits.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Disable the REST API user enumeration endpoint for unauthenticated requests.
 * By default, /wp-json/wp/v2/users exposes usernames — we restrict this.
 *
 * @param WP_Error|null|bool $access Current access status.
 * @return WP_Error|null|bool Modified access status.
 */
function collision_academy_restrict_user_api( $access ) {
	if ( ! is_user_logged_in() ) {
		return new WP_Error(
			'rest_forbidden',
			esc_html__( 'Authentication required.', 'collision-academy' ),
			array( 'status' => 401 )
		);
	}
	return $access;
}
add_filter( 'rest_endpoints', function( $endpoints ) {
	if ( isset( $endpoints['/wp/v2/users'] ) ) {
		foreach ( $endpoints['/wp/v2/users'] as $key => $endpoint ) {
			$endpoints['/wp/v2/users'][ $key ]['permission_callback'] = 'collision_academy_restrict_user_api';
		}
	}
	return $endpoints;
} );
