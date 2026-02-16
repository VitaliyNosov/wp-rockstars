<?php

add_action('wp_ajax_rock_stars_like_post', 'rock_stars_like_post');
add_action('wp_ajax_nopriv_rock_stars_like_post', 'rock_stars_like_post');

function rock_stars_like_post() {
	ob_start(); // Start buffering to catch accidental output (VIP requirement).

	// Verify nonce for security (Support both old and new if needed, though here we use theme-specific).
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'rock_stars_like_nonce' ) ) {
		ob_end_clean();
		wp_send_json_error( 'Invalid nonce' );
	}

	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$action  = isset( $_POST['like_action'] ) ? sanitize_text_field( wp_unslash( $_POST['like_action'] ) ) : 'add';

	if ( $post_id > 0 ) {
		// VIP: Use prefixed meta keys. We check old key for backward compatibility.
		$likes = get_post_meta( $post_id, '_rock_stars_post_likes_count', true );
		
		// Fallback to old key if new one doesn't exist.
		if ( '' === $likes ) {
			$likes = get_post_meta( $post_id, '_post_likes_count', true );
		}

		$likes = $likes ? intval( $likes ) : 0;

		if ( 'add' === $action ) {
			$likes++;
		} elseif ( 'remove' === $action ) {
			$likes--;
			if ( $likes < 0 ) {
				$likes = 0;
			}
		}

		// Update both keys or just the new one? VIP prefers prefixed. 
		// We update the new one to migrate data forward.
		update_post_meta( $post_id, '_rock_stars_post_likes_count', $likes );

		// Clean any stray output before sending JSON.
		if ( ob_get_length() ) {
			ob_clean();
		}

		wp_send_json_success( array( 'likes' => $likes ) );
	} else {
		ob_end_clean();
		wp_send_json_error( 'Invalid post ID' );
	}
}
