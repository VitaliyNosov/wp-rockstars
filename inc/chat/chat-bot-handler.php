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
function rock_stars_send_to_telegram( $rock_stars_text, $rock_stars_keyboard = null ) {
    $rock_stars_token    = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
    $rock_stars_admin_id = carbon_get_theme_option( 'rock_stars_chat_admin_id' );

    if ( ! $rock_stars_token || ! $rock_stars_admin_id ) {
        return false;
    }

    $rock_stars_args = array(
        'chat_id'    => $rock_stars_admin_id,
        'text'       => $rock_stars_text,
        'parse_mode' => 'Markdown',
    );

    if ( $rock_stars_keyboard ) {
        $rock_stars_args['reply_markup'] = json_encode( $rock_stars_keyboard );
    }

    $rock_stars_url      = "https://api.telegram.org/bot{$rock_stars_token}/sendMessage";
    $rock_stars_response = wp_remote_post( $rock_stars_url, array(
        'body'    => $rock_stars_args,
        'timeout' => 10,
    ) );

    if ( is_wp_error( $rock_stars_response ) ) {
        error_log( 'Telegram Send Error: ' . $rock_stars_response->get_error_message() );
        return false;
    }

    return json_decode( wp_remote_retrieve_body( $rock_stars_response ), true );
}

/**
 * Send file to Telegram Admin
 */
function rock_stars_send_file_to_telegram( $rock_stars_file_path, $rock_stars_caption = '' ) {
    $rock_stars_token    = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
    $rock_stars_admin_id = carbon_get_theme_option( 'rock_stars_chat_admin_id' );

    if ( ! $rock_stars_token || ! $rock_stars_admin_id || ! file_exists( $rock_stars_file_path ) ) {
        return false;
    }

    $rock_stars_url = "https://api.telegram.org/bot{$rock_stars_token}/sendDocument";
    
    $rock_stars_args = array(
        'chat_id'    => $rock_stars_admin_id,
        'caption'    => $rock_stars_caption,
        'document'   => new CURLFile( $rock_stars_file_path ),
        'parse_mode' => 'Markdown'
    );

    $rock_stars_ch = curl_init();
    curl_setopt( $rock_stars_ch, CURLOPT_URL, $rock_stars_url );
    curl_setopt( $rock_stars_ch, CURLOPT_POST, true );
    curl_setopt( $rock_stars_ch, CURLOPT_POSTFIELDS, $rock_stars_args );
    curl_setopt( $rock_stars_ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $rock_stars_ch, CURLOPT_SSL_VERIFYPEER, false );
    $rock_stars_result = curl_exec( $rock_stars_ch );
    curl_close( $rock_stars_ch );

    return json_decode( $rock_stars_result, true );
}

/**
 * Send voice message to Telegram Admin
 */
function rock_stars_send_voice_to_telegram( $rock_stars_file_path, $rock_stars_caption = '', $rock_stars_duration = 0 ) {
    $rock_stars_token    = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
    $rock_stars_admin_id = carbon_get_theme_option( 'rock_stars_chat_admin_id' );

    if ( ! $rock_stars_token || ! $rock_stars_admin_id || ! file_exists( $rock_stars_file_path ) ) {
        return false;
    }

    $rock_stars_extension = strtolower( pathinfo( $rock_stars_file_path, PATHINFO_EXTENSION ) );
    
    // Telegram sendVoice only supports OGG with Opus. 
    // If it's something else (like webm, m4a), we use sendAudio instead.
    $rock_stars_method = ( $rock_stars_extension === 'ogg' || $rock_stars_extension === 'opus' ) ? 'sendVoice' : 'sendAudio';
    $rock_stars_field  = ( $rock_stars_method === 'sendVoice' ) ? 'voice' : 'audio';

    $rock_stars_url = "https://api.telegram.org/bot{$rock_stars_token}/{$rock_stars_method}";
    
    $rock_stars_args = array(
        'chat_id'    => $rock_stars_admin_id,
        'caption'    => $rock_stars_caption,
        $rock_stars_field => new CURLFile( $rock_stars_file_path ),
        'duration'   => $rock_stars_duration,
        'parse_mode' => 'Markdown'
    );

    $rock_stars_ch = curl_init();
    curl_setopt( $rock_stars_ch, CURLOPT_URL, $rock_stars_url );
    curl_setopt( $rock_stars_ch, CURLOPT_POST, 1 );
    curl_setopt( $rock_stars_ch, CURLOPT_POSTFIELDS, $rock_stars_args );
    curl_setopt( $rock_stars_ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $rock_stars_ch, CURLOPT_SSL_VERIFYPEER, false );
    $rock_stars_result = curl_exec( $rock_stars_ch );
    curl_close( $rock_stars_ch );

    $rock_stars_response = json_decode( $rock_stars_result, true );
    
    // If sendVoice/sendAudio failed, try sendDocument as last resort
    if ( ! isset( $rock_stars_response['ok'] ) || ! $rock_stars_response['ok'] ) {
        error_log( 'Telegram ' . $rock_stars_method . ' failed: ' . $rock_stars_result );
        return rock_stars_send_file_to_telegram( $rock_stars_file_path, $rock_stars_caption );
    }

    return $rock_stars_response;
}

/**
 * Send video message to Telegram Admin
 */
function rock_stars_send_video_to_telegram( $rock_stars_file_path, $rock_stars_caption = '', $rock_stars_duration = 0 ) {
    $rock_stars_token    = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
    $rock_stars_admin_id = carbon_get_theme_option( 'rock_stars_chat_admin_id' );

    if ( ! $rock_stars_token || ! $rock_stars_admin_id || ! file_exists( $rock_stars_file_path ) ) {
        return false;
    }

    $rock_stars_url = "https://api.telegram.org/bot{$rock_stars_token}/sendVideo";
    
    $rock_stars_args = array(
        'chat_id'            => $rock_stars_admin_id,
        'caption'            => $rock_stars_caption,
        'video'              => new CURLFile( $rock_stars_file_path ),
        'duration'           => $rock_stars_duration,
        'supports_streaming' => true,
        'width'              => 640,
        'height'             => 480,
        'parse_mode'         => 'Markdown'
    );

    $rock_stars_ch = curl_init();
    curl_setopt( $rock_stars_ch, CURLOPT_URL, $rock_stars_url );
    curl_setopt( $rock_stars_ch, CURLOPT_POST, 1 );
    curl_setopt( $rock_stars_ch, CURLOPT_POSTFIELDS, $rock_stars_args );
    curl_setopt( $rock_stars_ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $rock_stars_ch, CURLOPT_SSL_VERIFYPEER, false );
    $rock_stars_result = curl_exec( $rock_stars_ch );
    curl_close( $rock_stars_ch );

    $rock_stars_response = json_decode( $rock_stars_result, true );

    // If sendVideo failed, try sendDocument as last resort
    if ( ! isset( $rock_stars_response['ok'] ) || ! $rock_stars_response['ok'] ) {
        error_log( 'Telegram sendVideo failed: ' . $rock_stars_result );
        return rock_stars_send_file_to_telegram( $rock_stars_file_path, $rock_stars_caption );
    }

    return $rock_stars_response;
}

/**
 * Process incoming Telegram Webhook
 */
function rock_stars_handle_telegram_webhook( $rock_stars_data ) {
    // 1. Handle Button Click (Callback Query)
    if ( ! empty( $rock_stars_data['callback_query'] ) ) {
        $rock_stars_callback      = $rock_stars_data['callback_query'];
        $rock_stars_callback_data = $rock_stars_callback['data'] ?? '';
        $rock_stars_chat_id       = $rock_stars_callback['message']['chat']['id'] ?? '';
        $rock_stars_admin_id      = carbon_get_theme_option( 'rock_stars_chat_admin_id' );
        
        // Match Chat ID and Admin ID
        if ( (string) $rock_stars_chat_id === (string) $rock_stars_admin_id && strpos( $rock_stars_callback_data, 'reply:' ) === 0 ) {
            $rock_stars_ticket_id = str_replace( 'reply:', '', $rock_stars_callback_data );
            
            // Send message with ForceReply
            $rock_stars_tg_response = rock_stars_send_to_telegram( 
                "✍️ Пожалуйста, введите ваш ответ для пользователя:", 
                array( 'force_reply' => true, 'selective' => true ) 
            );

            if ( ! empty( $rock_stars_tg_response['result']['message_id'] ) ) {
                add_post_meta( $rock_stars_ticket_id, '_rock_stars_tg_message_id', $rock_stars_tg_response['result']['message_id'] );
            }
            
            // Answer callback query to stop loading animation on button
            $rock_stars_token = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
            if ( $rock_stars_token ) {
                wp_remote_get( "https://api.telegram.org/bot{$rock_stars_token}/answerCallbackQuery?callback_query_id=" . urlencode( $rock_stars_callback['id'] ) );
            }
        }
        return;
    }

    if ( empty( $rock_stars_data['message'] ) ) {
        return;
    }

    $rock_stars_message  = $rock_stars_data['message'];
    $rock_stars_text     = $rock_stars_message['text'] ?? '';
    $rock_stars_chat_id  = $rock_stars_message['chat']['id'] ?? '';
    $rock_stars_admin_id = carbon_get_theme_option( 'rock_stars_chat_admin_id' );

    // Ensure it's from the admin
    if ( (string) $rock_stars_chat_id !== (string) $rock_stars_admin_id ) {
        return;
    }

    // 2. Handle Commands
    if ( $rock_stars_text === '/online' ) {
        update_option( '_rock_stars_chat_online_status', 'yes' ); 
        rock_stars_send_to_telegram( "✅ Status updated: *Online*" );
        return;
    }

    if ( $rock_stars_text === '/offline' ) {
        update_option( '_rock_stars_chat_online_status', 'no' );
        rock_stars_send_to_telegram( "🌙 Status updated: *Offline*" );
        return;
    }

    // 3. Handle Replies
    if ( ! empty( $rock_stars_message['reply_to_message'] ) ) {
        $rock_stars_reply_to_id = $rock_stars_message['reply_to_message']['message_id'];
        
        global $wpdb;
        $rock_stars_ticket_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_rock_stars_tg_message_id' AND meta_value = %s LIMIT 1",
            $rock_stars_reply_to_id
        ) );

        if ( $rock_stars_ticket_id ) {
            $rock_stars_reply_text = $rock_stars_text;
            // Handle captions for files if text is empty
            if ( empty( $rock_stars_reply_text ) ) {
                $rock_stars_reply_text = $rock_stars_message['caption'] ?? '';
            }

            $rock_stars_history = get_post_meta( $rock_stars_ticket_id, '_rock_stars_chat_history', true );
            if ( ! is_array( $rock_stars_history ) ) {
                $rock_stars_history = array();
            }
            
            // Simple check: if last message is same text and role is bot
            $rock_stars_last_msg = end( $rock_stars_history );
            if ( $rock_stars_last_msg && isset( $rock_stars_last_msg['role'] ) && $rock_stars_last_msg['role'] === 'bot' && isset( $rock_stars_last_msg['text'] ) && $rock_stars_last_msg['text'] === $rock_stars_reply_text ) {
                 // Duplicate message detected. Skipping.
            } else {
                $rock_stars_history[] = array(
                    'role' => 'bot',
                    'text' => $rock_stars_reply_text,
                    'time' => current_time( 'mysql' ),
                );
                
                update_post_meta( $rock_stars_ticket_id, '_rock_stars_chat_history', $rock_stars_history );
            }
            
            // Mark as modified
            wp_update_post( array( 'ID' => $rock_stars_ticket_id, 'post_modified' => current_time( 'mysql' ) ) );
        }
    }
}




