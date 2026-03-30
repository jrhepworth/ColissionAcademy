<?php
/**
 * Collision Academy — Search Form Template
 *
 * Provides consistent markup for get_search_form() calls and widgets.
 *
 * @package CollisionAcademy
 */

$search_id = wp_unique_id( 'ca-searchform-' );
?>

<form role="search" method="get" class="search-form ca-search-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $search_id ); ?>" class="screen-reader-text">
		<?php esc_html_e( 'Search articles', 'collision-academy' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $search_id ); ?>"
		class="search-field ca-search-inline__input"
		placeholder="<?php esc_attr_e( 'Search articles…', 'collision-academy' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="search-submit ca-btn ca-btn--primary">
		<?php esc_html_e( 'Search', 'collision-academy' ); ?>
	</button>
</form>
