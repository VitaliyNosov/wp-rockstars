<?php
/**
 * Telegram bot form notifications
 *
 * @package Rock_Star
 */

/**
 * Function to send Telegram notification
 */
function rock_stars_send_telegram_notification( $mid, $pid, $key, $val ) {
	// Support both old and new keys during transition.
	$is_new_key = ( '_rock_stars_message' === $key );
	$is_old_key = ( '_wp_custom_message' === $key );

	if ( ! $is_new_key && ! $is_old_key ) {
		return;
	}

	$post_type = get_post_type( $pid );

	if ( 'ticket' !== $post_type ) {
		return;
	}

	$prefix = $is_new_key ? '_rock_stars_' : '_wp_custom_';

	if ( get_post_meta( $pid, $prefix . 'telegram_sent', true ) ) {
		return;
	}

	$token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
	$chat_id = '422713968';

	$name    = get_post_meta( $pid, $prefix . 'sender_name', true );
	$email   = get_post_meta( $pid, $prefix . 'sender_email', true );
	$message = get_post_meta( $pid, $prefix . 'message', true );

	$text = sprintf(
		"New Ticket\nName: %s\nEmail: %s\n\n%s",
		esc_html( $name ),
		esc_html( $email ),
		esc_html( $message )
	);

	$response = wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
		'body' => array(
			'chat_id' => $chat_id,
			'text'    => $text,
		),
		'timeout' => 2,
	) );

	update_post_meta( $pid, $prefix . 'telegram_sent', 1 );
}

// Hook for both added and updated meta to ensure it works in all cases
add_action( 'added_post_meta', 'rock_stars_send_telegram_notification', 10, 4 );
add_action( 'updated_post_meta', 'rock_stars_send_telegram_notification', 10, 4 );
