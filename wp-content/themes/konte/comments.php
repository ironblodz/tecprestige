<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			esc_html_e( 'Comments', 'konte' );

			if ( $comment_count ) {
				echo ' (' . number_format_i18n( $comment_count ) . ')';
			}
			?>
		</h2><!-- .comments-title -->

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 40,
			) );
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_pagination( array(
			'prev_text' => konte_svg_icon( 'icon=left&echo=0' ) . esc_html__( 'Prev', 'konte' ),
			'next_text' => esc_html__( 'Next', 'konte' ) . konte_svg_icon( 'icon=right&echo=0' ),
		) );

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'konte' ); ?></p>
		<?php
		endif;

	endif; // Check for have_comments().

	comment_form();
	?>

</div><!-- #comments -->
