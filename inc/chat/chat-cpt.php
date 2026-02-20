<?php
/**
 * Register Custom Post Type for Q&A (Help Widget)
 */


function rock_stars_register_qa_cpt() {
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

    register_post_type( 'rock_stars_qa', $args );

    // Register Ticket Post Type for Chat Histories
    register_post_type( 'rock_stars_ticket', array(
        'labels' => array(
            'name'          => __( 'Tickets', 'rock-stars' ),
            'singular_name' => __( 'Ticket', 'rock-stars' ),
        ),
        'public'      => false,
        'show_ui'     => true,
        'show_in_menu' => 'edit.php?post_type=rock_stars_qa', // Nest under Q&A
        'supports'    => array( 'title', 'editor', 'custom-fields' ),
        'menu_icon'   => 'dashicons-email-alt',
    ) );
}
add_action( 'init', 'rock_stars_register_qa_cpt', 0 );

/**
 * Allow WebM/Opus Uploads for Chat
 */
add_filter( 'upload_mimes', function( $mimes ) {
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
    if ( ! isset( $mimes['weba'] ) ) {
        $mimes['weba'] = 'audio/webm';
    }
    
    return $mimes;
} );

/**
 * Register Custom REST Routes for Chat & Webhook
 */
add_action( 'rest_api_init', function () {
    // Contact Form Submission
    register_rest_route( 'qa/v1', '/contact', array(
        'methods'             => 'POST',
        'callback'            => 'rock_stars_handle_qa_contact_submission',
        'permission_callback' => '__return_true',
    ) );

    // Telegram Webhook
    register_rest_route( 'qa/v1', '/webhook', array(
        'methods'             => 'POST',
        'callback'            => function( $request ) {
            $rock_stars_data = $request->get_json_params();

            rock_stars_handle_telegram_webhook( $rock_stars_data );
            return array( 'success' => true );
        },
        'permission_callback' => '__return_true',
    ) );

    // Get Chat History (Polling)
    register_rest_route( 'qa/v1', '/history', array(
        'methods'             => 'GET',
        'callback'            => 'rock_stars_handle_get_chat_history',
        'permission_callback' => '__return_true',
    ) );

    // Get Current Status
    register_rest_route( 'qa/v1', '/status', array(
        'methods'             => 'GET',
        'callback'            => function() {
            return array(
                'is_online'   => carbon_get_theme_option( 'rock_stars_chat_online_status' ) === 'yes' || carbon_get_theme_option( 'rock_stars_chat_online_status' ) === true,
                'offline_msg' => carbon_get_theme_option( 'rock_stars_chat_offline_message' ),
            );
        },
        'permission_callback' => '__return_true',
    ) );
} );

function rock_stars_handle_get_chat_history( $request ) {
    $rock_stars_session_id = $request->get_param( 'session_id' );
    
    if ( ! $rock_stars_session_id ) {
        return array();
    }

    $rock_stars_tickets = get_posts( array(
        'post_type'      => 'rock_stars_ticket',
        'meta_key'       => '_rock_stars_chat_session_id',
        'meta_value'     => $rock_stars_session_id,
        'posts_per_page' => 1,
        'post_status'    => 'any',
        'orderby'        => 'date',
        'order'          => 'DESC'
    ) );
    
    if ( empty( $rock_stars_tickets ) ) {
        return array();
    }

    $rock_stars_history = get_post_meta( $rock_stars_tickets[0]->ID, '_rock_stars_chat_history', true ) ?: array();
    
    // Apply Ngrok/Proxy fix to history URLs
    // We prioritize X-Forwarded headers as they contain the public URL (like Ngrok)
    $rock_stars_current_host = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_HOST', FILTER_SANITIZE_URL ) ?: filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL ) ?: '';
    
    if ( strpos( $rock_stars_current_host, ',' ) !== false ) {
        $rock_stars_parts        = explode( ',', $rock_stars_current_host );
        $rock_stars_current_host = trim( $rock_stars_parts[0] );
    }

    $rock_stars_current_proto = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_PROTO', FILTER_SANITIZE_URL ) ?: ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) ? 'https' : 'http' );

    if ( $rock_stars_current_host ) {
        $rock_stars_site_url = get_site_url();
        $rock_stars_url_parts = parse_url( $rock_stars_site_url );
        if ( isset( $rock_stars_url_parts['host'] ) ) {
            $rock_stars_old_authority = $rock_stars_url_parts['host'] . ( isset( $rock_stars_url_parts['port'] ) ? ':' . $rock_stars_url_parts['port'] : '' );
            $rock_stars_old_proto     = $rock_stars_url_parts['scheme'] ?? 'http';

            foreach ( $rock_stars_history as &$rock_stars_msg ) {
                if ( isset( $rock_stars_msg['text'] ) ) {
                    // 1. Replace the authority (host:port)
                    $rock_stars_msg['text'] = str_replace( $rock_stars_old_authority, $rock_stars_current_host, $rock_stars_msg['text'] );
                    // 2. Replace the protocol if it doesn't match the current access method
                    if ( $rock_stars_old_proto !== $rock_stars_current_proto ) {
                        $rock_stars_msg['text'] = str_replace( $rock_stars_old_proto . '://' . $rock_stars_current_host, $rock_stars_current_proto . '://' . $rock_stars_current_host, $rock_stars_msg['text'] );
                    }
                }
                if ( isset( $rock_stars_msg['attachment'] ) ) {
                    $rock_stars_msg['attachment'] = str_replace( $rock_stars_old_authority, $rock_stars_current_host, $rock_stars_msg['attachment'] );
                    if ( $rock_stars_old_proto !== $rock_stars_current_proto ) {
                        $rock_stars_msg['attachment'] = str_replace( $rock_stars_old_proto . '://' . $rock_stars_current_host, $rock_stars_current_proto . '://' . $rock_stars_current_host, $rock_stars_msg['attachment'] );
                    }
                    // Final safety for protocol
                    if ( function_exists( 'set_url_scheme' ) ) {
                        $rock_stars_msg['attachment'] = set_url_scheme( $rock_stars_msg['attachment'], $rock_stars_current_proto );
                    }
                }
            }
        }
    }

    return $rock_stars_history;
}

function rock_stars_handle_qa_contact_submission( $request ) {
    // For multipart/form-data, use get_params() and get_file_params()
    $rock_stars_params = $request->get_params();
    $rock_stars_files  = $request->get_file_params();
    
    // 1. Anti-Bot: Honeypot Check
    if ( ! empty( $rock_stars_params['website_url'] ) ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Email sent' ), 200 );
    }

    // Check for post_max_size overflow (empty POST/FILES when content-length > 0)
    if ( empty( $rock_stars_files ) && empty( $rock_stars_params ) && 
         isset( $_SERVER['CONTENT_LENGTH'] ) && 
         (int) $_SERVER['CONTENT_LENGTH'] > 0 ) {
         
         $rock_stars_max_size = ini_get( 'post_max_size' );
         return new WP_REST_Response( array( 
             'success' => false, 
             'message' => 'File exceeds server limit (' . $rock_stars_max_size . ')',
             'code'    => 'post_max_size_exceeded'
         ), 400 );
    }

    // 2. Validate Inputs
    $rock_stars_session_id   = sanitize_text_field( $rock_stars_params['session_id'] ?? '' );
    $rock_stars_name         = sanitize_text_field( $rock_stars_params['name'] ?? '' ) ?: 'Visitor';
    $rock_stars_email        = sanitize_email( $rock_stars_params['email'] ?? '' ) ?: 'no-email@provided.com';
    $rock_stars_message_text = sanitize_textarea_field( $rock_stars_params['message'] ?? '' );
    $rock_stars_is_voice     = ! empty( $rock_stars_params['is_voice'] );
    $rock_stars_is_video     = ! empty( $rock_stars_params['is_video'] );
    $rock_stars_duration     = intval( $rock_stars_params['duration'] ?? 0 );

    // Handle File Upload
    $rock_stars_chat_file    = $rock_stars_files['chat_file'] ?? null;
    $rock_stars_attachment_url = '';
    $rock_stars_local_path     = '';

    if ( $rock_stars_chat_file && ! empty( $rock_stars_chat_file['name'] ) && ! empty( $rock_stars_chat_file['tmp_name'] ) ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        // Increased limit to 256MB
        if ( $rock_stars_chat_file['size'] > 256 * 1024 * 1024 ) {
             return new WP_REST_Response( array( 'success' => false, 'message' => 'File too large (Max 256MB)', 'status' => 400 ), 400 );
        }

        // Use wp_upload_bits for better reliability in REST/programmatic contexts
        $rock_stars_upload = wp_upload_bits( $rock_stars_chat_file['name'], null, file_get_contents( $rock_stars_chat_file['tmp_name'] ) );

        if ( $rock_stars_upload && ! $rock_stars_upload['error'] ) {
            $rock_stars_attachment_url = $rock_stars_upload['url'];
            $rock_stars_local_path      = $rock_stars_upload['file'];
            error_log( 'Chat Upload Success (wp_upload_bits): ' . $rock_stars_attachment_url );
        } else {
             $rock_stars_upload_error_msg = $rock_stars_upload['error'] ?? 'Unknown upload error';
             error_log( 'Chat Upload Error: ' . $rock_stars_upload_error_msg );
        }
    }

    if ( empty( $rock_stars_message_text ) && ! $rock_stars_attachment_url ) {
        $rock_stars_error_detail = ( isset( $rock_stars_upload_error_msg ) ) ? ' (Error: ' . $rock_stars_upload_error_msg . ')' : '';
        return new WP_REST_Response( array( 
            'success' => false,
            'code'    => 'missing_params', 
            'message' => 'Message is empty' . $rock_stars_error_detail, 
        ), 400 );
    }

    // 3. Find existing ticket for this session
    $rock_stars_existing_ticket_id = null;
    if ( $rock_stars_session_id ) {
        $rock_stars_tickets = get_posts( array(
            'post_type'      => 'rock_stars_ticket',
            'meta_key'       => '_rock_stars_chat_session_id',
            'meta_value'     => $rock_stars_session_id,
            'posts_per_page' => 1,
            'post_status'    => 'any'
        ) );
        if ( ! empty( $rock_stars_tickets ) ) {
            $rock_stars_existing_ticket_id = $rock_stars_tickets[0]->ID;
        }
    }

    // 4. Send Telegram using Bot Settings
    $rock_stars_tg_text = $rock_stars_existing_ticket_id ? "💬 *New Message*\n" : "📩 *New Chat Inquiry*\n";
    $rock_stars_tg_text .= "👤 *" . $rock_stars_name . "*\n";
    if ( $rock_stars_email !== 'no-email@provided.com' ) {
        $rock_stars_tg_text .= "📧 " . $rock_stars_email . "\n";
    }
    if ( $rock_stars_message_text ) {
        $rock_stars_tg_text .= "\n❓ " . $rock_stars_message_text;
    }
    
    if ( $rock_stars_existing_ticket_id ) {
        $rock_stars_tg_text .= "\n\n_Continues existing thread_";
    }

    if ( $rock_stars_attachment_url ) {
        $rock_stars_prefix = $rock_stars_is_voice ? "🎤 Voice Message" : ( $rock_stars_is_video ? "📹 Video Message" : "📎 [Attached File]" );
        $rock_stars_tg_text .= "\n\n" . $rock_stars_prefix . ( $rock_stars_is_video || $rock_stars_is_voice ? "" : "(" . $rock_stars_attachment_url . ")" );
    }

    $rock_stars_tg_message_id = null;
    if ( $rock_stars_local_path ) {
        // Telegram Bot API limit is 50MB. We use 49MB to be safe.
        $rock_stars_filesize = file_exists( $rock_stars_local_path ) ? filesize( $rock_stars_local_path ) : 0;
        
        if ( $rock_stars_filesize > 49 * 1024 * 1024 ) {
             // Too large for Telegram: Send Link instead
             if ( strpos( $rock_stars_tg_text, $rock_stars_attachment_url ) === false ) {
                 $rock_stars_tg_text .= "\n\n📥 File Link: " . $rock_stars_attachment_url . "\n(File > 50MB, cannot be sent to Telegram directly)";
             } else {
                 $rock_stars_tg_text .= "\n(File > 50MB, sent as link)";
             }
             $rock_stars_tg_response = rock_stars_send_to_telegram( $rock_stars_tg_text );
             $rock_stars_tg_message_id = $rock_stars_tg_response['result']['message_id'] ?? null;
             
        } else {
            // Normal sending
            if ( $rock_stars_is_voice ) {
                $rock_stars_tg_response = rock_stars_send_voice_to_telegram( $rock_stars_local_path, $rock_stars_tg_text, $rock_stars_duration );
            } elseif ( $rock_stars_is_video ) {
                $rock_stars_tg_response = rock_stars_send_video_to_telegram( $rock_stars_local_path, $rock_stars_tg_text, $rock_stars_duration );
            } else {
                $rock_stars_tg_response = rock_stars_send_file_to_telegram( $rock_stars_local_path, $rock_stars_tg_text );
            }
            
            // Check for failure and fallback to link
            if ( ! isset( $rock_stars_tg_response['ok'] ) || ! $rock_stars_tg_response['ok'] ) {
                 // Try sending just the link as fallback
                 $rock_stars_fallback_text = $rock_stars_tg_text;
                 if ( strpos( $rock_stars_fallback_text, $rock_stars_attachment_url ) === false ) {
                     $rock_stars_fallback_text .= "\n\n📥 File Link: " . $rock_stars_attachment_url . "\n(File failed to upload to Telegram, here is the link)";
                 } else {
                     $rock_stars_fallback_text .= "\n(File upload failed, sent as link)";
                 }
                 $rock_stars_tg_response = rock_stars_send_to_telegram( $rock_stars_fallback_text );
            }

            $rock_stars_tg_message_id = $rock_stars_tg_response['result']['message_id'] ?? null;
        }
    } else {
        $rock_stars_tg_response = rock_stars_send_to_telegram( $rock_stars_tg_text );
        $rock_stars_tg_message_id = $rock_stars_tg_response['result']['message_id'] ?? null;
    }
    
    // 5. Create or Update Ticket
    $rock_stars_history_text = $rock_stars_message_text ?: ( $rock_stars_is_voice ? "🎤 Voice Message" : ( $rock_stars_is_video ? "📹 Video Message" : "📎 Attached File" ) );
    if ( $rock_stars_attachment_url ) {
        $rock_stars_file_prefix = $rock_stars_is_voice ? "🎤 Voice: " : ( $rock_stars_is_video ? "📹 Video: " : "📎 File: " );
        $rock_stars_history_text .= ( $rock_stars_message_text ? "\n\n" : "" ) . $rock_stars_file_prefix . "<a href='" . esc_url( $rock_stars_attachment_url ) . "' target='_blank'>" . esc_html( basename( $rock_stars_attachment_url ) ) . "</a>";
    }

    if ( $rock_stars_existing_ticket_id ) {
        $rock_stars_post_id = $rock_stars_existing_ticket_id;
        if ( $rock_stars_tg_message_id ) {
            add_post_meta( $rock_stars_post_id, '_rock_stars_tg_message_id', $rock_stars_tg_message_id );
        }

        // Update Reply Button
        $rock_stars_token = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
        if ( $rock_stars_tg_message_id && $rock_stars_token ) {
            $rock_stars_btn_keyboard = array(
                'inline_keyboard' => array( array( array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $rock_stars_post_id ) ) )
            );
            wp_remote_post( "https://api.telegram.org/bot{$rock_stars_token}/editMessageReplyMarkup", array(
                'body' => array(
                    'chat_id'      => carbon_get_theme_option( 'rock_stars_chat_admin_id' ),
                    'message_id'   => $rock_stars_tg_message_id,
                    'reply_markup' => json_encode( $rock_stars_btn_keyboard )
                )
            ) );
        }
        
        $rock_stars_current_content = get_post_field( 'post_content', $rock_stars_post_id );
        wp_update_post( array(
            'ID'           => $rock_stars_post_id,
            'post_content' => $rock_stars_current_content . "\n\n---\n" . $rock_stars_history_text
        ) );

        $rock_stars_history = get_post_meta( $rock_stars_post_id, '_rock_stars_chat_history', true ) ?: array();
        $rock_stars_history[] = array(
            'role'       => 'user',
            'text'       => $rock_stars_message_text ?: ( $rock_stars_is_voice ? '🎤 Voice Message' : ( $rock_stars_is_video ? '📹 Video Message' : '' ) ),
            'attachment' => $rock_stars_attachment_url,
            'is_voice'   => $rock_stars_is_voice,
            'is_video'   => $rock_stars_is_video,
            'time'       => current_time( 'mysql' )
        );
        update_post_meta( $rock_stars_post_id, '_rock_stars_chat_history', $rock_stars_history );
        
    } else {
        $rock_stars_post_data = array(
            'post_title'   => 'Chat Inquiry: ' . $rock_stars_name . ' (' . ( $rock_stars_session_id ?: 'no session' ) . ')',
            'post_content' => $rock_stars_history_text,
            'post_status'  => 'publish',
            'post_type'    => 'rock_stars_ticket',
            'post_author'  => 1 
        );

        $rock_stars_post_id = wp_insert_post( $rock_stars_post_data );

        if ( ! is_wp_error( $rock_stars_post_id ) ) {
            update_post_meta( $rock_stars_post_id, '_rock_stars_custom_sender_name', $rock_stars_name );
            update_post_meta( $rock_stars_post_id, '_rock_stars_custom_sender_email', $rock_stars_email );
            update_post_meta( $rock_stars_post_id, '_rock_stars_chat_session_id', $rock_stars_session_id );
            
            if ( $rock_stars_tg_message_id ) {
                add_post_meta( $rock_stars_post_id, '_rock_stars_tg_message_id', $rock_stars_tg_message_id );
            }
            
            $rock_stars_token = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
            if ( $rock_stars_tg_message_id && $rock_stars_token ) {
                $rock_stars_btn_keyboard = array(
                    'inline_keyboard' => array( array( array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $rock_stars_post_id ) ) )
                );
                wp_remote_post( "https://api.telegram.org/bot{$rock_stars_token}/editMessageReplyMarkup", array(
                    'body' => array(
                        'chat_id'      => carbon_get_theme_option( 'rock_stars_chat_admin_id' ),
                        'message_id'   => $rock_stars_tg_message_id,
                        'reply_markup' => json_encode( $rock_stars_btn_keyboard )
                    )
                ) );
            }

            $rock_stars_history = array(
                array(
                    'role'       => 'user',
                    'text'       => $rock_stars_message_text ?: ( $rock_stars_is_voice ? '🎤 Voice Message' : ( $rock_stars_is_video ? '📹 Video Message' : '' ) ),
                    'attachment' => $rock_stars_attachment_url,
                    'is_voice'   => $rock_stars_is_voice,
                    'is_video'   => $rock_stars_is_video,
                    'time'       => current_time( 'mysql' )
                )
            );
            update_post_meta( $rock_stars_post_id, '_rock_stars_chat_history', $rock_stars_history );
        }
    }

    // 6. Send Email only for new inquiries
    if ( ! $rock_stars_existing_ticket_id ) {
        $rock_stars_to      = get_option( 'admin_email' );
        $rock_stars_subject = 'New Chat Inquiry: ' . $rock_stars_name;
        $rock_stars_body    = "Name: $rock_stars_name\nEmail: $rock_stars_email\nQuestion:\n$rock_stars_message_text";
        wp_mail( $rock_stars_to, $rock_stars_subject, sanitize_textarea_field( $rock_stars_body ) );
    }

    return new WP_REST_Response( array( 
        'success' => true, 
        'message' => 'Message handled',
    ), 200 );
}
