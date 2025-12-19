<?php

// AJAX handler for subscription form submission

function wp_custom_handle_subscribe_submission() {
    // Debug logging
    error_log('WP Custom Subscribe: AJAX handler called');
    
    // Check if nonce exists
    if (!isset($_POST['nonce'])) {
        wp_send_json_error('No nonce provided');
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'wp_custom_subscribe_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Check if required fields exist
    if (!isset($_POST['email'])) {
        wp_send_json_error('Email is required');
    }
    
    // Sanitize input data
    $email = sanitize_email($_POST['email']);
    
    // Validate data
    if (!is_email($email)) {
        wp_send_json_error('Invalid email address');
    }
    
    // Create ticket post (reusing Ticket system for unified notifications)
    $post_data = array(
        'post_title' => 'New Subscriber: ' . $email,
        'post_content' => 'New subscription request from landing page.',
        'post_status' => 'publish',
        'post_type' => 'ticket',
        'post_author' => 1
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        error_log('WP Custom Subscribe: Failed to create post - ' . $post_id->get_error_message());
        wp_send_json_error('Failed to subscribe');
    }
    
    // Add meta data
    // We use the same keys as the contact form to trigger the existing Telegram logic
    update_post_meta($post_id, '_wp_custom_sender_name', 'Subscriber'); 
    update_post_meta($post_id, '_wp_custom_sender_email', $email);
    update_post_meta($post_id, '_wp_custom_message', 'User subscribed via landing page footer form.');
    update_post_meta($post_id, '_wp_custom_submission_time', current_time('mysql'));
    update_post_meta($post_id, '_wp_custom_ip_address', $_SERVER['REMOTE_ADDR']);
    update_post_meta($post_id, '_wp_custom_type', 'subscription');
    
    error_log('WP Custom Subscribe: Successfully created ticket with ID: ' . $post_id);

    // --- SEND AUTO-REPLY EMAIL START ---
    $to = $email;
    $subject = 'You are subscribed! | ' . get_bloginfo('name');
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Define variables for template
    $name = 'Subscriber'; // Default name

    // Load template
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>You are subscribed!</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #000000; color: #ffffff;">
        <table role="presentation" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 40px 0; text-align: center; background: radial-gradient(circle at 50% 50%, #1D2144 0%, #090E34 100%);">
                    <div style="max-width: 600px; margin: 0 auto; background-color: #1F2024; border: 1px solid #2E3038; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        <!-- Header with Logo -->
                        <div style="background-color: #1F2024; padding: 30px 20px; text-align: center; border-bottom: 1px solid #2E3038;">
                            <img src="cid:company-logo" alt="<?php echo get_bloginfo('name'); ?>" style="max-width: 150px; height: auto;">
                        </div>
                        
                        <!-- Content -->
                        <div style="padding: 40px 30px; text-align: left; color: #d1d5db; background-color: #060607;">
                            <h1 style="color: #ffffff; margin-top: 0; margin-bottom: 20px; font-size: 24px;">Subscription Confirmed!</h1>
                            
                            <p style="font-size: 16px; margin-bottom: 20px; color: #ffffff;">Hello,</p>
                            
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                                Thank you for subscribing to our newsletter! You have successfully joined our community.
                            </p>
                            
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                Stay tuned for the latest news, updates, and exclusive offers delivered straight to your inbox.
                            </p>
                            
                            <div style="background-color: #242B51; border-left: 4px solid #4A6CF7; padding: 15px; margin-bottom: 30px; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #9ca3af;">
                                    <strong style="color: #ffffff;">Note:</strong> You subscribed with <span style="color: #4A6CF7;"><?php echo esc_html($to); ?></span>
                                </p>
                            </div>
                            
                            <p style="font-size: 16px; margin-bottom: 0;">
                                Best regards,<br>
                                <strong style="color: #ffffff;"><?php echo get_bloginfo('name'); ?> Team</strong>
                            </p>
                        </div>
                        
                        <!-- Footer -->
                        <div style="background-color: #18191E; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border-top: 1px solid #2E3038;">
                            <p style="margin: 0;">&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All rights reserved.</p>
                            <p style="margin: 10px 0 0;"><a href="<?php echo home_url(); ?>" style="color: #4A6CF7; text-decoration: none;"><?php echo home_url(); ?></a></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
    </html>
    <?php
    $message_html = ob_get_clean();

    // Define the closure for the hook (copied from contact form logic)
    $embed_logo_callback = function($phpmailer) {
        $logo_path = get_template_directory() . '/images/logo.png';
        if (file_exists($logo_path)) {
            $phpmailer->AddEmbeddedImage($logo_path, 'company-logo', 'logo.png');
        }
    };

    // Add the hook
    add_action('phpmailer_init', $embed_logo_callback);

    // Send email
    $sent = wp_mail($to, $subject, $message_html, $headers);
    
    // Remove the hook
    remove_action('phpmailer_init', $embed_logo_callback);
    
    if ($sent) {
        error_log('WP Custom Subscribe: Confirmation email sent to ' . $to);
    } else {
        error_log('WP Custom Subscribe: Failed to send confirmation email to ' . $to);
    }
    // --- SEND AUTO-REPLY EMAIL END ---

    wp_send_json_success('Subscribed successfully');
}
add_action('wp_ajax_wp_custom_subscribe', 'wp_custom_handle_subscribe_submission');
add_action('wp_ajax_nopriv_wp_custom_subscribe', 'wp_custom_handle_subscribe_submission');
