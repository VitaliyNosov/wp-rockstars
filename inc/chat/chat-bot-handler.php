<?php
/**
 * Telegram Bot Logic Handler
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Send message to Telegram Admin
 */
function rock_stars_send_to_telegram( $text, $keyboard = null ) {
    $token = carbon_get_theme_option( 'chat_bot_token' );
    $admin_id = carbon_get_theme_option( 'chat_admin_id' );

    if ( ! $token || ! $admin_id ) {
        return false;
    }

    $args = array(
        'chat_id' => $admin_id,
        'text'    => $text,
        'parse_mode' => 'Markdown',
    );

    if ( $keyboard ) {
        $args['reply_markup'] = json_encode( $keyboard );
    }

    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $response = wp_remote_post( $url, array(
        'body' => $args,
        'timeout' => 10,
    ) );

    if ( is_wp_error( $response ) ) {
        error_log( 'Telegram Send Error: ' . $response->get_error_message() );
        return false;
    }

    return json_decode( wp_remote_retrieve_body( $response ), true );
}

/**
 * Send file to Telegram Admin
 */
function rock_stars_send_file_to_telegram( $file_path, $caption = '' ) {
    $token = carbon_get_theme_option( 'chat_bot_token' );
    $admin_id = carbon_get_theme_option( 'chat_admin_id' );

    if ( ! $token || ! $admin_id || ! file_exists( $file_path ) ) {
        return false;
    }

    $url = "https://api.telegram.org/bot{$token}/sendDocument";
    
    // PHP 5.5+ safe way to send files with CURL via wp_remote_post is tricky.
    // We'll use a more direct approach for multipart/form-data with files.
    
    $args = array(
        'chat_id' => $admin_id,
        'caption' => $caption,
        'document' => new CURLFile( $file_path ),
        'parse_mode' => 'Markdown'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode( $result, true );
}

/**
 * Send voice message to Telegram Admin
 */
function rock_stars_send_voice_to_telegram( $file_path, $caption = '', $duration = 0 ) {
    $token = carbon_get_theme_option( 'chat_bot_token' );
    $admin_id = carbon_get_theme_option( 'chat_admin_id' );

    if ( ! $token || ! $admin_id || ! file_exists( $file_path ) ) {
        return false;
    }

    $extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
    
    // Telegram sendVoice only supports OGG with Opus. 
    // If it's something else (like webm, m4a), we use sendAudio instead.
    $method = ( $extension === 'ogg' || $extension === 'opus' ) ? 'sendVoice' : 'sendAudio';
    $field = ( $method === 'sendVoice' ) ? 'voice' : 'audio';

    $url = "https://api.telegram.org/bot{$token}/{$method}";
    
    $args = array(
        'chat_id' => $admin_id,
        'caption' => $caption,
        $field     => new CURLFile( $file_path ),
        'duration' => $duration,
        'parse_mode' => 'Markdown'
    );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    $result = curl_exec( $ch );
    curl_close( $ch );

    $response = json_decode( $result, true );
    
    // If sendVoice/sendAudio failed, try sendDocument as last resort
    if ( ! isset( $response['ok'] ) || ! $response['ok'] ) {
        error_log('Telegram ' . $method . ' failed: ' . $result);
        return rock_stars_send_file_to_telegram( $file_path, $caption );
    }

    return $response;
}

/**
 * Send video message to Telegram Admin
 */
function rock_stars_send_video_to_telegram( $file_path, $caption = '', $duration = 0 ) {
    $token = carbon_get_theme_option( 'chat_bot_token' );
    $admin_id = carbon_get_theme_option( 'chat_admin_id' );

    if ( ! $token || ! $admin_id || ! file_exists( $file_path ) ) {
        return false;
    }

    $url = "https://api.telegram.org/bot{$token}/sendVideo";
    
    $args = array(
        'chat_id' => $admin_id,
        'caption' => $caption,
        'video'   => new CURLFile( $file_path ),
        'duration' => $duration,
        'supports_streaming' => true,
        'width' => 640,
        'height' => 480,
        'parse_mode' => 'Markdown'
    );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    $result = curl_exec( $ch );
    curl_close( $ch );

    $response = json_decode( $result, true );

    // If sendVideo failed, try sendDocument as last resort
    if ( ! isset( $response['ok'] ) || ! $response['ok'] ) {
        error_log('Telegram sendVideo failed: ' . $result);
        return rock_stars_send_file_to_telegram( $file_path, $caption );
    }

    return $response;
}

/**
 * Process incoming Telegram Webhook
 */
function rock_stars_handle_telegram_webhook( $data ) {
    // 1. Handle Button Click (Callback Query)
    if ( ! empty( $data['callback_query'] ) ) {
        $callback = $data['callback_query'];
        $callback_data = $callback['data'] ?? '';
        $chat_id = $callback['message']['chat']['id'] ?? '';
        $admin_id = carbon_get_theme_option( 'chat_admin_id' );



        if ( (string)$chat_id === (string)$admin_id && strpos( $callback_data, 'reply:' ) === 0 ) {
            $ticket_id = str_replace( 'reply:', '', $callback_data );
            
            // Send message with ForceReply
            $tg_response = rock_stars_send_to_telegram( 
                "✍️ Пожалуйста, введите ваш ответ для пользователя:", 
                array( 'force_reply' => true, 'selective' => true ) 
            );



            if ( ! empty( $tg_response['result']['message_id'] ) ) {
                add_post_meta( $ticket_id, '_tg_message_id', $tg_response['result']['message_id'] );
            }
        }
        return;
    }

    if ( empty( $data['message'] ) ) {
        return;
    }

    $message = $data['message'];
    $text    = $message['text'] ?? '';
    $chat_id = $message['chat']['id'] ?? '';
    $admin_id = carbon_get_theme_option( 'chat_admin_id' );



    // Ensure it's from the admin
    if ( (string)$chat_id !== (string)$admin_id ) {

        return;
    }

    // 2. Handle Commands
    if ( $text === '/online' ) {
        update_option( '_chat_online_status', 'yes' ); 
        rock_stars_send_to_telegram( "✅ Status updated: *Online*" );
        return;
    }

    if ( $text === '/offline' ) {
        update_option( '_chat_online_status', 'no' );
        rock_stars_send_to_telegram( "🌙 Status updated: *Offline*" );
        return;
    }

    // 3. Handle Replies
    if ( ! empty( $message['reply_to_message'] ) ) {
        $reply_to_id = $message['reply_to_message']['message_id'];
        
        // Use a more direct meta query
        global $wpdb;
        $ticket_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_tg_message_id' AND meta_value = %s LIMIT 1",
            $reply_to_id
        ) );

        if ( $ticket_id ) {
            $reply_text = $text;

            $history = get_post_meta( $ticket_id, '_chat_history', true ) ?: array();
            $history[] = array(
                'role' => 'bot',
                'text' => $reply_text,
                'time' => current_time( 'mysql' ),
            );
            update_post_meta( $ticket_id, '_chat_history', $history );
            
            // Mark as modified so polling might pick it up correctly if relying on timestamps (optional but good practice)
            wp_update_post( array( 'ID' => $ticket_id, 'post_modified' => current_time('mysql') ) );
        }
    }
}
