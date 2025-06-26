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

// Enqueue styles and scripts
function theme_enqueue_assets() {
    // Enqueue main stylesheet
    wp_enqueue_style('theme-style-tailwind', get_template_directory_uri() . '/common/css/style.css', [], '1.0');
    wp_enqueue_style('theme-style-mod-tailwind', get_template_directory_uri() . '/common/css/style-mod.css', [], '1.0');

 
    // Enqueue JavaScript files - in footer
    wp_enqueue_script('build-js', get_template_directory_uri() . '/common/js/bundle.js', array(), '1.0', true);
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/common/js/custom.js', array(), '1.0', true);

    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
    // Аналогично для других библиотек



}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

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



// audio play posts 

add_filter('the_content', 'add_tts_audio_wave_progress');

function add_tts_audio_wave_progress($content) {
    if (!is_singular('post')) return $content;

    $post_text = wp_strip_all_tags($content);
    $post_text = preg_replace('/\s+/', ' ', trim($post_text)); // Нормализация пробелов
    $post_text = esc_js($post_text);

    $script = <<<HTML
<style>
  .wave-progress-wrapper {
    width: 100%;
    height: 64px;
    display: flex;
    gap: 1px;
    overflow: hidden;
    user-select: none;
    align-items: flex-end;
    background: transparent;
  }
  .wave-block {
    width: 2px;
    background: #3b82f6;
    border-radius: 2px;
    transform-origin: bottom center;
    transition: height 0.1s ease-out;
  }
  .tts-controls {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  .tts-button {
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  .tts-button:hover {
    background: #1d4ed8;
  }
  .tts-button:disabled {
    background: #6b7280;
    cursor: not-allowed;
  }
</style>

<div style="width:100%; max-width:100%; margin-bottom:24px; padding:16px; background:#000; border:1px solid #2E3038; color:#fff; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
  <div style="display:flex; align-items:center; justify-between; gap:12px; flex-wrap:wrap;">
    <div style="font-size:1.125rem; font-weight:600; display:flex; align-items:center; gap:8px; flex-grow:1;">
      <span>🔊</span><span>Voice the article</span>
    </div>
    <div class="tts-controls">
      <button id="tts-toggle" class="tts-button">▶️ Play</button>
      <button id="tts-stop" class="tts-button" style="display:none;">⏹️ Stop</button>
    </div>
  </div>
  <div class="wave-progress-wrapper" id="wave-progress-wrapper" aria-label="Прогресс озвучки" aria-live="polite" aria-atomic="true"></div>
</div>

<script>
(function(){
  const toggleBtn = document.getElementById('tts-toggle');
  const stopBtn = document.getElementById('tts-stop');
  const waveWrapper = document.getElementById('wave-progress-wrapper');

  const blockWidth = 2;
  const blockGap = 1;
  let maxBlocks = 0;
  let blocks = [];
  let utterance = null;
  let timer = null;
  let startTime = 0;
  let totalDuration = 0;
  let pausedTime = 0;
  let isPaused = false;
  let isSpeaking = false;
  let selectedVoice = null;
  let textChunks = [];
  let currentChunkIndex = 0;
  let chunkStartTimes = [];

  const text = "$post_text";

  // Разбиваем текст на части для лучшей работы с разными языками
  function splitTextIntoChunks(text, maxLength = 300) {
    const chunks = [];
    const sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0);
    
    let currentChunk = '';
    for (const sentence of sentences) {
      if (currentChunk.length + sentence.length > maxLength && currentChunk.length > 0) {
        chunks.push(currentChunk.trim());
        currentChunk = sentence.trim();
      } else {
        currentChunk += (currentChunk.length > 0 ? '. ' : '') + sentence.trim();
      }
    }
    
    if (currentChunk.length > 0) {
      chunks.push(currentChunk.trim());
    }
    
    return chunks.length > 0 ? chunks : [text];
  }

  function createBlocks() {
    waveWrapper.innerHTML = '';
    const wrapperWidth = waveWrapper.clientWidth || 600;
    maxBlocks = Math.floor(wrapperWidth / (blockWidth + blockGap));
    blocks = [];
    
    for (let i = 0; i < maxBlocks; i++) {
      const block = document.createElement('div');
      block.className = 'wave-block';
      block.style.height = Math.floor(Math.random() * 60 + 4) + 'px';
      block.style.visibility = 'hidden';
      waveWrapper.appendChild(block);
      blocks.push(block);
    }
  }

  function updateProgress() {
    if (!isSpeaking) return;
    
    const now = Date.now();
    const elapsed = (now - startTime) + pausedTime;
    const progress = Math.min(elapsed / totalDuration, 1);
    const visibleBlocks = Math.floor(progress * maxBlocks);
    
    for (let i = 0; i < maxBlocks; i++) {
      blocks[i].style.visibility = i < visibleBlocks ? 'visible' : 'hidden';
    }
    
    if (progress >= 1) {
      stopSpeech();
    }
  }

  function detectVoice() {
    return new Promise((resolve) => {
      const setVoice = () => {
        const voices = speechSynthesis.getVoices();
        if (voices.length === 0) return;
        
        // Определяем язык страницы
        const pageLanguage = document.documentElement.lang || 
                           navigator.language || 
                           navigator.userLanguage || 
                           'en-US';
        
        // Ищем голос для языка страницы
        let voice = voices.find(v => v.lang === pageLanguage);
        
        // Если не найден точный, ищем по основному языку
        if (!voice) {
          const mainLang = pageLanguage.split('-')[0];
          voice = voices.find(v => v.lang.startsWith(mainLang));
        }
        
        // Если все еще не найден, берем первый доступный
        if (!voice) {
          voice = voices.find(v => v.default) || voices[0];
        }
        
        selectedVoice = voice;
        resolve();
      };

      if (speechSynthesis.getVoices().length > 0) {
        setVoice();
      } else {
        speechSynthesis.onvoiceschanged = setVoice;
      }
    });
  }

  function calculateDuration(text) {
    // Более точный расчет длительности для разных языков
    const wordsPerMinute = selectedVoice && selectedVoice.lang.startsWith('ru') ? 180 : 200;
    const words = text.split(/\s+/).length;
    return (words / wordsPerMinute) * 60 * 1000; // в миллисекундах
  }

  function speakChunk(chunkIndex) {
    if (chunkIndex >= textChunks.length) {
      stopSpeech();
      return;
    }

    currentChunkIndex = chunkIndex;
    utterance = new SpeechSynthesisUtterance(textChunks[chunkIndex]);
    
    if (selectedVoice) {
      utterance.voice = selectedVoice;
      utterance.lang = selectedVoice.lang;
    }
    
    utterance.rate = 1.0;
    utterance.pitch = 1.0;
    utterance.volume = 1.0;

    utterance.onend = () => {
      if (isSpeaking && currentChunkIndex < textChunks.length - 1) {
        // Небольшая пауза между частями
        setTimeout(() => speakChunk(currentChunkIndex + 1), 100);
      } else {
        stopSpeech();
      }
    };

    utterance.onerror = (event) => {
      console.error('Speech synthesis error:', event);
      stopSpeech();
    };

    speechSynthesis.speak(utterance);
  }

  function startSpeech() {
    if (!selectedVoice) return;

    textChunks = splitTextIntoChunks(text);
    totalDuration = calculateDuration(text);
    
    if (!isPaused) {
      startTime = Date.now();
      pausedTime = 0;
      currentChunkIndex = 0;
    } else {
      startTime = Date.now();
      isPaused = false;
    }

    isSpeaking = true;
    timer = setInterval(updateProgress, 100);
    
    speakChunk(currentChunkIndex);
    
    toggleBtn.textContent = '⏸️ Pause';
    stopBtn.style.display = 'inline-block';
  }

  function pauseSpeech() {
    if (speechSynthesis.speaking) {
      speechSynthesis.pause();
      clearInterval(timer);
      pausedTime += Date.now() - startTime;
      isPaused = true;
      isSpeaking = false;
      toggleBtn.textContent = '▶️ Continue';
    }
  }

  function resumeSpeech() {
    if (speechSynthesis.paused) {
      speechSynthesis.resume();
      startTime = Date.now();
      isSpeaking = true;
      timer = setInterval(updateProgress, 100);
      toggleBtn.textContent = '⏸️ Pause';
    }
  }

  function stopSpeech() {
    speechSynthesis.cancel();
    clearInterval(timer);
    
    isSpeaking = false;
    isPaused = false;
    pausedTime = 0;
    currentChunkIndex = 0;
    
    // Скрываем все блоки прогресса
    for (let i = 0; i < maxBlocks; i++) {
      blocks[i].style.visibility = 'hidden';
    }
    
    toggleBtn.textContent = '▶️ Play';
    stopBtn.style.display = 'none';
  }

  // Обработчики событий
  toggleBtn.onclick = () => {
    if (!selectedVoice) {
      detectVoice().then(() => {
        if (selectedVoice) toggleBtn.onclick();
      });
      return;
    }

    if (isSpeaking) {
      pauseSpeech();
    } else if (isPaused) {
      resumeSpeech();
    } else {
      startSpeech();
    }
  };

  stopBtn.onclick = stopSpeech;

  // Инициализация
  createBlocks();
  detectVoice();

  // Пересоздаем блоки при изменении размера окна
  window.addEventListener('resize', createBlocks);

  // Останавливаем при уходе со страницы
  window.addEventListener('beforeunload', stopSpeech);
})();
</script>
HTML;

    return $script . $content;
}










