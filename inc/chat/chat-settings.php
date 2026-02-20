<?php
/**
 * Chat Module Settings (Carbon Fields)
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'rock_stars_register_chat_settings' );
function rock_stars_register_chat_settings() {
    Container::make( 'theme_options', __( 'Chat Settings', 'rock-stars' ) )
        ->set_page_parent( 'edit.php?post_type=rock_stars_qa' ) // Nest under Q&A menu
        ->add_fields( array(
            Field::make( 'separator', 'rock_stars_crb_bot_sep', __( 'Telegram Bot Configuration', 'rock-stars' ) ),
            
            Field::make( 'text', 'rock_stars_chat_bot_token', __( 'Telegram Bot Token', 'rock-stars' ) )
                ->set_help_text( __( 'Enter the token from @BotFather', 'rock-stars' ) ),
                
            Field::make( 'text', 'rock_stars_chat_admin_id', __( 'Admin Chat ID', 'rock-stars' ) )
                ->set_help_text( __( 'Your Telegram user ID (use @userinfobot to find it)', 'rock-stars' ) ),

            Field::make( 'separator', 'rock_stars_crb_status_sep', __( 'Chat Status & Availability', 'rock-stars' ) ),

            Field::make( 'checkbox', 'rock_stars_chat_online_status', __( 'Is Online?', 'rock-stars' ) )
                ->set_option_value( 'yes' )
                ->set_default_value( true )
                ->set_help_text( __( 'Toggle manually or use /online /offline commands in Telegram', 'rock-stars' ) ),

            Field::make( 'textarea', 'rock_stars_chat_offline_message', __( 'Offline Greeting', 'rock-stars' ) )
                ->set_default_value( __( 'We are currently offline. Feel free to search our knowledge base or leave a message.', 'rock-stars' ) )
                ->set_rows( 3 ),

            Field::make( 'separator', 'rock_stars_crb_audio_sep', __( 'Audio Settings', 'rock-stars' ) ),

            Field::make( 'file', 'rock_stars_chat_sound_file', __( 'Custom Notification Sound', 'rock-stars' ) )
                // Storing ID is standard and safer for retrieval
                ->set_type( array( 'audio' ) )
                ->set_help_text( __( 'Upload an MP3/WAV file. Plays when a new message arrives from admin.', 'rock-stars' ) ),

            Field::make( 'text', 'rock_stars_chat_sound_url', __( 'Direct Sound URL', 'rock-stars' ) )
                ->set_help_text( __( 'Overrides the uploaded file if set. Useful for external hosting.', 'rock-stars' ) ),

            Field::make( 'separator', 'rock_stars_crb_webhook_sep', __( 'Technical Info', 'rock-stars' ) ),
            
            Field::make( 'html', 'rock_stars_chat_webhook_info' )
                ->set_html( function() {
                    $rock_stars_webhook_url = get_rest_url( null, 'qa/v1/webhook' );
                    return '<p><strong>' . __( 'Webhook URL:', 'rock-stars' ) . '</strong><br><code>' . esc_url( $rock_stars_webhook_url ) . '</code></p>' .
                           '<p><small>' . __( 'Note: This URL must be reachable by Telegram (no localhost).', 'rock-stars' ) . '</small></p>';
                } ),
        ) );
}
