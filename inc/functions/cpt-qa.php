<?php
/**
 * Register Custom Post Type for Q&A (Help Widget)
 */

function register_qa_cpt() {
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
        'supports'              => array( 'title', 'editor', 'excerpt' ), // title = вопрос, editor = ответ.
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => false, // Не создаем публичные страницы (single-qa.php не нужен)
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-format-chat',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true, // Исключаем из обычного поиска по сайту
        'publicly_queryable'    => false, // Нельза открыть по прямой ссылке
        'show_in_rest'          => true, // ВАЖНО: Включаем REST API для SPA и JS-виджета
        'show_in_graphql'       => true, // ВАЖНО: Включаем GraphQL для будущего SPA
        'graphql_single_name'   => 'qaItem',
        'graphql_plural_name'   => 'qaItems',
        'rest_base'             => 'qa', // URL будет /wp-json/wp/v2/qa
    );

    register_post_type( 'qa', $args );
}
add_action( 'init', 'register_qa_cpt', 0 );

/**
 * Register Custom REST Route for Contact Form
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'qa/v1', '/contact', array(
        'methods' => 'POST',
        'callback' => 'handle_qa_contact_submission',
        'permission_callback' => '__return_true', // Open for public use (add nonce check logic in callback if needed)
    ) );
} );

function handle_qa_contact_submission( $request ) {
    $params = $request->get_json_params();
    
    // 1. Anti-Bot: Honeypot Check
    if ( !empty( $params['website_url'] ) ) {
        // Pretend it worked to confuse bots
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Email sent' ), 200 );
    }

    // 2. Validate Inputs
    $name    = sanitize_text_field( $params['name'] ?? '' );
    $email   = sanitize_email( $params['email'] ?? '' );
    $message_text = sanitize_textarea_field( $params['message'] ?? '' );

    if ( empty( $name ) || empty( $email ) || empty( $message_text ) ) {
        return new WP_Error( 'missing_params', 'Please fill in required fields', array( 'status' => 400 ) );
    }

    if ( !is_email( $email ) ) {
        return new WP_Error( 'invalid_email', 'Invalid email address', array( 'status' => 400 ) );
    }

    // 3. Prepare Content
    $full_message_body = "You received a new question from the Chat Widget:\n\n";
    $full_message_body .= "Name: $name\n";
    $full_message_body .= "Email: $email\n";
    $full_message_body .= "------------------------\n";
    $full_message_body .= "Question:\n$message_text\n";
    $full_message_body .= "------------------------\n";
    $full_message_body .= "Sent via Rock-Stars Chat Widget";

    $to = get_option( 'admin_email' );
    $subject = 'New Question: ' . $name;
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    if($email) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }

    // 4. Send Email
    $sent = wp_mail( $to, $subject, $full_message_body, $headers );

    // 5. Send Telegram (Fire & Forget)
    $token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
    $chat_id = '422713968';
    
    // Truncate message for TG if too long
    $tg_text = "📩 *New Chat Inquiry*\n\n";
    $tg_text .= "👤 *$name*\n";
    $tg_text .= "📧 $email\n";
    $tg_text .= "\n❓ " . $message_text;

    wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
        'body' => array(
            'chat_id' => $chat_id,
            'text'    => $tg_text,
            'parse_mode' => 'Markdown'
        ),
        'timeout' => 5,
        'blocking' => false,
    ) );

    if ( $sent ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Email sent' ), 200 );
    } else {
        return new WP_Error( 'email_failed', 'Failed to send email', array( 'status' => 500 ) );
    }
}
