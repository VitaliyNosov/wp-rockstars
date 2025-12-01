<?php

// Telegram bot form


// Function to send Telegram notification
function wp_custom_send_telegram_notification( $mid, $pid, $key, $val ) {
    if ( '_wp_custom_message' !== $key ) return;
    if ( 'ticket' !== get_post_type( $pid ) ) return;
    if ( get_post_meta( $pid, '_wp_custom_telegram_sent', true ) ) return;

    $token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
    $chat_id = '422713968';

    $name    = get_post_meta( $pid, '_wp_custom_sender_name', true );
    $email   = get_post_meta( $pid, '_wp_custom_sender_email', true );
    $message = get_post_meta( $pid, '_wp_custom_message', true );

    $text = "Новый тикет\nИмя: $name\nEmail: $email\n\n$message";

    $response = wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
        'body' => array(
            'chat_id' => $chat_id,
            'text'    => $text,
        ),
        'timeout' => 10,
    ) );

    // Log response for debugging
    if ( is_wp_error( $response ) ) {
        error_log( 'Telegram Bot Error: ' . $response->get_error_message() );
    } else {
        error_log( 'Telegram notification sent successfully for ticket #' . $pid );
    }

    update_post_meta( $pid, '_wp_custom_telegram_sent', 1 );
}

// Hook for both added and updated meta to ensure it works in all cases
add_action( 'added_post_meta', 'wp_custom_send_telegram_notification', 10, 4 );
add_action( 'updated_post_meta', 'wp_custom_send_telegram_notification', 10, 4 );

