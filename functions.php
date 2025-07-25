<?php
// Theme setup function
function theme_setup() {
    // Add support for title tag
    add_theme_support('title-tag');

    // Add support for post thumbnails
    add_theme_support('post-thumbnails');

    // Register a primary menu
    register_nav_menus([
        'primary' => __('Primary Menu', 'your-theme-textdomain'),
    ]);

    // Register a footer menu 1
    register_nav_menus([
        'footer-one' => __('Footer Menu One', 'your-theme-textdomain'),
    ]);

    // Register a footer menu 2
    register_nav_menus([
        'footer-two' => __('Footer Menu Two', 'your-theme-textdomain'),
    ]);

    // Register a footer menu 3
    register_nav_menus([
        'footer-three' => __('Footer Menu Three', 'your-theme-textdomain'),
    ]);
    
    // Add theme support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add theme support for custom logo

    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'theme_setup');


// Carbon libs

use Carbon_Fields\Carbon_Fields;

add_action( 'after_setup_theme', function() {
    require_once __DIR__ . '/vendor/autoload.php'; // путь к autoload.php

    Carbon_Fields::boot();

    require_once get_template_directory() . '/inc/carbon-fields-init.php'; 
});


// Enqueue styles and scripts

function theme_enqueue_assets() {

    // Enqueue main stylesheet

    wp_enqueue_style('theme-style-tailwind', get_template_directory_uri() . '/common/css/style.css', [], '1.0');
    wp_enqueue_style('theme-style-mod-tailwind', get_template_directory_uri() . '/common/css/style-mod.css', [], '1.0');

 
    // Enqueue JavaScript files - in footer

    wp_enqueue_script('bundle-js', get_template_directory_uri() . '/common/js/bundle.js', array(), '1.0', true);
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/common/js/custom.js', array(), '1.0', true);
    wp_enqueue_script('earth-js', get_template_directory_uri() . '/common/js/earth.js', array(), '1.0', true);
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js', [], null, true);
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
    wp_enqueue_style('glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', [], '3.2.0');
    wp_enqueue_script('glightbox-js', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', [], '3.2.0', true);
    wp_enqueue_style( 'plyr-css', 'https://cdn.plyr.io/3.7.8/plyr.css', [], null );
	wp_enqueue_script( 'plyr-js', 'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js', [], null, true );
    wp_enqueue_script( 'wavesurfer-js', 'https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js', [], null, true );
    // Инициализация в DOMContentLoaded
    wp_add_inline_script('glightbox-js', '
      document.addEventListener("DOMContentLoaded", function() {
        const lightbox = GLightbox({ selector: ".glightbox" });
      });
    ');


}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');


// Tailwind и Preline для лендингов

add_action('wp_enqueue_scripts', function () {
    // Массив ID страниц, на которых нужно подключать Tailwind и Preline
    $allowed_pages = [1548, 1562]; // Добавь сюда другие ID страниц, если нужно

    if (is_page($allowed_pages)) {
        wp_enqueue_script(
            'tailwind-cdn',
            'https://cdn.tailwindcss.com',
            [],
            null,
            false
        );

        wp_enqueue_script(
            'preline-js',
            'https://cdn.jsdelivr.net/npm/preline@2.0.3/dist/preline.js',
            [],
            null,
            true
        );
    }
});



// Register widget areas

function theme_widgets_init() {
    register_sidebar([
        'name'          => __('Sidebar', 'your-theme-textdomain'),
        'id'            => 'sidebar-1',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'theme_widgets_init');

// REST API settings

add_filter('rest_authentication_errors', '__return_false');

add_action('init', function() {
    header("Access-Control-Allow-Origin: *");
});


// Функция которая отодвигает с верху шапку сайта что бы это выглядело красиво с навигационным меню от wordpress

function add_margin_for_admin_bar() {
    if ( is_user_logged_in() && is_admin_bar_showing() ) {
        ?>
        <style>
            header {
                margin-top: 36px;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'add_margin_for_admin_bar');

// Колличество слов в карточки постов

function custom_excerpt_length($length) {
    return 20; // Количество слов
}
add_filter('excerpt_length', 'custom_excerpt_length');



// WordPress Ticket System - Debug Version for functions.php

// 1. Register Custom Post Type 'Ticket'

function wp_custom_register_ticket_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Tickets',
            'singular_name' => 'Ticket',
            'menu_name' => 'Tickets',
            'add_new' => 'Add New Ticket',
            'add_new_item' => 'Add New Ticket',
            'edit_item' => 'Edit Ticket',
            'new_item' => 'New Ticket',
            'view_item' => 'View Ticket',
            'search_items' => 'Search Tickets',
            'not_found' => 'No tickets found',
            'not_found_in_trash' => 'No tickets found in trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-tickets-alt',
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_rest' => false
    );
    register_post_type('ticket', $args);
}
add_action('init', 'wp_custom_register_ticket_post_type');

// 2. Add custom columns to admin list

function wp_custom_ticket_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'Ticket',
        'sender_name' => 'Sender Name',
        'sender_email' => 'Email',
        'message_preview' => 'Message Preview',
        'date' => 'Date Submitted'
    );
    return $new_columns;
}
add_filter('manage_ticket_posts_columns', 'wp_custom_ticket_columns');

// 3. Fill custom columns with data

function wp_custom_ticket_column_content($column, $post_id) {
    switch ($column) {
        case 'sender_name':
            echo get_post_meta($post_id, '_wp_custom_sender_name', true);
            break;
        case 'sender_email':
            echo get_post_meta($post_id, '_wp_custom_sender_email', true);
            break;
        case 'message_preview':
            $message = get_post_meta($post_id, '_wp_custom_message', true);
            echo wp_trim_words($message, 10, '...');
            break;
    }
}
add_action('manage_ticket_posts_custom_column', 'wp_custom_ticket_column_content', 10, 2);

// 4. Add meta box to show ticket details in admin

function wp_custom_ticket_meta_box() {
    add_meta_box(
        'wp-custom-ticket-details',
        'Ticket Details',
        'wp_custom_ticket_meta_box_callback',
        'ticket',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_custom_ticket_meta_box');

function wp_custom_ticket_meta_box_callback($post) {
    $sender_name = get_post_meta($post->ID, '_wp_custom_sender_name', true);
    $sender_email = get_post_meta($post->ID, '_wp_custom_sender_email', true);
    $message = get_post_meta($post->ID, '_wp_custom_message', true);
    $submission_time = get_post_meta($post->ID, '_wp_custom_submission_time', true);
    
    echo '<div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px; border-radius: 8px; margin: -6px -12px;">';
    echo '<h4 style="color: #64b5f6; border-bottom: 2px solid #64b5f6; padding-bottom: 8px; margin-bottom: 15px; margin-top: 0;">📧 Sender Information</h4>';
    echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Name:</strong> ' . esc_html($sender_name) . '</p>';
    echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Email:</strong> ' . esc_html($sender_email) . '</p>';
    echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Submitted:</strong> ' . esc_html($submission_time) . '</p>';
    echo '<h4 style="color: #64b5f6; border-bottom: 2px solid #64b5f6; padding-bottom: 8px; margin-bottom: 15px;">💬 Message</h4>';
    echo '<div class="message-content" style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #64b5f6; margin-top: 10px; line-height: 1.7; white-space: pre-wrap;">' . esc_html($message) . '</div>';
    echo '</div>';
}

// 5. AJAX handler for form submission

function wp_custom_handle_ticket_submission() {
    // Debug logging
    error_log('WP Custom Ticket: AJAX handler called');
    
    // Check if nonce exists

    if (!isset($_POST['nonce'])) {
        error_log('WP Custom Ticket: No nonce provided');
        wp_send_json_error('No nonce provided');
    }
    
    // Verify nonce

    if (!wp_verify_nonce($_POST['nonce'], 'wp_custom_ticket_nonce')) {
        error_log('WP Custom Ticket: Nonce verification failed');
        wp_send_json_error('Security check failed');
    }
    
    // Check if required fields exist

    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message'])) {
        error_log('WP Custom Ticket: Missing required fields');
        wp_send_json_error('Missing required fields');
    }
    
    // Sanitize input data

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);
    
    error_log('WP Custom Ticket: Processing - Name: ' . $name . ', Email: ' . $email);
    
    // Validate data

    if (empty($name) || empty($email) || empty($message)) {
        error_log('WP Custom Ticket: Empty fields after sanitization');
        wp_send_json_error('All fields are required');
    }
    
    if (!is_email($email)) {
        error_log('WP Custom Ticket: Invalid email: ' . $email);
        wp_send_json_error('Invalid email address');
    }
    
    // Create ticket post

    $post_data = array(
        'post_title' => 'Ticket from ' . $name,
        'post_content' => $message,
        'post_status' => 'publish',
        'post_type' => 'ticket',
        'post_author' => 1
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        error_log('WP Custom Ticket: Failed to create post - ' . $post_id->get_error_message());
        wp_send_json_error('Failed to create ticket');
    }
    
    // Add meta data

    update_post_meta($post_id, '_wp_custom_sender_name', $name);
    update_post_meta($post_id, '_wp_custom_sender_email', $email);
    update_post_meta($post_id, '_wp_custom_message', $message);
    update_post_meta($post_id, '_wp_custom_submission_time', current_time('mysql'));
    update_post_meta($post_id, '_wp_custom_ip_address', $_SERVER['REMOTE_ADDR']);
    
    error_log('WP Custom Ticket: Successfully created ticket with ID: ' . $post_id);
    wp_send_json_success('Ticket submitted successfully');
}
add_action('wp_ajax_wp_custom_submit_ticket', 'wp_custom_handle_ticket_submission');
add_action('wp_ajax_nopriv_wp_custom_submit_ticket', 'wp_custom_handle_ticket_submission');

// 6. Enqueue scripts and styles - SIMPLIFIED VERSION

function wp_custom_enqueue_ticket_scripts() {

    // Only load on pages that have the form

    if (!is_admin()) {
        wp_enqueue_script('jquery');
        
        // Localize script for AJAX

        wp_localize_script('jquery', 'wp_custom_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_custom_ticket_nonce')
        ));
        
        // Add inline script

        wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            console.log("WP Custom Ticket: jQuery loaded");
            console.log("AJAX URL:", wp_custom_ajax.ajax_url);
            console.log("Nonce:", wp_custom_ajax.nonce);
            
            // Test if form exists
            if ($("#wp-custom-contact-form").length > 0) {
                console.log("WP Custom Ticket: Form found!");
            } else {
                console.log("WP Custom Ticket: Form NOT found!");
            }
            
            // Form submission handler
            $(document).on("submit", "#wp-custom-contact-form", function(e) {
                console.log("WP Custom Ticket: Form submitted!");
                e.preventDefault();
                
                var name = $("#wp-custom-name").val().trim();
                var email = $("#wp-custom-email").val().trim();
                var message = $("#wp-custom-message").val().trim();
                
                console.log("Form data:", {name: name, email: email, message: message});
                
                // Simple validation
                if (!name || !email || !message) {
                    alert("Please fill in all fields");
                    return false;
                }
                
                // Show loading
                var submitBtn = $("#wp-custom-submit");
                submitBtn.text("Sending...").prop("disabled", true);
                
                // AJAX request
                $.ajax({
                    url: wp_custom_ajax.ajax_url,
                    type: "POST",
                    data: {
                        action: "wp_custom_submit_ticket",
                        nonce: wp_custom_ajax.nonce,
                        name: name,
                        email: email,
                        message: message
                    },
                    success: function(response) {
                        console.log("AJAX Success:", response);
                        if (response.success) {
                            showSuccessModal();
                            $("#wp-custom-contact-form")[0].reset();
                        } else {
                            alert("Error: " + (response.data || "Something went wrong"));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", xhr, status, error);
                        alert("Error: Unable to submit ticket. Check console for details.");
                    },
                    complete: function() {
                        submitBtn.text("Submit Ticket").prop("disabled", false);
                    }
                });
            });
            
            // Success Modal Function
            function showSuccessModal() {
                var modal = $(\'<div id="wp-custom-success-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">\' +
                    \'<div style="background: #060607; border-radius: 16px; border: 1px solid #2E3038; padding: 40px; max-width: 420px; margin: 20px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalSlideIn 0.3s ease-out;">\' +
                        \'<div style="margin-bottom: 20px;">\' +
                            \'<svg style="margin: 0 auto; height: 80px; width: 80px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">\' +
                                \'<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"></circle>\' +
                                \'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>\' +
                            \'</svg>\' +
                        \'</div>\' +
                        \'<h3 style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 12px; font-family: -apple-system, BlinkMacSystemFont, system-ui;">Thank You!</h3>\' +
                        \'<p style="color: #d1d5db; margin-bottom: 24px; font-size: 16px; line-height: 1.5;">We have received your message and will contact you soon.</p>\' +
                        \'<button id="close-modal" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 32px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px 0 rgba(116, 79, 168, 0.75);">Close</button>\' +
                    \'</div>\' +
                \'</div>\');
                
                // Add CSS animations
                $(\'<style>\').prop(\'type\', \'text/css\').html(\'@keyframes modalSlideIn { from { opacity: 0; transform: translateY(-50px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }\').appendTo(\'head\');
                
                $(\'body\').append(modal);
                
                // Auto close after 5 seconds
                setTimeout(function() {
                    modal.fadeOut(400, function() {
                        modal.remove();
                    });
                }, 5000);
                
                // Manual close
                $(\'#close-modal\').on(\'click\', function() {
                    modal.fadeOut(400, function() {
                        modal.remove();
                    });
                });
                
                // Close on backdrop click
                modal.on(\'click\', function(e) {
                    if (e.target === this) {
                        modal.fadeOut(400, function() {
                            modal.remove();
                        });
                    }
                });
                
                // Close on Escape key
                $(document).on(\'keydown.modal\', function(e) {
                    if (e.keyCode === 27) {
                        modal.fadeOut(400, function() {
                            modal.remove();
                        });
                        $(document).off(\'keydown.modal\');
                    }
                });
            }
        });
        ');
    }
}
add_action('wp_enqueue_scripts', 'wp_custom_enqueue_ticket_scripts');

// 7. Admin page styling for tickets

function wp_custom_ticket_admin_styles() {
    $screen = get_current_screen();
    if ($screen && ($screen->post_type === 'ticket' || $screen->id === 'edit-ticket')) {
        ?>
        <style>
        /* Dark theme for tickets admin page */
        /* #wpwrap{
            background: #000000 !important;
        } */
        .wp-list-table.tickets {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .wp-list-table.tickets thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 15px 10px;
            border: none;
        }
        
        .wp-list-table.tickets tbody tr {
            background: #2d2d2d;
            border-bottom: 1px solid #404040;
            transition: all 0.2s ease;
        }
        
        .wp-list-table.tickets tbody tr:hover {
            background: #3a3a3a;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .wp-list-table.tickets tbody tr:nth-child(even) {
            background: #262626;
        }
        
        .wp-list-table.tickets tbody tr:nth-child(even):hover {
            background: #353535;
        }
        
        .wp-list-table.tickets td {
            color: #e5e5e5;
            padding: 15px 10px;
            border: none;
            vertical-align: middle;
        }
        
        .wp-list-table.tickets .column-title a {
            color: #64b5f6;
            font-weight: 600;
            text-decoration: none;
        }
        
        .wp-list-table.tickets .column-title a:hover {
            color: #90caf9;
        }
        
        .wp-list-table.tickets .column-sender_name {
            font-weight: 500;
            color: #81c784;
        }
        
        .wp-list-table.tickets .column-sender_email {
            color: #ffb74d;
            font-family: monospace;
        }
        
        .wp-list-table.tickets .column-message_preview {
            color: #bcbcbc;
            font-style: italic;
            max-width: 300px;
        }
        
        .wp-list-table.tickets .column-date {
            color: #f48fb1;
            font-size: 13px;
        }
        
        /* Ticket details meta box styling */

        #wp-custom-ticket-details .inside {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
        }
        
        #wp-custom-ticket-details h4 {
            color: #64b5f6;
            border-bottom: 2px solid #64b5f6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        #wp-custom-ticket-details p {
            margin: 8px 0;
            line-height: 1.6;
        }
        
        #wp-custom-ticket-details strong {
            color: #81c784;
        }
        
        #wp-custom-ticket-details .message-content {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #64b5f6;
            margin-top: 10px;
            line-height: 1.7;
        }
        
        /* Page title styling */

        .wrap h1.wp-heading-inline {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 28px;
        }
        
        /* Add new button styling */

        .page-title-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .page-title-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }
        
        /* Search box styling */

        .search-box input[type="search"] {
            background: #2d2d2d;
            border: 1px solid #404040;
            color: #e5e5e5;
            border-radius: 6px;
            padding: 8px 12px;
        }
        
        .search-box input[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            margin-left: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        /* Pagination styling */

        .tablenav .tablenav-pages a {
            background: #2d2d2d;
            color: #64b5f6;
            border: 1px solid #404040;
            border-radius: 4px;
        }
        
        .tablenav .tablenav-pages a:hover {
            background: #3a3a3a;
            color: #90caf9;
        }
        
        /* Status indicators */

        .ticket-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .ticket-status.new {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid #4caf50;
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'wp_custom_ticket_admin_styles');
function wp_custom_debug_info() {
    if (current_user_can('manage_options')) {
        echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #0073aa;">';
        echo '<h4>WP Custom Ticket Debug Info:</h4>';
        echo '<p><strong>Post Type Registered:</strong> ' . (post_type_exists('ticket') ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>AJAX URL:</strong> ' . admin_url('admin-ajax.php') . '</p>';
        echo '<p><strong>Current User Can Manage:</strong> ' . (current_user_can('manage_options') ? 'Yes' : 'No') . '</p>';
        echo '</div>';
    }
}

// Add debug info to admin footer (remove after testing)

// add_action('admin_footer', 'wp_custom_debug_info');



// Telegram bot form


add_action( 'added_post_meta', function( $mid, $pid, $key, $val ) {
    if ( '_wp_custom_message' !== $key ) return;
    if ( 'ticket' !== get_post_type( $pid ) ) return;
    if ( get_post_meta( $pid, '_wp_custom_telegram_sent', true ) ) return;

    $token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
    $chat_id = '422713968';

    $name    = get_post_meta( $pid, '_wp_custom_sender_name', true );
    $email   = get_post_meta( $pid, '_wp_custom_sender_email', true );
    $message = get_post_meta( $pid, '_wp_custom_message', true );

    $text = "Новый тикет\nИмя: $name\nEmail: $email\n\n$message";

    wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
        'body' => array(
            'chat_id' => $chat_id,
            'text'    => $text,
        ),
        'timeout' => 10,
    ) );

    update_post_meta( $pid, '_wp_custom_telegram_sent', 1 );
}, 10, 4 );




// audio play posts 


// function rs_enqueue_audio_assets() {
// 	if ( is_singular( 'post' ) ) {
// 		wp_enqueue_style( 'plyr-css', 'https://cdn.plyr.io/3.7.8/plyr.css', [], null );
// 		wp_enqueue_script( 'plyr-js', 'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js', [], null, true );
// 		// WaveSurfer 7.x
// 		wp_enqueue_script( 'wavesurfer-js', 'https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js', [], null, true );
// 	}
// }
// add_action( 'wp_enqueue_scripts', 'rs_enqueue_audio_assets' );

/**
 * Append collapsible audio player + waveform (bars) after content.
 */
function rs_prepend_post_audio_player( $content ) {
	if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {

		$audio_url = carbon_get_post_meta( get_the_ID(), 'post_audio_file' );
		if ( $audio_url ) {

			$post_id      = get_the_ID();
			$uid          = 'rs-' . $post_id; // стабильный префикс
			$btn_id       = $uid . '-btn';
			$wrap_id      = $uid . '-wrap';
			$icon_id      = $uid . '-icon';
			$wave_id      = $uid . '-wave';
			$audio_dom_id = $uid . '-audio';

			ob_start(); ?>
			<div class="rs-listen-toggle-container" style="margin-top:50px;max-width:100%;width:100%;">
				<button
					id="<?php echo esc_attr( $btn_id ); ?>"
					aria-expanded="false"
					data-target="#<?php echo esc_attr( $wrap_id ); ?>"
					class="inline-flex items-center justify-center py-2 px-4 mr-4 mb-2 rounded-md bg-primary bg-opacity-10 text-body-color hover:bg-opacity-100 hover:text-white cursor-pointer border border-transparent transition"
					style="text-decoration:none;"
					type="button"
				>
					<span id="<?php echo esc_attr( $icon_id ); ?>" class="inline-block w-5 h-5 mr-2" aria-hidden="true">
						<!-- Play Icon -->
						<svg viewBox="0 0 24 24" fill="#2E3038" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
							<path d="M8 5v14l11-7z"/>
						</svg>
					</span>
					<span>Listen to the article</span>
				</button>

				<div
					id="<?php echo esc_attr( $wrap_id ); ?>"
					class="rs-post-audio-player-container 123"
				>
					<!-- Waveform -->
					<div id="<?php echo esc_attr( $wave_id ); ?>" class="rs-audio-wave"></div>

					<!-- Audio element controlled by Plyr; WaveSurfer reads from it -->
					<audio
						id="<?php echo esc_attr( $audio_dom_id ); ?>"
						class="js-player"
						controls
						crossorigin
						style="width:100%;height:60px;margin-top:8px;"
					>
						<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg" />
					</audio>
				</div>
			</div>
			<?php
			$player_html = ob_get_clean();
			$content    .= $player_html;

			// Подключаем общий inline JS/CSS в футере только 1 раз.
			static $rs_audio_inline_done = false;
			if ( ! $rs_audio_inline_done ) {
				$rs_audio_inline_done = true;
				add_action( 'wp_footer', 'rs_audio_inline_assets', 99 );
			}
		}
	}
	return $content;
}
add_filter( 'the_content', 'rs_prepend_post_audio_player' );

/**
 * Inline JS/CSS (один раз в футере).
 */
function rs_audio_inline_assets() { ?>
	<script>
	document.addEventListener('DOMContentLoaded', () => {
		const playIconSVG = `
			<svg viewBox="0 0 24 24" fill="#2E3038" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
				<path d="M8 5v14l11-7z"/>
			</svg>
		`;
		const closeIconSVG = `
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
				<path d="M18 6L6 18" stroke="#2E3038" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M6 6L18 18" stroke="#2E3038" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		`;

		// Для каждого плеера на странице
		document.querySelectorAll('.rs-post-audio-player-container').forEach((wrap) => {
			const waveEl  = wrap.querySelector('.rs-audio-wave');
			const audioEl = wrap.querySelector('audio.js-player');
			const btn     = wrap.parentElement.querySelector('button[id$="-btn"]');
			const icon    = wrap.parentElement.querySelector('span[id$="-icon"]');

			// Plyr: убираем прогресс (используем волну)
			const plyrInstance = new Plyr(audioEl, {
				controls: ['play', 'current-time', 'mute', 'volume'],
			});

			// WaveSurfer: грузим звук из уже существующего <audio>
			const wavesurfer = WaveSurfer.create({
				container: waveEl,
				waveColor: '#2E3038',
				progressColor: '#4A6CF7',
				barWidth: 2,
				barGap: 2,
				barRadius: 1,
				height: 80,
				responsive: true,
				interact: true,
				dragToSeek: true,
				backend: 'MediaElement',
				media: audioEl, // используем тот же media элемент
			});
			// В backend: 'MediaElement' режим, WaveSurfer работает поверх audioEl, НЕ создавая второй звук.

			// Перемотка по волне (дублируем на всякий случай, хотя MediaElement сам синхронен)
			wavesurfer.on('seek', (progress) => {
				if (audioEl.duration) {
					audioEl.currentTime = progress * audioEl.duration;
				}
			});

			// Кнопка раскрытия/сворачивания
			btn.addEventListener('click', () => {
				const expanded = btn.getAttribute('aria-expanded') === 'true';

				if (expanded) {
					// СВЕРНУТЬ: пауза, позиция остаётся
					plyrInstance.pause(); // останавливает audioEl; волна тоже остановится (общий media)
					wrap.classList.remove('is-open');
					btn.setAttribute('aria-expanded', 'false');
					icon.innerHTML = playIconSVG;
					btn.style.border = ''; // вернуть border-transparent
				} else {
					// РАЗВЕРНУТЬ
					wrap.classList.add('is-open');
					btn.setAttribute('aria-expanded', 'true');
					icon.innerHTML = closeIconSVG;
					btn.style.border = 'none';
				}
			});
		});
	});
	</script>

	<style>
	/* Скрываем стандартный прогресс Plyr */
	.rs-post-audio-player-container .plyr__progress,
	.rs-post-audio-player-container .plyr__progress__container {
		display:none !important;
	}

	/* Обёртка плеера (анимация открытия) */
	.rs-post-audio-player-container {
		max-height:0;
		opacity:0;
		overflow:hidden;
		transition:opacity .4s ease,max-height .5s ease;
		/* background-color:#000; */
		border:1px solid #2E3038;
		border-radius:6px;
		--plyr-color-main:#4A6CF7;
		--plyr-track-background:#222;
		--plyr-progress-background:#4A6CF7;
		--plyr-control-hover-background:rgba(74,108,247,.15);
		color:#fff;
		margin-top:10px;
		padding:8px 12px 16px;
	}
	.rs-post-audio-player-container.is-open {
		max-height:300px; /* запас под волну + контролы */
		opacity:1;
	}

	/* Волна */
	.rs-post-audio-player-container .rs-audio-wave {
		width:100%;
		height:80px;
	}

	/* Plyr */
	.rs-post-audio-player-container .plyr {
		width:100% !important;
		height:60px !important;
	}
	.rs-post-audio-player-container .plyr__time {
		color:#fff !important;
	}
	.plyr--audio .plyr__controls {
		background: rgba(0, 0, 0, 0) !important;
	}
	</style>
<?php }



// modal portfolio

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('portfolio-modal', get_template_directory_uri() . '/common/js/portfolio-modal.js', [], null, true);
});

add_action('wp_ajax_nopriv_proxy_site', 'proxy_site_func');
add_action('wp_ajax_proxy_site', 'proxy_site_func');

function proxy_site_func() {
    if (empty($_GET['url'])) {
        wp_send_json_error('Missing URL', 400);
    }

    $url = esc_url_raw($_GET['url']);
    $parsed_url = parse_url($url);

    if (!isset($parsed_url['host'])) {
        wp_send_json_error('Invalid URL', 400);
    }

    // Нормализация хоста — убираем www.
    $host = strtolower($parsed_url['host']);
    $host = preg_replace('/^www\./', '', $host);

    // Разрешённые домены (без www)
    $allowed_hosts = ['seeintl.org', 'waapple.org', 'snopud.com', 'cchpca.org'];

    if (!in_array($host, $allowed_hosts, true)) {
        wp_send_json_error('URL not allowed', 403);
    }

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        wp_send_json_error('Request failed');
    }

    $body = wp_remote_retrieve_body($response);
    $content_type = wp_remote_retrieve_header($response, 'content-type');

    header('Content-Type: ' . $content_type);
    echo $body;
    wp_die();
}










