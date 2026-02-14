<?php
/**
 * Register GraphQL fields and mutations for the Chat Widget.
 */

add_action( 'graphql_register_types', 'register_chat_graphql_logic' );

function register_chat_graphql_logic() {

    // --- Types ---

    register_graphql_object_type( 'ChatStatus', [
        'fields' => [
            'isOnline'    => [ 'type' => 'Boolean' ],
            'offlineMsg'  => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'ChatMessage', [
        'fields' => [
            'id'            => [ 'type' => 'ID' ], // Optional, usually index or generated
            'role'          => [ 'type' => 'String' ], // user, bot
            'text'          => [ 'type' => 'String' ],
            'attachmentUrl' => [ 'type' => 'String' ],
            'isVoice'       => [ 'type' => 'Boolean' ],
            'isVideo'       => [ 'type' => 'Boolean' ],
            'timestamp'     => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'ChatSessionHistory', [
        'fields' => [
            'sessionId' => [ 'type' => 'String' ],
            'messages'  => [ 'type' => [ 'list_of' => 'ChatMessage' ] ],
        ]
    ] );

    register_graphql_object_type( 'ChatMessageResponse', [
        'fields' => [
            'text'          => [ 'type' => 'String' ],
            'attachmentUrl' => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'SendChatMessagePayload', [
        'fields' => [
            'success'     => [ 'type' => 'Boolean' ],
            'message'     => [ 'type' => 'String' ],
            'sentMessage' => [ 'type' => 'ChatMessageResponse' ],
        ]
    ] );


    // --- Queries ---

    register_graphql_field( 'RootQuery', 'chatStatus', [
        'type' => 'ChatStatus',
        'resolve' => function() {
            if ( ! function_exists( 'carbon_get_theme_option' ) ) return null;
            return [
                'isOnline'   => carbon_get_theme_option( 'chat_online_status' ) === 'yes' || carbon_get_theme_option( 'chat_online_status' ) === true,
                'offlineMsg' => carbon_get_theme_option( 'chat_offline_message' ),
            ];
        }
    ] );

    register_graphql_field( 'RootQuery', 'chatHistory', [
        'type' => 'ChatSessionHistory',
        'args' => [
            'sessionId' => [ 'type' => 'String' ],
        ],
        'resolve' => function( $source, $args, $context, $info ) {
            $session_id = $args['sessionId'] ?? '';
            if ( empty( $session_id ) ) return null;

            $tickets = get_posts( array(
                'post_type' => 'ticket',
                'meta_key'  => '_chat_session_id',
                'meta_value' => $session_id,
                'posts_per_page' => 1,
                'post_status' => 'any',
                'orderby' => 'date',
                'order' => 'DESC'
            ) );

            $messages = [];
            if ( ! empty( $tickets ) ) {
                $raw_history = get_post_meta( $tickets[0]->ID, '_chat_history', true ) ?: [];
                
                // URL Resolving logic for Proxy/Ngrok/Ports (aligned with chat-cpt.php)
                $current_host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '';
                if (strpos($current_host, ',') !== false) {
                    $parts = explode(',', $current_host);
                    $current_host = trim($parts[0]);
                }
                $current_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http' );
                
                $site_url = get_site_url();
                $url_parts = parse_url($site_url);
                $old_authority = isset($url_parts['host']) ? $url_parts['host'] . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '') : '';
                $old_proto = $url_parts['scheme'] ?? 'http';

                foreach ( $raw_history as $index => $msg ) {
                    $text = $msg['text'] ?? '';
                    $attachment = $msg['attachment'] ?? '';

                    if ($current_host && $old_authority) {
                        // Fix text links
                        $text = str_replace($old_authority, $current_host, $text);
                        if ($old_proto !== $current_proto) {
                            $text = str_replace($old_proto . '://' . $current_host, $current_proto . '://' . $current_host, $text);
                        }

                        // Fix attachment links
                        if ($attachment) {
                            $attachment = str_replace($old_authority, $current_host, $attachment);
                            if ($old_proto !== $current_proto) {
                                $attachment = str_replace($old_proto . '://' . $current_host, $current_proto . '://' . $current_host, $attachment);
                            }
                            if (function_exists('set_url_scheme')) {
                                $attachment = set_url_scheme($attachment, $current_proto);
                            }
                        }
                    }

                    $messages[] = [
                        'id'            => (string)$index,
                        'role'          => $msg['role'] ?? '',
                        'text'          => $text,
                        'attachmentUrl' => $attachment,
                        'isVoice'       => !empty($msg['is_voice']),
                        'isVideo'       => !empty($msg['is_video']),
                        'timestamp'     => $msg['time'] ?? '',
                    ];
                }
            }

            return [
                'sessionId' => $session_id,
                'messages'  => $messages,
            ];
        }
    ] );

    // --- Mutation ---

    register_graphql_mutation( 'sendChatMessage', [
        'inputFields' => [
            'sessionId'   => [ 'type' => 'String' ],
            'name'        => [ 'type' => 'String' ],
            'email'       => [ 'type' => 'String' ],
            'message'     => [ 'type' => 'String' ],
            'fileBase64'  => [ 'type' => 'String', 'description' => 'Base64 encoded file content' ],
            'fileName'    => [ 'type' => 'String', 'description' => 'Filename with extension' ],
            'isVoice'     => [ 'type' => 'Boolean' ],
            'isVideo'     => [ 'type' => 'Boolean' ],
            'duration'    => [ 'type' => 'Int' ],
        ],
        'outputFields' => [
            'success'     => [ 'type' => 'Boolean' ],
            'message'     => [ 'type' => 'String' ],
            'sentMessage' => [ 'type' => 'ChatMessageResponse' ],
        ],
        'mutateAndGetPayload' => function( $input, $context, $info ) {
            // Validate inputs
            $session_id = sanitize_text_field( $input['sessionId'] ?? '' );
            if ( empty( $session_id ) ) {
                return [ 'success' => false, 'message' => 'Session ID is required' ];
            }

            $name    = sanitize_text_field( $input['name'] ?? '' ) ?: 'Visitor';
            $email   = sanitize_email( $input['email'] ?? '' ) ?: 'no-email@provided.com';
            $message_text = sanitize_textarea_field( $input['message'] ?? '' );
            $is_voice = !empty( $input['isVoice'] );
            $is_video = !empty( $input['isVideo'] );
            $duration = intval( $input['duration'] ?? 0 );

            // Handle File Upload
            $attachment_url = '';
            $local_path = '';
            $file_base64 = $input['fileBase64'] ?? '';
            $file_name   = $input['fileName'] ?? '';

            if ( ! empty( $file_base64 ) && ! empty( $file_name ) ) {
                // Check if base64 contains header data "data:image/png;base64," and strip it
                if ( strpos( $file_base64, ';base64,' ) !== false ) {
                    $exploded = explode( ';base64,', $file_base64 );
                    $file_base64 = isset( $exploded[1] ) ? $exploded[1] : '';
                }
                
                $decoded_file = base64_decode( $file_base64 );
                if ( $decoded_file !== false ) {
                     if ( ! function_exists( 'wp_handle_upload' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }
                    
                    // Validate size (approx check)
                    $size = strlen($decoded_file);
                    if ($size > 50 * 1024 * 1024) { // 50MB limit
                         return [ 'success' => false, 'message' => 'File too large (Max 50MB)' ];
                    }

                    $upload = wp_upload_bits( $file_name, null, $decoded_file );
                    if ( $upload && ! $upload['error'] ) {
                        $attachment_url = $upload['url'];
                        $local_path = $upload['file'];
                    } else {
                         return [ 'success' => false, 'message' => 'Upload failed: ' . ($upload['error'] ?? 'Unknown error') ];
                    }
                }
            }

            if ( empty( $message_text ) && !$attachment_url ) {
                return [ 'success' => false, 'message' => 'Message and file are empty' ];
            }

            // --- Logic from chat-cpt.php replicated ---
            
            // 3. Find existing ticket
            $existing_ticket_id = null;
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

            // 4. Send Telegram
            // We need to require chat-bot-handler.php if not already (it usually is)
            // Assuming rock_stars_send_to_telegram exists globally.
            
            $tg_text = $existing_ticket_id ? "💬 *New Message*\n" : "📩 *New Chat Inquiry*\n";
            $tg_text .= "👤 *$name*\n";
            if ($email !== 'no-email@provided.com') $tg_text .= "📧 $email\n";
            if ($message_text) $tg_text .= "\n❓ " . $message_text;
            if ( $existing_ticket_id ) $tg_text .= "\n\n_Continues existing thread_";

            if ($attachment_url) {
                $prefix = $is_voice ? "🎤 Voice Message" : ($is_video ? "📹 Video Message" : "📎 [Attached File]");
                $tg_text .= "\n\n" . $prefix . ($is_video || $is_voice ? "" : "(" . $attachment_url . ")");
            }

            $tg_message_id = null;
            
            if ( function_exists('rock_stars_send_to_telegram') ) {
                if ( $local_path ) {
                     // Check size for Telegram
                     $filesize = file_exists($local_path) ? filesize($local_path) : 0;
                     if ( $filesize > 49 * 1024 * 1024 ) {
                         $tg_text .= "\n(File > 50MB, sent as link)";
                         $tg_response = rock_stars_send_to_telegram( $tg_text );
                     } else {
                        if ( $is_voice ) {
                            $tg_response = rock_stars_send_voice_to_telegram( $local_path, $tg_text, $duration );
                        } elseif ( $is_video ) {
                            $tg_response = rock_stars_send_video_to_telegram( $local_path, $tg_text, $duration );
                        } else {
                            $tg_response = rock_stars_send_file_to_telegram( $local_path, $tg_text );
                        }
                     }
                } else {
                    $tg_response = rock_stars_send_to_telegram( $tg_text );
                }
                $tg_message_id = $tg_response['result']['message_id'] ?? null;
            }

            // 5. Create or Update Ticket
             $history_text = $message_text ?: ($is_voice ? "🎤 Voice Message" : ($is_video ? "📹 Video Message" : "📎 Attached File"));
            if ($attachment_url) {
                $prefix = $is_voice ? "🎤 Voice: " : ($is_video ? "📹 Video: " : "📎 File: ");
                $history_text .= ($message_text ? "\n\n" : "") . $prefix . "<a href='$attachment_url' target='_blank'>" . basename($attachment_url) . "</a>";
            }

            if ( $existing_ticket_id ) {
                $post_id = $existing_ticket_id;
                if ($tg_message_id) add_post_meta( $post_id, '_tg_message_id', $tg_message_id );

                // Reply Button
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
                $post_id = wp_insert_post( array(
                    'post_title'   => 'Chat Inquiry: ' . $name . ' (' . ( $session_id ?: 'no session' ) . ')',
                    'post_content' => $history_text,
                    'post_status'  => 'publish',
                    'post_type'    => 'ticket',
                    'post_author'  => 1 
                ) );
                
                if ( $post_id ) {
                    update_post_meta( $post_id, '_chat_session_id', $session_id );
                    update_post_meta( $post_id, '_wp_custom_sender_name', $name );
                    update_post_meta( $post_id, '_wp_custom_sender_email', $email );
                    if ($tg_message_id) update_post_meta( $post_id, '_tg_message_id', $tg_message_id );
                    
                    $history = array();
                    $history[] = array(
                        'role' => 'user',
                        'text' => $message_text ?: ($is_voice ? '🎤 Voice Message' : ($is_video ? '📹 Video Message' : '')),
                        'attachment' => $attachment_url,
                        'is_voice' => $is_voice,
                        'is_video' => $is_video,
                        'time' => current_time( 'mysql' )
                    );
                    update_post_meta( $post_id, '_chat_history', $history );
                }
            }
            
            // 6. Send Email only for new inquiries (aligned with chat-cpt.php)
            if ( ! $existing_ticket_id ) {
                $to = get_option( 'admin_email' );
                $subject = 'New Chat Inquiry (via GraphQL): ' . $name;
                $body = "Name: $name\nEmail: $email\nQuestion:\n$message_text";
                wp_mail( $to, $subject, $body );
            }

            return [
                'success' => true,
                'message' => 'Message sent',
                'sentMessage' => [
                    'text' => $message_text,
                    'attachmentUrl' => $attachment_url,
                ]
            ];
        }
    ] );
}
