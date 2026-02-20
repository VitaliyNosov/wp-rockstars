<?php
/**
 * Register GraphQL fields and mutations for the Chat Widget.
 */

add_action( 'graphql_register_types', 'rock_stars_register_chat_graphql_logic' );

function rock_stars_register_chat_graphql_logic() {

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
                'isOnline'   => carbon_get_theme_option( 'rock_stars_chat_online_status' ) === 'yes' || carbon_get_theme_option( 'rock_stars_chat_online_status' ) === true,
                'offlineMsg' => carbon_get_theme_option( 'rock_stars_chat_offline_message' ),
            ];
        }
    ] );

    register_graphql_field( 'RootQuery', 'chatHistory', [
        'type' => 'ChatSessionHistory',
        'args' => [
            'sessionId' => [ 'type' => 'String' ],
        ],
        'resolve' => function( $source, $args, $context, $info ) {
            $rock_stars_session_id = $args['sessionId'] ?? '';
            if ( empty( $rock_stars_session_id ) ) return null;

            $rock_stars_tickets = get_posts( array(
                'post_type'      => 'rock_stars_ticket',
                'meta_key'       => '_rock_stars_chat_session_id',
                'meta_value'     => $rock_stars_session_id,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'orderby'        => 'date',
                'order'          => 'DESC'
            ) );

            $rock_stars_messages = [];
            if ( ! empty( $rock_stars_tickets ) ) {
                $rock_stars_raw_history = get_post_meta( $rock_stars_tickets[0]->ID, '_rock_stars_chat_history', true ) ?: [];
                
                // URL Resolving logic for Proxy/Ngrok/Ports (aligned with chat-cpt.php)
                $rock_stars_current_host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '';
                if (strpos($current_host, ',') !== false) {
                    $parts = explode(',', $current_host);
                    $current_host = trim($parts[0]);
                }
                $current_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http' );
                
                $site_url = get_site_url();
                $url_parts = parse_url($site_url);
                $old_authority = isset($url_parts['host']) ? $url_parts['host'] . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '') : '';
                $old_proto = $url_parts['scheme'] ?? 'http';

                foreach ( $rock_stars_raw_history as $rock_stars_index => $rock_stars_msg ) {
                    $rock_stars_text       = $rock_stars_msg['text'] ?? '';
                    $rock_stars_attachment = $rock_stars_msg['attachment'] ?? '';

                    if ( $rock_stars_current_host && $rock_stars_old_authority ) {
                        // Fix text links
                        $rock_stars_text = str_replace( $rock_stars_old_authority, $rock_stars_current_host, $rock_stars_text );
                        if ( $rock_stars_old_proto !== $rock_stars_current_proto ) {
                            $rock_stars_text = str_replace( $rock_stars_old_proto . '://' . $rock_stars_current_host, $rock_stars_current_proto . '://' . $rock_stars_current_host, $rock_stars_text );
                        }

                        // Fix attachment links
                        if ( $rock_stars_attachment ) {
                            $rock_stars_attachment = str_replace( $rock_stars_old_authority, $rock_stars_current_host, $rock_stars_attachment );
                            if ( $rock_stars_old_proto !== $rock_stars_current_proto ) {
                                $rock_stars_attachment = str_replace( $rock_stars_old_proto . '://' . $rock_stars_current_host, $rock_stars_current_proto . '://' . $rock_stars_current_host, $rock_stars_attachment );
                            }
                            if ( function_exists( 'set_url_scheme' ) ) {
                                $rock_stars_attachment = set_url_scheme( $rock_stars_attachment, $rock_stars_current_proto );
                            }
                        }
                    }

                    $rock_stars_messages[] = [
                        'id'            => (string) $rock_stars_index,
                        'role'          => $rock_stars_msg['role'] ?? '',
                        'text'          => $rock_stars_text,
                        'attachmentUrl' => $rock_stars_attachment,
                        'isVoice'       => ! empty( $rock_stars_msg['is_voice'] ),
                        'isVideo'       => ! empty( $rock_stars_msg['is_video'] ),
                        'timestamp'     => $rock_stars_msg['time'] ?? '',
                    ];
                }
            }

            return [
                'sessionId' => $rock_stars_session_id,
                'messages'  => $rock_stars_messages,
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
        'mutateAndGetPayload' => function( $rock_stars_input, $context, $info ) {
            // Validate inputs
            $rock_stars_session_id = sanitize_text_field( $rock_stars_input['sessionId'] ?? '' );
            if ( empty( $rock_stars_session_id ) ) {
                return [ 'success' => false, 'message' => 'Session ID is required' ];
            }

            $rock_stars_name         = sanitize_text_field( $rock_stars_input['name'] ?? '' ) ?: 'Visitor';
            $rock_stars_email        = sanitize_email( $rock_stars_input['email'] ?? '' ) ?: 'no-email@provided.com';
            $rock_stars_message_text = sanitize_textarea_field( $rock_stars_input['message'] ?? '' );
            $rock_stars_is_voice     = ! empty( $rock_stars_input['isVoice'] );
            $rock_stars_is_video     = ! empty( $rock_stars_input['isVideo'] );
            $rock_stars_duration     = intval( $rock_stars_input['duration'] ?? 0 );

            // Handle File Upload
            $rock_stars_attachment_url = '';
            $rock_stars_local_path     = '';
            $rock_stars_file_base64    = $rock_stars_input['fileBase64'] ?? '';
            $rock_stars_file_name      = sanitize_file_name( $rock_stars_input['fileName'] ?? '' );

            if ( ! empty( $rock_stars_file_base64 ) && ! empty( $rock_stars_file_name ) ) {
                // Check if base64 contains header data "data:image/png;base64," and strip it
                if ( strpos( $rock_stars_file_base64, ';base64,' ) !== false ) {
                    $rock_stars_exploded    = explode( ';base64,', $rock_stars_file_base64 );
                    $rock_stars_file_base64 = isset( $rock_stars_exploded[1] ) ? $rock_stars_exploded[1] : '';
                }
                
                $rock_stars_decoded_file = base64_decode( $rock_stars_file_base64 );
                if ( $rock_stars_decoded_file !== false ) {
                     if ( ! function_exists( 'wp_handle_upload' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }
                    
                    // Validate size (approx check)
                    $rock_stars_size = strlen( $rock_stars_decoded_file );
                    if ( $rock_stars_size > 50 * 1024 * 1024 ) { // 50MB limit
                         return [ 'success' => false, 'message' => 'File too large (Max 50MB)' ];
                    }

                    $rock_stars_upload = wp_upload_bits( $rock_stars_file_name, null, $rock_stars_decoded_file );
                    if ( $rock_stars_upload && ! $rock_stars_upload['error'] ) {
                        $rock_stars_attachment_url = $rock_stars_upload['url'];
                        $rock_stars_local_path     = $rock_stars_upload['file'];
                    } else {
                         return [ 'success' => false, 'message' => 'Upload failed: ' . ( $rock_stars_upload['error'] ?? 'Unknown error' ) ];
                    }
                }
            }

            if ( empty( $rock_stars_message_text ) && ! $rock_stars_attachment_url ) {
                return [ 'success' => false, 'message' => 'Message and file are empty' ];
            }

            // --- Logic from chat-cpt.php replicated ---
            
            // 3. Find existing ticket
            $rock_stars_existing_ticket_id = null;
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

            // 4. Send Telegram
            // We need to require chat-bot-handler.php if not already (it usually is)
            // Assuming rock_stars_send_to_telegram exists globally.
            
            $rock_stars_tg_text = $rock_stars_existing_ticket_id ? "💬 *New Message*\n" : "📩 *New Chat Inquiry*\n";
            $rock_stars_tg_text .= "👤 *$rock_stars_name*\n";
            if ( $rock_stars_email !== 'no-email@provided.com' ) {
                $rock_stars_tg_text .= "📧 $rock_stars_email\n";
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
            
            if ( function_exists('rock_stars_send_to_telegram') ) {
                if ( $rock_stars_local_path ) {
                     // Check size for Telegram
                     $rock_stars_filesize = file_exists( $rock_stars_local_path ) ? filesize( $rock_stars_local_path ) : 0;
                     if ( $rock_stars_filesize > 49 * 1024 * 1024 ) {
                         $rock_stars_tg_text .= "\n(File > 50MB, sent as link)";
                         $rock_stars_tg_response = rock_stars_send_to_telegram( $rock_stars_tg_text );
                     } else {
                        if ( $rock_stars_is_voice ) {
                            $rock_stars_tg_response = rock_stars_send_voice_to_telegram( $rock_stars_local_path, $rock_stars_tg_text, $rock_stars_duration );
                        } elseif ( $rock_stars_is_video ) {
                            $rock_stars_tg_response = rock_stars_send_video_to_telegram( $rock_stars_local_path, $rock_stars_tg_text, $rock_stars_duration );
                        } else {
                            $rock_stars_tg_response = rock_stars_send_file_to_telegram( $rock_stars_local_path, $rock_stars_tg_text );
                        }
                     }
                } else {
                    $rock_stars_tg_response = rock_stars_send_to_telegram( $rock_stars_tg_text );
                }
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

                // Reply Button
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
                $rock_stars_post_id = wp_insert_post( array(
                    'post_title'   => 'Chat Inquiry: ' . $rock_stars_name . ' (' . ( $rock_stars_session_id ?: 'no session' ) . ')',
                    'post_content' => $rock_stars_history_text,
                    'post_status'  => 'publish',
                    'post_type'    => 'rock_stars_ticket',
                    'post_author'  => 1 
                ) );
                
                if ( $rock_stars_post_id && ! is_wp_error( $rock_stars_post_id ) ) {
                    update_post_meta( $rock_stars_post_id, '_rock_stars_chat_session_id', $rock_stars_session_id );
                    update_post_meta( $rock_stars_post_id, '_rock_stars_custom_sender_name', $rock_stars_name );
                    update_post_meta( $rock_stars_post_id, '_rock_stars_custom_sender_email', $rock_stars_email );
                    if ( $rock_stars_tg_message_id ) {
                        update_post_meta( $rock_stars_post_id, '_rock_stars_tg_message_id', $rock_stars_tg_message_id );
                    }
                    
                    $rock_stars_history = array();
                    $rock_stars_history[] = array(
                        'role'       => 'user',
                        'text'       => $rock_stars_message_text ?: ( $rock_stars_is_voice ? '🎤 Voice Message' : ( $rock_stars_is_video ? '📹 Video Message' : '' ) ),
                        'attachment' => $rock_stars_attachment_url,
                        'is_voice'   => $rock_stars_is_voice,
                        'is_video'   => $rock_stars_is_video,
                        'time'       => current_time( 'mysql' )
                    );
                    update_post_meta( $rock_stars_post_id, '_rock_stars_chat_history', $rock_stars_history );
                }
            }
            
            // 6. Send Email only for new inquiries (aligned with chat-cpt.php)
            if ( ! $rock_stars_existing_ticket_id ) {
                $rock_stars_to      = get_option( 'admin_email' );
                $rock_stars_subject = 'New Chat Inquiry (via GraphQL): ' . $rock_stars_name;
                $rock_stars_body    = "Name: $rock_stars_name\nEmail: $rock_stars_email\nQuestion:\n$rock_stars_message_text";
                wp_mail( $rock_stars_to, $rock_stars_subject, sanitize_textarea_field( $rock_stars_body ) );
            }

            return [
                'success'     => true,
                'message'     => 'Message sent',
                'sentMessage' => [
                    'text'          => $rock_stars_message_text,
                    'attachmentUrl' => $rock_stars_attachment_url,
                ]
            ];
        }
    ] );
}
