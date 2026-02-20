<?php
/**
 * Telegram Webhook Management
 * Handles manual setup and auto-updates on domain change.
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * 1. Auto-update Webhook on Domain Change
 * Checks on admin_init if the current site URL matches the last registered webhook URL.
 */
add_action( 'admin_init', 'rock_stars_auto_update_webhook' );
function rock_stars_auto_update_webhook() {
    // Only run for admins
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $rock_stars_token = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
    if ( ! $rock_stars_token ) {
        return;
    }

    $rock_stars_current_url = get_rest_url( null, 'qa/v1/webhook' );
    
    // Check if we are on localhost
    $rock_stars_is_localhost = ( strpos( $rock_stars_current_url, 'localhost' ) !== false || strpos( $rock_stars_current_url, '127.0.0.1' ) !== false );

    // If on localhost, do NOT auto-update (Telegram can't reach us anyway)
    if ( $rock_stars_is_localhost ) {
        return;
    }

    // Get last registered URL
    $rock_stars_registered_url = get_option( '_rock_stars_chat_registered_webhook_url' );

    // If URLs are different, update Telegram
    if ( $rock_stars_registered_url !== $rock_stars_current_url ) {
        $rock_stars_api_url  = "https://api.telegram.org/bot{$rock_stars_token}/setWebhook?url=" . urlencode( $rock_stars_current_url );
        $rock_stars_response = wp_remote_get( $rock_stars_api_url );
        
        if ( ! is_wp_error( $rock_stars_response ) ) {
            $rock_stars_body = json_decode( wp_remote_retrieve_body( $rock_stars_response ), true );
            
            if ( isset( $rock_stars_body['ok'] ) && $rock_stars_body['ok'] ) {
                // Success! Save new URL
                update_option( '_rock_stars_chat_registered_webhook_url', $rock_stars_current_url );
                
                // Show admin notice
                add_action( 'admin_notices', function() use ( $rock_stars_current_url ) {
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><strong><?php esc_html_e( 'Telegram Webhook Updated!', 'rock-stars' ); ?></strong> <?php printf( esc_html__( 'Site URL changed, so we automatically updated the bot webhook to: %s', 'rock-stars' ), '<code>' . esc_html( $rock_stars_current_url ) . '</code>' ); ?></p>
                    </div>
                    <?php
                } );
            }
        }
    }
}

/**
 * 2. Manual Webhook Setup Trigger (Legacy Support)
 * Usage: Visit yoursite/wp-admin/?setup_bot=1 (must be admin)
 */
add_action( 'init', 'rock_stars_setup_webhook_trigger_manual' );
function rock_stars_setup_webhook_trigger_manual() {
    if ( isset( $_GET['setup_bot'] ) && current_user_can( 'manage_options' ) ) {
        $rock_stars_token = carbon_get_theme_option( 'rock_stars_chat_bot_token' );
        if ( ! $rock_stars_token ) {
            wp_die( esc_html__( 'Error: Bot Token not set in Theme Options.', 'rock-stars' ) );
        }

        $rock_stars_webhook_url = get_rest_url( null, 'qa/v1/webhook' );
        
        // Telegram API
        $rock_stars_url = "https://api.telegram.org/bot{$rock_stars_token}/setWebhook?url=" . urlencode( $rock_stars_webhook_url );
        
        $rock_stars_response = wp_remote_get( $rock_stars_url );
        $rock_stars_body     = wp_remote_retrieve_body( $rock_stars_response );
        
        // Update option manually too
        update_option( '_rock_stars_chat_registered_webhook_url', $rock_stars_webhook_url );

        echo "<h1>" . esc_html__( 'Telegram Webhook Setup', 'rock-stars' ) . "</h1>";
        echo "<p><strong>" . esc_html__( 'Webhook URL:', 'rock-stars' ) . "</strong> " . esc_html( $rock_stars_webhook_url ) . "</p>";
        echo "<p><strong>" . esc_html__( 'Result:', 'rock-stars' ) . "</strong> " . esc_html( $rock_stars_body ) . "</p>";
        echo "<p><em>" . esc_html__( 'Saved to database for auto-detect feature.', 'rock-stars' ) . "</em></p>";
        exit;
    }
}
