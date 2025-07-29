<?php

// Telegram bot form


add_action( 'added_post_meta', function( $mid, $pid, $key, $val ) {
    if ( '_wp_custom_message' !== $key ) return;
    if ( 'ticket' !== get_post_type( $pid ) ) return;
    if ( get_post_meta( $pid, '_wp_custom_telegram_sent', true ) ) return;

    $token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
    $chat_id = '422713968';

    $name    = get_post_meta( $pid, '_wp_custom_sender_name', true );
    $email   = get_post_meta( $pid, '_wp_custom_sender_email', true );
    $message = get_post_meta( $pid, '_wp_custom_message', true );

    $text = "Новый тикет\nИмя: $name\nEmail: $email\n\n$message";

    wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
        'body' => array(
            'chat_id' => $chat_id,
            'text'    => $text,
        ),
        'timeout' => 10,
    ) );

    update_post_meta( $pid, '_wp_custom_telegram_sent', 1 );
}, 10, 4 );
