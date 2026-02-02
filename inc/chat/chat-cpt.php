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
    
    // DEBUG: Log polling
    // error_log( "Polling history for session: $session_id" );

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
        // error_log( "Polling: No tickets found for session $session_id" );
        return array();
    }

    $history = get_post_meta( $tickets[0]->ID, '_chat_history', true ) ?: array();
    

    return $history;
}

function handle_qa_contact_submission( $request ) {
    $params = $request->get_json_params();
    
    // 1. Anti-Bot: Honeypot Check
    if ( !empty( $params['website_url'] ) ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Email sent' ), 200 );
    }

    // 2. Validate Inputs
    $session_id = sanitize_text_field( $params['session_id'] ?? '' );
    $name    = sanitize_text_field( $params['name'] ?? '' ) ?: 'Visitor';
    $email   = sanitize_email( $params['email'] ?? '' ) ?: 'no-email@provided.com';
    $message_text = sanitize_textarea_field( $params['message'] ?? '' );

    if ( empty( $message_text ) ) {
        return new WP_Error( 'missing_params', 'Message is empty', array( 'status' => 400 ) );
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
    $is_online = carbon_get_theme_option( 'chat_online_status' ) === 'yes' || carbon_get_theme_option( 'chat_online_status' ) === true;
    
    $tg_text = $existing_ticket_id ? "💬 *New Message in Chat*\n" : "📩 *New Chat Inquiry*\n";
    $tg_text .= "👤 *$name*\n";
    if ($email !== 'no-email@provided.com') $tg_text .= "📧 $email\n";
    $tg_text .= "\n❓ " . $message_text;
    
    if ( $existing_ticket_id ) {
        $tg_text .= "\n\n_Continues existing thread_";
    }

    // Add "Answer" button
    $keyboard = null;
    $post_id_for_tg = $existing_ticket_id ?: 0; // Temporarily 0 if new, will update after insert

    $tg_response = rock_stars_send_to_telegram( $tg_text );
    $tg_message_id = $tg_response['result']['message_id'] ?? null;

    // 5. Create or Update Ticket
    if ( $existing_ticket_id ) {
        $post_id = $existing_ticket_id;
        
        // Store the user message ID so we can match replies
        if ($tg_message_id) add_post_meta( $post_id, '_tg_message_id', $tg_message_id );

        // Add button to the message we just sent (Editing it)
        $token = carbon_get_theme_option( 'chat_bot_token' );
        if ($tg_message_id && $token) {
            $btn_keyboard = array(
                'inline_keyboard' => array(
                    array(
                        array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $post_id )
                    )
                )
            );
            wp_remote_post( "https://api.telegram.org/bot{$token}/editMessageReplyMarkup", array(
                'body' => array(
                    'chat_id' => carbon_get_theme_option( 'chat_admin_id' ),
                    'message_id' => $tg_message_id,
                    'reply_markup' => json_encode( $btn_keyboard )
                )
            ) );
        }
        
        // Append to content
        $current_content = get_post_field( 'post_content', $post_id );
        wp_update_post( array(
            'ID' => $post_id,
            'post_content' => $current_content . "\n\n---\n" . $message_text
        ) );

        // Update History
        $history = get_post_meta( $post_id, '_chat_history', true ) ?: array();
        $history[] = array(
            'role' => 'user',
            'text' => $message_text,
            'time' => current_time( 'mysql' )
        );
        update_post_meta( $post_id, '_chat_history', $history );
        
    } else {
        $post_data = array(
            'post_title'   => 'Chat Inquiry: ' . $name . ' (' . ( $session_id ?: 'no session' ) . ')',
            'post_content' => $message_text,
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
            
            // Add button to the first message (Editing it)
            $token = carbon_get_theme_option( 'chat_bot_token' );
            if ($tg_message_id && $token) {
                $btn_keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array( 'text' => '✍️ Ответить', 'callback_data' => 'reply:' . $post_id )
                        )
                    )
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
                    'text' => $message_text,
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

    return new WP_REST_Response( array( 'success' => true, 'message' => 'Message handled' ), 200 );
}
