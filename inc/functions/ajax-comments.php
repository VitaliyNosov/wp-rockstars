<?php
/**
 * AJAX Comment Handling
 *
 * @package Rock_Star
 */

function rock_stars_ajax_comment_handler() {
	ob_start(); // Start buffering to catch any stray output (VIP requirement).

	// Check nonce for security.
	$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'rock_stars_comment_nonce' ) ) {
		ob_end_clean();
		wp_send_json_error( 'Security check failed.' );
	}

	$comment_post_id = isset( $_POST['comment_post_ID'] ) ? (int) $_POST['comment_post_ID'] : 0;
	$author          = isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : '';
	$email           = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$url             = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
	$comment_content = isset( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '';
	$comment_parent  = isset( $_POST['comment_parent'] ) ? (int) $_POST['comment_parent'] : 0;

	$comment_data = array(
		'comment_post_ID' => $comment_post_id,
		'author'          => $author,
		'email'           => $email,
		'url'             => $url,
		'comment'         => $comment_content,
		'comment_type'    => '',
		'comment_parent'  => $comment_parent,
		'user_id'         => get_current_user_id(),
	);

	// Validate standard WordPress comment data
	$comment = wp_handle_comment_submission( $comment_data );

	if ( is_wp_error( $comment ) ) {
		// Prepare Sanitized Debug Info.
		$debug_info = sprintf(
			' [Recv: Name=%s, ID=%d]',
			esc_html( substr( $author, 0, 10 ) ),
			intval( $comment_post_id )
		);

		ob_end_clean();
		wp_send_json_error( $comment->get_error_message() . $debug_info );
	}

	$user = wp_get_current_user();
	do_action( 'set_comment_cookies', $comment, $user );

	// Render the new comment using output buffering.
	if ( ob_get_length() ) {
		ob_clean();
	}
	ob_start();

	$args = array(
		'max_depth' => get_option( 'thread_comments_depth' ),
		'style'     => 'div',
	);
	$depth = 1;

	// Use the shared callback to ensure identical HTML structure
	rock_stars_comment_callback( $comment, $args, $depth );
	echo '</div>'; // Manually close the div since the callback leaves it open for Walker_Comment compatibility

	$comment_html = ob_get_clean();

	// Prepare structured data for frontend
	$comment_data_for_frontend = array(
		'id'           => $comment->comment_ID,
		'authorName'   => $comment->comment_author,
		'authorAvatar' => get_avatar_url( $comment, array( 'size' => 50 ) ),
		'date'         => get_comment_date( get_option( 'date_format' ), $comment ),
		'content'      => apply_filters( 'comment_text', $comment->comment_content, $comment ),
		'parentId'     => $comment->comment_parent,
	);

	wp_send_json_success(
		array(
			'html'    => $comment_html,
			'data'    => $comment_data_for_frontend,
			'message' => 'Comment submitted successfully',
		)
	);
}

add_action('wp_ajax_rock_stars_submit_comment', 'rock_stars_ajax_comment_handler');
add_action('wp_ajax_nopriv_rock_stars_submit_comment', 'rock_stars_ajax_comment_handler');
