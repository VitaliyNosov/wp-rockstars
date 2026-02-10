<?php
/**
 * Register Custom Post Type for Q&A (Help Widget)
 */

function register_qa_cpt() {
    // ... (existing qa registration)
    $labels = array(
        'name'                  => _x( 'Вопросы и ответы', 'Post Type General Name', 'rock-stars' ),
        'singular_name'         => _x( 'Вопрос', 'Post Type Singular Name', 'rock-stars' ),
        'menu_name'             => __( 'Вопросы и ответы (Chat)', 'rock-stars' ),
        'name_admin_bar'        => __( 'Вопрос', 'rock-stars' ),
        'archives'              => __( 'Архив вопросов', 'rock-stars' ),
        'attributes'            => __( 'Атрибуты вопроса', 'rock-stars' ),
        'parent_item_colon'     => __( 'Родительский вопрос:', 'rock-stars' ),
        'all_items'             => __( 'Все вопросы', 'rock-stars' ),
        'add_new_item'          => __( 'Добавить новый вопрос', 'rock-stars' ),
        'add_new'               => __( 'Добавить новый', 'rock-stars' ),
        'new_item'              => __( 'Новый вопрос', 'rock-stars' ),
        'edit_item'             => __( 'Редактировать вопрос', 'rock-stars' ),
        'update_item'           => __( 'Обновить вопрос', 'rock-stars' ),
        'view_item'             => __( 'Просмотр вопроса', 'rock-stars' ),
        'view_items'            => __( 'Просмотр вопросов', 'rock-stars' ),
        'search_items'          => __( 'Искать вопрос', 'rock-stars' ),
    );

    $args = array(
        'label'                 => __( 'Вопрос', 'rock-stars' ),
        'description'           => __( 'База знаний для чат-виджета', 'rock-stars' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt' ),
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-format-chat',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_in_rest'          => true,
        'show_in_graphql'       => true,
        'graphql_single_name'   => 'qaItem',
        'graphql_plural_name'   => 'qaItems',
        'rest_base'             => 'qa',
    );

    register_post_type( 'qa', $args );

    // Register Ticket Post Type for Chat Histories
    register_post_type( 'ticket', array(
        'labels' => array(
            'name'          => __( 'Tickets', 'rock-stars' ),
            'singular_name' => __( 'Ticket', 'rock-stars' ),
        ),
        'public'      => false,
        'show_ui'     => true,
        'show_in_menu' => 'edit.php?post_type=qa', // Nest under Q&A
        'supports'    => array( 'title', 'editor', 'custom-fields' ),
        'menu_icon'   => 'dashicons-email-alt',
    ) );
}
add_action( 'init', 'register_qa_cpt', 0 );

/**
 * Allow WebM/Opus Uploads for Chat
 */
add_filter('upload_mimes', function($mimes) {
    // Video formats
    $mimes['webm'] = 'video/webm';
    $mimes['mp4']  = 'video/mp4';
    $mimes['mkv']  = 'video/x-matroska';
    
    // Audio formats
    $mimes['ogg']  = 'audio/ogg';
    $mimes['oga']  = 'audio/ogg';
    $mimes['opus'] = 'audio/ogg';
    $mimes['wav']  = 'audio/wav';
    $mimes['m4a']  = 'audio/mp4';
    
    // WebM can also be audio-only
    if (!isset($mimes['weba'])) {
        $mimes['weba'] = 'audio/webm';
    }
    
    return $mimes;
});

/**
 * Register Custom REST Routes for Chat & Webhook
 */
add_action( 'rest_api_init', function () {
    // Contact Form Submission
    register_rest_route( 'qa/v1', '/contact', array(
        'methods' => 'POST',
        'callback' => 'handle_qa_contact_submission',
        'permission_callback' => '__return_true',
    ) );

    // Telegram Webhook
    register_rest_route( 'qa/v1', '/webhook', array(
        'methods' => 'POST',
        'callback' => function( $request ) {
            $data = $request->get_json_params();

            rock_stars_handle_telegram_webhook( $data );
            return array( 'success' => true );
        },
        'permission_callback' => '__return_true',
    ) );

    // Get Chat History (Polling)
    register_rest_route( 'qa/v1', '/history', array(
        'methods' => 'GET',
        'callback' => 'handle_get_chat_history',
        'permission_callback' => '__return_true',
    ) );

    // Get Current Status
    register_rest_route( 'qa/v1', '/status', array(
        'methods' => 'GET',
        'callback' => function() {
            return array(
                'is_online' => carbon_get_theme_option( 'chat_online_status' ) === 'yes' || carbon_get_theme_option( 'chat_online_status' ) === true,
                'offline_msg' => carbon_get_theme_option( 'chat_offline_message' ),
            );
        },
        'permission_callback' => '__return_true',
    ) );
} );

function handle_get_chat_history( $request ) {
    $session_id = $request->get_param( 'session_id' );
    
    if ( ! $session_id ) return array();

    $tickets = get_posts( array(
        'post_type' => 'ticket',
        'meta_key'  => '_chat_session_id',
        'meta_value' => $session_id,
        'posts_per_page' => 1,
        'post_status' => 'any',
        'orderby' => 'date',
        'order' => 'DESC'
    ) );
    
    if ( empty( $tickets ) ) {
        return array();
    }

    $history = get_post_meta( $tickets[0]->ID, '_chat_history', true ) ?: array();
    
    // Apply Ngrok/Proxy fix to history URLs
    // We prioritize X-Forwarded headers as they contain the public URL (like Ngrok)
    $current_host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($current_host, ',') !== false) {
        $parts = explode(',', $current_host);
        $current_host = trim($parts[0]);
    }

    $current_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http' );

    if ($current_host) {
        $site_url = get_site_url();
        $url_parts = parse_url($site_url);
        if (isset($url_parts['host'])) {
            $old_authority = $url_parts['host'] . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '');
            $old_proto = $url_parts['scheme'] ?? 'http';

            foreach ($history as &$msg) {
                if (isset($msg['text'])) {
                    // 1. Replace the authority (host:port)
                    $msg['text'] = str_replace($old_authority, $current_host, $msg['text']);
                    // 2. Replace the protocol if it doesn't match the current access method
                    if ($old_proto !== $current_proto) {
                        $msg['text'] = str_replace($old_proto . '://' . $current_host, $current_proto . '://' . $current_host, $msg['text']);
                    }
                }
                if (isset($msg['attachment'])) {
                    $msg['attachment'] = str_replace($old_authority, $current_host, $msg['attachment']);
                    if ($old_proto !== $current_proto) {
                        $msg['attachment'] = str_replace($old_proto . '://' . $current_host, $current_proto . '://' . $current_host, $msg['attachment']);
                    }
                    // Final safety for protocol
                    if (function_exists('set_url_scheme')) {
                        $msg['attachment'] = set_url_scheme($msg['attachment'], $current_proto);
                    }
                }
            }
        }
    }

    return $history;
}

function handle_qa_contact_submission( $request ) {
    // For multipart/form-data, use get_params() and get_file_params()
    $params = $request->get_params();
    $files  = $request->get_file_params();
    
    // 1. Anti-Bot: Honeypot Check
    if ( !empty( $params['website_url'] ) ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Email sent' ), 200 );
    }

    // Check for post_max_size overflow (empty POST/FILES when content-length > 0)
    if ( empty( $files ) && empty( $params ) && 
         isset( $_SERVER['CONTENT_LENGTH'] ) && 
         (int) $_SERVER['CONTENT_LENGTH'] > 0 ) {
         
         $max_size = ini_get( 'post_max_size' );
         return new WP_REST_Response( array( 
             'success' => false, 
             'message' => 'File exceeds server limit (' . $max_size . ')',
             'code' => 'post_max_size_exceeded'
         ), 400 );
    }

    // 2. Validate Inputs
    $session_id = sanitize_text_field( $params['session_id'] ?? '' );
    $name    = sanitize_text_field( $params['name'] ?? '' ) ?: 'Visitor';
    $email   = sanitize_email( $params['email'] ?? '' ) ?: 'no-email@provided.com';
    $message_text = sanitize_textarea_field( $params['message'] ?? '' );
    $is_voice = !empty( $params['is_voice'] );
    $is_video = !empty( $params['is_video'] );
    $duration = intval( $params['duration'] ?? 0 );

    // Handle File Upload
    $chat_file = $files['chat_file'] ?? null;
    $attachment_url = '';
    $local_path = '';

    if ( $chat_file && !empty($chat_file['name']) && !empty($chat_file['tmp_name']) ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        
        // Increased limit to 256MB
        if ($chat_file['size'] > 256 * 1024 * 1024) {
             return new WP_REST_Response( array( 'success' => false, 'message' => 'File too large (Max 256MB)', 'status' => 400 ), 400 );
        }

        // Use wp_upload_bits for better reliability in REST/programmatic contexts
        $upload = wp_upload_bits( $chat_file['name'], null, file_get_contents( $chat_file['tmp_name'] ) );

        if ( $upload && ! $upload['error'] ) {
            $attachment_url = $upload['url'];
            $local_path = $upload['file'];
            error_log('Chat Upload Success (wp_upload_bits): ' . $attachment_url);
        } else {
             $debug_upload = $upload;
             $upload_error_msg = $upload['error'] ?? 'Unknown upload error';
             error_log('Chat Upload Error: ' . $upload_error_msg);
             error_log('File Info: ' . print_r($chat_file, true));
        }
    } else {
        $debug_upload = 'No file provided or missing temp path';
    }

    if ( empty( $message_text ) && !$attachment_url ) {
        $error_detail = (isset($upload_error_msg)) ? ' (Error: ' . $upload_error_msg . ')' : '';
        return new WP_REST_Response( array( 
            'success' => false,
            'code' => 'missing_params', 
            'message' => 'Message is empty' . $error_detail, 
            'debug_upload' => isset($debug_upload) ? $debug_upload : null
        ), 400 );
    }

    // 3. Find existing ticket for this session
    $existing_ticket_id = null;
    if ( $session_id ) {
        $tickets = get_posts( array(
            'post_type'  => 'ticket',
            'meta_key'   => '_chat_session_id',
            'meta_value' => $session_id,
            'posts_per_page' => 1,
            'post_status' => 'any'
        ) );
        if ( ! empty( $tickets ) ) {
            $existing_ticket_id = $tickets[0]->ID;
        }
    }

    // 4. Send Telegram using Bot Settings
    $tg_text = $existing_ticket_id ? "💬 *New Message*\n" : "📩 *New Chat Inquiry*\n";
    $tg_text .= "👤 *$name*\n";
    if ($email !== 'no-email@provided.com') $tg_text .= "📧 $email\n";
    if ($message_text) $tg_text .= "\n❓ " . $message_text;
    
    if ( $existing_ticket_id ) {
        $tg_text .= "\n\n_Continues existing thread_";
    }

    if ($attachment_url) {
        $prefix = $is_voice ? "🎤 Voice Message" : ($is_video ? "📹 Video Message" : "📎 [Attached File]");
        $tg_text .= "\n\n" . $prefix . ($is_video || $is_voice ? "" : "(" . $attachment_url . ")");
    }

    $tg_message_id = null;
    if ( $local_path ) {
        // Telegram Bot API limit is 50MB. We use 49MB to be safe.
        $filesize = file_exists($local_path) ? filesize($local_path) : 0;
        
        if ( $filesize > 49 * 1024 * 1024 ) {
             // Too large for Telegram: Send Link instead
             if (strpos($tg_text, $attachment_url) === false) {
                 $tg_text .= "\n\n📥 File Link: " . $attachment_url . "\n(File > 50MB, cannot be sent to Telegram directly)";
             } else {
                 $tg_text .= "\n(File > 50MB, sent as link)";
             }
             $tg_response = rock_stars_send_to_telegram( $tg_text );
             $tg_message_id = $tg_response['result']['message_id'] ?? null;
             
        } else {
            // Normal sending
            if ( $is_voice ) {
                $tg_response = rock_stars_send_voice_to_telegram( $local_path, $tg_text, $duration );
            } elseif ( $is_video ) {
                $tg_response = rock_stars_send_video_to_telegram( $local_path, $tg_text, $duration );
            } else {
                $tg_response = rock_stars_send_file_to_telegram( $local_path, $tg_text );
            }
            
            // Check for failure and fallback to link
            if ( ! isset($tg_response['ok']) || ! $tg_response['ok'] ) {
                 // Try sending just the link as fallback
                 $fallback_text = $tg_text;
                 if (strpos($fallback_text, $attachment_url) === false) {
                     $fallback_text .= "\n\n📥 File Link: " . $attachment_url . "\n(File failed to upload to Telegram, here is the link)";
                 } else {
                     $fallback_text .= "\n(File upload failed, sent as link)";
                 }
                 $tg_response = rock_stars_send_to_telegram( $fallback_text );
            }

            $tg_message_id = $tg_response['result']['message_id'] ?? null;
        }
    } else {
        $tg_response = rock_stars_send_to_telegram( $tg_text );
        $tg_message_id = $tg_response['result']['message_id'] ?? null;
    }
    
    $tg_response_debug = isset($tg_response) ? $tg_response : 'No request made';

    // 5. Create or Update Ticket
    $history_text = $message_text ?: ($is_voice ? "🎤 Voice Message" : ($is_video ? "📹 Video Message" : "📎 Attached File"));
    if ($attachment_url) {
        $prefix = $is_voice ? "🎤 Voice: " : ($is_video ? "📹 Video: " : "📎 File: ");
        $history_text .= ($message_text ? "\n\n" : "") . $prefix . "<a href='$attachment_url' target='_blank'>" . basename($attachment_url) . "</a>";
    }

    if ( $existing_ticket_id ) {
        $post_id = $existing_ticket_id;
        if ($tg_message_id) add_post_meta( $post_id, '_tg_message_id', $tg_message_id );

        // Update Reply Button
        $token = carbon_get_theme_option( 'chat_bot_token' );
        if ($tg_message_id && $token) {
            $btn_keyboard = array(
                'inline_keyboard' => array( array( array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $post_id ) ) )
            );
            wp_remote_post( "https://api.telegram.org/bot{$token}/editMessageReplyMarkup", array(
                'body' => array(
                    'chat_id' => carbon_get_theme_option( 'chat_admin_id' ),
                    'message_id' => $tg_message_id,
                    'reply_markup' => json_encode( $btn_keyboard )
                )
            ) );
        }
        
        $current_content = get_post_field( 'post_content', $post_id );
        wp_update_post( array(
            'ID' => $post_id,
            'post_content' => $current_content . "\n\n---\n" . $history_text
        ) );

        $history = get_post_meta( $post_id, '_chat_history', true ) ?: array();
        $history[] = array(
            'role' => 'user',
            'text' => $message_text ?: ($is_voice ? '🎤 Voice Message' : ($is_video ? '📹 Video Message' : '')),
            'attachment' => $attachment_url,
            'is_voice' => $is_voice,
            'is_video' => $is_video,
            'time' => current_time( 'mysql' )
        );
        update_post_meta( $post_id, '_chat_history', $history );
        
    } else {
        $post_data = array(
            'post_title'   => 'Chat Inquiry: ' . $name . ' (' . ( $session_id ?: 'no session' ) . ')',
            'post_content' => $history_text,
            'post_status'  => 'publish',
            'post_type'    => 'ticket',
            'post_author'  => 1 
        );

        $post_id = wp_insert_post( $post_data );

        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_wp_custom_sender_name', $name );
            update_post_meta( $post_id, '_wp_custom_sender_email', $email );
            update_post_meta( $post_id, '_chat_session_id', $session_id );
            
            if ($tg_message_id) add_post_meta( $post_id, '_tg_message_id', $tg_message_id );
            
            $token = carbon_get_theme_option( 'chat_bot_token' );
            if ($tg_message_id && $token) {
                $btn_keyboard = array(
                    'inline_keyboard' => array( array( array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $post_id ) ) )
                );
                wp_remote_post( "https://api.telegram.org/bot{$token}/editMessageReplyMarkup", array(
                    'body' => array(
                        'chat_id' => carbon_get_theme_option( 'chat_admin_id' ),
                        'message_id' => $tg_message_id,
                        'reply_markup' => json_encode( $btn_keyboard )
                    )
                ) );
            }

            $history = array(
                array(
                    'role' => 'user',
                    'text' => $message_text ?: ($is_voice ? '🎤 Voice Message' : ($is_video ? '📹 Video Message' : '')),
                    'attachment' => $attachment_url,
                    'is_voice' => $is_voice,
                    'is_video' => $is_video,
                    'time' => current_time( 'mysql' )
                )
            );
            update_post_meta( $post_id, '_chat_history', $history );
        }
    }

    // 6. Send Email only for new inquiries
    if ( ! $existing_ticket_id ) {
        $to = get_option( 'admin_email' );
        $subject = 'New Chat Inquiry: ' . $name;
        $body = "Name: $name\nEmail: $email\nQuestion:\n$message_text";
        wp_mail( $to, $subject, $body );
    }

    return new WP_REST_Response( array( 
        'success'      => true, 
        'message'      => 'Message handled',
        'debug_upload' => isset($debug_upload) ? $debug_upload : null,
        'debug_tg'     => isset($tg_response_debug) ? $tg_response_debug : null
    ), 200 );
}
