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

    $token = carbon_get_theme_option( 'chat_bot_token' );
    if ( ! $token ) {
        return;
    }

    $current_url = get_rest_url( null, 'qa/v1/webhook' );
    
    // Check if we are on localhost
    $is_localhost = ( strpos( $current_url, 'localhost' ) !== false || strpos( $current_url, '127.0.0.1' ) !== false );

    // If on localhost, do NOT auto-update (Telegram can't reach us anyway)
    if ( $is_localhost ) {
        return;
    }

    // Get last registered URL
    $registered_url = get_option( '_chat_registered_webhook_url' );

    // If URLs are different, update Telegram
    if ( $registered_url !== $current_url ) {
        $api_url = "https://api.telegram.org/bot{$token}/setWebhook?url=" . urlencode( $current_url );
        $response = wp_remote_get( $api_url );
        
        if ( ! is_wp_error( $response ) ) {
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            
            if ( isset( $body['ok'] ) && $body['ok'] ) {
                // Success! Save new URL
                update_option( '_chat_registered_webhook_url', $current_url );
                
                // Show admin notice
                add_action( 'admin_notices', function() use ( $current_url ) {
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><strong>Telegram Webhook Updated!</strong> Site URL changed, so we automatically updated the bot webhook to: <code><?php echo esc_html( $current_url ); ?></code></p>
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
        $token = carbon_get_theme_option( 'chat_bot_token' );
        if ( ! $token ) {
            wp_die( 'Error: Bot Token not set in Theme Options.' );
        }

        $webhook_url = get_rest_url( null, 'qa/v1/webhook' );
        
        // Telegram API
        $url = "https://api.telegram.org/bot{$token}/setWebhook?url=" . urlencode( $webhook_url );
        
        $response = wp_remote_get( $url );
        $body = wp_remote_retrieve_body( $response );
        
        // Update option manually too
        update_option( '_chat_registered_webhook_url', $webhook_url );

        echo "<h1>Telegram Webhook Setup</h1>";
        echo "<p><strong>Webhook URL:</strong> " . esc_html( $webhook_url ) . "</p>";
        echo "<p><strong>Result:</strong> " . esc_html( $body ) . "</p>";
        echo "<p><em>Saved to database for auto-detect feature.</em></p>";
        exit;
    }
}
