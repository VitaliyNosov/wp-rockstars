<?php
/**
 * Chat Module Initialization
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// 1. Carbon Fields Settings
require_once __DIR__ . '/chat-settings.php';

// 2. Register Custom Post Type and REST API 
require_once __DIR__ . '/chat-cpt.php';

// 3. Bot Logic Handler
require_once __DIR__ . '/chat-bot-handler.php';

// 4. Render Chat Widget HTML in footer
require_once __DIR__ . '/chat-widget.php';

/**
 * Enqueue Chat Styles
 */
add_action( 'wp_enqueue_scripts', 'rock_stars_chat_enqueue_assets' );
function rock_stars_chat_enqueue_assets() {
    // Styles
    wp_enqueue_style( 'rock-stars-chat-widget', get_theme_file_uri( '/inc/chat/css/chat-widget.css' ), array(), '1.0.0' );

    // Scripts
    wp_enqueue_script( 'rock-stars-chat-widget-js', get_theme_file_uri( '/inc/chat/js/chat-widget.js' ), array(), '1.0.0', true );

    // Configuration for JS
    $config = array(
        'apiUrl'  => wp_make_link_relative( get_rest_url( null, 'wp/v2/qa' ) ),
        'siteUrl' => wp_make_link_relative( get_site_url() ),
        'root'    => wp_make_link_relative( rest_url() ),
        'nonce'   => wp_create_nonce( 'wp_rest' )
    );

    // Resolving Sound URL
    if ( function_exists( 'carbon_get_theme_option' ) ) {
        $sound_url = carbon_get_theme_option('chat_sound_url');
        if (!$sound_url) {
            $sound_id = carbon_get_theme_option('chat_sound_file');
            if ($sound_id) {
                $sound_url = wp_get_attachment_url($sound_id);
            }
        }
        
        if ($sound_url) {
            $config['notificationSound'] = wp_make_link_relative($sound_url);
        }
    }

    wp_localize_script( 'rock-stars-chat-widget-js', 'rockStarsChatConfig', $config );
}
