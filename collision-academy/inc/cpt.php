<?php
/**
 * Collision Academy — Custom Post Types
 *
 * Registers the 'course' and 'resource' custom post types for the future
 * training platform. These are registered now to reserve the post type slugs
 * and prevent URL conflicts when Phase 2 development begins.
 *
 * At launch, visiting a course or resource URL shows a "Coming Soon" notice.
 * The post types are visible in wp-admin so content can be drafted in advance.
 *
 * @package CollisionAcademy
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// REGISTER CUSTOM POST TYPES
// =============================================================================

/**
 * collision_academy_register_cpts()
 *
 * Registers all custom post types. Hooked to 'init' so WordPress's rewrite
 * system is ready to handle our custom slugs.
 */
function collision_academy_register_cpts() {

	// -------------------------------------------------------------------------
	// POST TYPE: Course
	// -------------------------------------------------------------------------
	// For future training courses and CPD (Continuing Professional Development).
	// URL structure: /courses/course-slug/

	$course_labels = array(
		'name'                  => _x( 'Courses', 'Post type general name', 'collision-academy' ),
		'singular_name'         => _x( 'Course', 'Post type singular name', 'collision-academy' ),
		'menu_name'             => _x( 'Courses', 'Admin Menu text', 'collision-academy' ),
		'add_new'               => __( 'Add New', 'collision-academy' ),
		'add_new_item'          => __( 'Add New Course', 'collision-academy' ),
		'edit_item'             => __( 'Edit Course', 'collision-academy' ),
		'new_item'              => __( 'New Course', 'collision-academy' ),
		'view_item'             => __( 'View Course', 'collision-academy' ),
		'search_items'          => __( 'Search Courses', 'collision-academy' ),
		'not_found'             => __( 'No courses found.', 'collision-academy' ),
		'not_found_in_trash'    => __( 'No courses found in Trash.', 'collision-academy' ),
		'all_items'             => __( 'All Courses', 'collision-academy' ),
	);

	register_post_type( 'course', array(
		'labels'             => $course_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,         // Show in wp-admin.
		'show_in_menu'       => true,
		'show_in_rest'       => true,         // Enable Gutenberg editor support.
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'courses',
			'with_front' => false,
		),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-welcome-learn-more',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'description'        => __( 'Training courses for the future learning platform (Phase 2).', 'collision-academy' ),
	) );

	// -------------------------------------------------------------------------
	// POST TYPE: Resource
	// -------------------------------------------------------------------------
	// For downloadable resources, guides, templates, and reference documents.
	// URL structure: /resources/resource-slug/

	$resource_labels = array(
		'name'                  => _x( 'Resources', 'Post type general name', 'collision-academy' ),
		'singular_name'         => _x( 'Resource', 'Post type singular name', 'collision-academy' ),
		'menu_name'             => _x( 'Resources', 'Admin Menu text', 'collision-academy' ),
		'add_new'               => __( 'Add New', 'collision-academy' ),
		'add_new_item'          => __( 'Add New Resource', 'collision-academy' ),
		'edit_item'             => __( 'Edit Resource', 'collision-academy' ),
		'new_item'              => __( 'New Resource', 'collision-academy' ),
		'view_item'             => __( 'View Resource', 'collision-academy' ),
		'search_items'          => __( 'Search Resources', 'collision-academy' ),
		'not_found'             => __( 'No resources found.', 'collision-academy' ),
		'not_found_in_trash'    => __( 'No resources found in Trash.', 'collision-academy' ),
		'all_items'             => __( 'All Resources', 'collision-academy' ),
	);

	register_post_type( 'resource', array(
		'labels'             => $resource_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'resources',
			'with_front' => false,
		),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 21,
		'menu_icon'          => 'dashicons-media-document',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'description'        => __( 'Downloadable resources and guides for the future learning platform (Phase 2).', 'collision-academy' ),
	) );
}
add_action( 'init', 'collision_academy_register_cpts' );

// =============================================================================
// COMING SOON REDIRECT
// =============================================================================

/**
 * collision_academy_cpt_coming_soon_redirect()
 *
 * Intercepts requests for course and resource URLs and displays a
 * "Coming Soon" notice instead of a 404 or empty template.
 *
 * This runs on 'template_redirect' — after WordPress has determined which
 * template to use, but before it actually loads the template file.
 *
 * When Phase 2 development begins, simply remove this function (or the
 * add_action call below) and create proper single-course.php / archive-course.php
 * template files.
 */
function collision_academy_cpt_coming_soon_redirect() {
	if (
		is_singular( 'course' ) ||
		is_post_type_archive( 'course' ) ||
		is_singular( 'resource' ) ||
		is_post_type_archive( 'resource' )
	) {
		// Load the full theme header/footer but show a coming soon message.
		get_header();
		?>
		<section class="ca-coming-soon-page">
			<div class="ca-container">
				<div class="ca-coming-soon">
					<span class="ca-coming-soon__eyebrow"><?php esc_html_e( 'Collision Academy', 'collision-academy' ); ?></span>
					<h1 class="ca-coming-soon__title"><?php esc_html_e( 'Coming Soon', 'collision-academy' ); ?></h1>
					<p class="ca-coming-soon__text">
						<?php esc_html_e( 'We are building out our training platform. Courses, resources, and CPD materials will be available here in a future phase.', 'collision-academy' ); ?>
					</p>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ca-btn ca-btn--primary">
						<?php esc_html_e( 'Return to Home', 'collision-academy' ); ?>
					</a>
				</div>
			</div>
		</section>
		<?php
		get_footer();
		exit; // Stop WordPress from loading any further template output.
	}
}
add_action( 'template_redirect', 'collision_academy_cpt_coming_soon_redirect' );
