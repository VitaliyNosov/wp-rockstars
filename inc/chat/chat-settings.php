<?php
/**
 * Chat Module Settings (Carbon Fields)
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'rock_stars_register_chat_settings' );
function rock_stars_register_chat_settings() {
    Container::make( 'theme_options', __( 'Chat Settings', 'rock-star' ) )
        ->set_page_parent( 'edit.php?post_type=qa' ) // Nest under Q&A menu
        ->add_fields( array(
            Field::make( 'separator', 'crb_bot_sep', __( 'Telegram Bot Configuration', 'rock-star' ) ),
            
            Field::make( 'text', 'chat_bot_token', __( 'Telegram Bot Token', 'rock-star' ) )
                ->set_help_text( __( 'Enter the token from @BotFather', 'rock-star' ) ),
                
            Field::make( 'text', 'chat_admin_id', __( 'Admin Chat ID', 'rock-star' ) )
                ->set_help_text( __( 'Your Telegram user ID (use @userinfobot to find it)', 'rock-star' ) ),

            Field::make( 'separator', 'crb_status_sep', __( 'Chat Status & Availability', 'rock-star' ) ),

            Field::make( 'checkbox', 'chat_online_status', __( 'Is Online?', 'rock-star' ) )
                ->set_option_value( 'yes' )
                ->set_default_value( true )
                ->set_help_text( __( 'Toggle manually or use /online /offline commands in Telegram', 'rock-star' ) ),

            Field::make( 'textarea', 'chat_offline_message', __( 'Offline Greeting', 'rock-star' ) )
                ->set_default_value( __( 'We are currently offline. Feel free to search our knowledge base or leave a message.', 'rock-star' ) )
                ->set_rows(3),

            Field::make( 'separator', 'crb_audio_sep', __( 'Audio Settings', 'rock-star' ) ),

            Field::make( 'file', 'chat_sound_file', __( 'Custom Notification Sound', 'rock-star' ) )
                // Storing ID is standard and safer for retrieval
                ->set_type( array( 'audio' ) )
                ->set_help_text( __( 'Upload an MP3/WAV file. Plays when a new message arrives from admin.', 'rock-star' ) ),

            Field::make( 'text', 'chat_sound_url', __( 'Direct Sound URL', 'rock-star' ) )
                ->set_help_text( __( 'Overrides the uploaded file if set. Useful for external hosting.', 'rock-star' ) ),

            Field::make( 'separator', 'crb_webhook_sep', __( 'Technical Info', 'rock-star' ) ),
            
            Field::make( 'html', 'chat_webhook_info' )
                ->set_html( function() {
                    $webhook_url = get_rest_url( null, 'qa/v1/webhook' );
                    return '<p><strong>' . __( 'Webhook URL:', 'rock-star' ) . '</strong><br><code>' . esc_url( $webhook_url ) . '</code></p>' .
                           '<p><small>' . __( 'Note: This URL must be reachable by Telegram (no localhost).', 'rock-star' ) . '</small></p>';
                } ),
        ) );
}
