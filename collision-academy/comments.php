<?php
/**
 * Collision Academy — Comments Template
 *
 * Loaded by comments_template() in single.php.
 * Renders the comment list and comment submission form in a style
 * consistent with the rest of the theme.
 *
 * Features:
 * - Threaded comment display
 * - Author comments highlighted with a special badge
 * - Moderation notice shown to commenters
 * - Accessible form with proper labels
 *
 * @package CollisionAcademy
 */

// Prevent direct access and direct loading without a post context.
if ( ! defined( 'ABSPATH' ) || post_password_required() ) {
	return;
}
?>

<section id="comments" class="ca-comments">

	<?php if ( have_comments() ) : ?>

		<h2 class="ca-comments__title">
			<?php
			$comment_count = get_comments_number();
			printf(
				/* translators: 1: Number of comments, 2: Post title */
				esc_html( _n( '%1$s response to "%2$s"', '%1$s responses to "%2$s"', $comment_count, 'collision-academy' ) ),
				number_format_i18n( $comment_count ),
				'<span>' . esc_html( get_the_title() ) . '</span>'
			);
			?>
		</h2>

		<ol class="ca-comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'callback'    => 'collision_academy_comment',
				'avatar_size' => 50,
			) );
			?>
		</ol><!-- .ca-comment-list -->

		<?php
		// Pagination for comment pages (if enabled in Settings > Discussion).
		the_comments_navigation( array(
			'prev_text' => '<span aria-hidden="true">&larr;</span> ' . esc_html__( 'Older comments', 'collision-academy' ),
			'next_text' => esc_html__( 'Newer comments', 'collision-academy' ) . ' <span aria-hidden="true">&rarr;</span>',
		) );
		?>

	<?php endif; // have_comments() ?>

	<?php
	// If comments are closed but there are existing comments, show a notice.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="ca-comments-closed">
			<?php esc_html_e( 'Comments are closed for this article.', 'collision-academy' ); ?>
		</p>
	<?php endif; ?>

	<?php
	/*
	 * comment_form() outputs the comment submission form.
	 * We customise the default fields for style consistency.
	 */
	$commenter = wp_get_current_commenter();

	comment_form( array(
		'title_reply'          => esc_html__( 'Leave a comment', 'collision-academy' ),
		'title_reply_to'       => esc_html__( 'Reply to %s', 'collision-academy' ),
		'cancel_reply_link'    => esc_html__( 'Cancel reply', 'collision-academy' ),
		'label_submit'         => esc_html__( 'Submit comment', 'collision-academy' ),
		'class_submit'         => 'ca-btn ca-btn--primary',
		'comment_notes_before' => sprintf(
			'<p class="ca-comment-notes">%s</p>',
			esc_html__( 'Your email address will not be published. Comments are moderated and may take time to appear.', 'collision-academy' )
		),
		'comment_notes_after'  => '',
		'class_form'           => 'ca-comment-form',
		'fields'               => array(
			'author' => sprintf(
				'<p class="ca-form-field">
					<label for="author">%s <span aria-hidden="true">*</span></label>
					<input id="author" name="author" type="text" class="ca-form-field__input" value="%s" size="30" maxlength="245" autocomplete="name" required>
				</p>',
				esc_html__( 'Name', 'collision-academy' ),
				esc_attr( isset( $commenter['comment_author'] ) ? $commenter['comment_author'] : '' )
			),
			'email'  => sprintf(
				'<p class="ca-form-field">
					<label for="email">%s <span aria-hidden="true">*</span></label>
					<input id="email" name="email" type="email" class="ca-form-field__input" value="%s" size="30" maxlength="100" autocomplete="email" required>
					<span class="ca-form-field__help">%s</span>
				</p>',
				esc_html__( 'Email', 'collision-academy' ),
				esc_attr( isset( $commenter['comment_author_email'] ) ? $commenter['comment_author_email'] : '' ),
				esc_html__( 'Not published.', 'collision-academy' )
			),
			'url'    => '', // Remove the website URL field — reduces spam.
		),
		'comment_field'        => sprintf(
			'<p class="ca-form-field">
				<label for="comment">%s <span aria-hidden="true">*</span></label>
				<textarea id="comment" name="comment" class="ca-form-field__textarea" cols="45" rows="8" maxlength="65525" required></textarea>
			</p>',
			esc_html__( 'Comment', 'collision-academy' )
		),
	) );
	?>

</section><!-- #comments -->

<?php
/**
 * collision_academy_comment()
 *
 * Custom callback for wp_list_comments() that renders each comment.
 * Called for every comment in the list. We render it ourselves to
 * apply our CSS classes and highlight author comments.
 *
 * @param WP_Comment $comment The comment object.
 * @param array      $args    Arguments passed to wp_list_comments().
 * @param int        $depth   Comment depth (for threading).
 */
function collision_academy_comment( $comment, $args, $depth ) {
	$is_post_author = ( $comment->user_id === get_post()->post_author );

	$classes = array( 'ca-comment' );
	if ( $is_post_author ) {
		$classes[] = 'ca-comment--author';
	}
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( $classes, $comment ); ?>>
		<div id="div-comment-<?php comment_ID(); ?>" class="ca-comment__inner">

			<!-- Avatar -->
			<div class="ca-comment__avatar">
				<?php echo get_avatar( $comment, 50, '', '', array( 'class' => 'ca-comment__avatar-img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="ca-comment__body">

				<!-- Comment Header -->
				<header class="ca-comment__header">
					<cite class="ca-comment__author">
						<?php echo get_comment_author_link( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</cite>

					<?php if ( $is_post_author ) : ?>
						<span class="ca-comment__badge"><?php esc_html_e( 'Author', 'collision-academy' ); ?></span>
					<?php endif; ?>

					<time class="ca-comment__date" datetime="<?php comment_date( 'c' ); ?>">
						<?php
						printf(
							esc_html__( '%s at %s', 'collision-academy' ),
							get_comment_date( 'j F Y' ),
							get_comment_time( 'H:i' )
						);
						?>
					</time>
				</header>

				<!-- Awaiting moderation notice -->
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="ca-comment__moderation">
						<?php esc_html_e( 'Your comment is awaiting moderation.', 'collision-academy' ); ?>
					</p>
				<?php endif; ?>

				<!-- Comment Text -->
				<div class="ca-comment__text">
					<?php comment_text(); ?>
				</div>

				<!-- Reply Link -->
				<?php if ( comments_open() && $depth < $args['max_depth'] ) : ?>
					<div class="ca-comment__reply">
						<?php
						comment_reply_link( array_merge( $args, array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '',
							'after'     => '',
							'reply_text' => esc_html__( 'Reply', 'collision-academy' ),
						) ) );
						?>
					</div>
				<?php endif; ?>

			</div><!-- .ca-comment__body -->

		</div><!-- .ca-comment__inner -->
	<?php
	// Note: NO closing </li> here — wp_list_comments handles that.
}
