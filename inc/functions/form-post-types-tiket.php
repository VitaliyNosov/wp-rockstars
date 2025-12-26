<?php

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
        'show_in_rest' => false,
        'capabilities' => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true
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
    
    // UTM Parameters
    $utm_source = isset($_POST['utm_source']) ? sanitize_text_field($_POST['utm_source']) : '';
    $utm_medium = isset($_POST['utm_medium']) ? sanitize_text_field($_POST['utm_medium']) : '';
    $utm_campaign = isset($_POST['utm_campaign']) ? sanitize_text_field($_POST['utm_campaign']) : '';
    
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
    
    // Save UTMs
    if($utm_source) update_post_meta($post_id, '_wp_custom_utm_source', $utm_source);
    if($utm_medium) update_post_meta($post_id, '_wp_custom_utm_medium', $utm_medium);
    if($utm_campaign) update_post_meta($post_id, '_wp_custom_utm_campaign', $utm_campaign);
    
    update_post_meta($post_id, '_wp_custom_submission_time', current_time('mysql'));
    update_post_meta($post_id, '_wp_custom_submission_time', current_time('mysql'));
    update_post_meta($post_id, '_wp_custom_ip_address', $_SERVER['REMOTE_ADDR']);
    
    // Resolve Geolocation (Backend)
    $user_ip = $_SERVER['REMOTE_ADDR'];
    // For local testing, mock an IP if localhost
    if($user_ip == '127.0.0.1' || $user_ip == '::1') {
        $user_ip = '8.8.8.8'; // Mock Google DNS IP for test
    }
    
    $api_url = "http://ip-api.com/json/" . $user_ip;
    $response = wp_remote_get($api_url, array('timeout' => 2));
    
    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if ($data && $data['status'] == 'success') {
            update_post_meta($post_id, '_wp_custom_country', $data['country']);
            update_post_meta($post_id, '_wp_custom_country_code', $data['countryCode']);
            update_post_meta($post_id, '_wp_custom_city', $data['city']);
        }
    }
    
    error_log('WP Custom Ticket: Successfully created ticket with ID: ' . $post_id);

    // --- SEND AUTO-REPLY EMAIL START ---
    $to = $email;
    $subject = 'We received your request | ' . get_bloginfo('name');
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Load template
    ob_start();
    include get_template_directory() . '/inc/email-templates/contact-reply.php';
    $message_html = ob_get_clean();

    // Define the closure for the hook
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
    
    // Remove the hook so it doesn't affect other emails
    remove_action('phpmailer_init', $embed_logo_callback);
    
    if ($sent) {
        error_log('WP Custom Ticket: Auto-reply sent to ' . $to);
    } else {
        error_log('WP Custom Ticket: Failed to send auto-reply to ' . $to);
    }
    // --- SEND AUTO-REPLY EMAIL END ---

    wp_send_json_success(array(
        'message' => 'Ticket submitted successfully',
        'debug' => array(
            'source' => $utm_source,
            'medium' => $utm_medium,
            'campaign' => $utm_campaign
        )
    ));
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
            
            // Add Styles for Dots
            $(\'<style>.loading-dots:after { content: "."; animation: dots 1.5s steps(5, end) infinite; } @keyframes dots { 0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 40% { color: white; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 60% { text-shadow: .25em 0 0 white, .5em 0 0 rgba(0,0,0,0);} 80%, 100% { text-shadow: .25em 0 0 white, .5em 0 0 white;}}</style>\').appendTo("head");
            
            // Form submission handler
            $(document).on("submit", "#wp-custom-contact-form", function(e) {
                console.log("WP Custom Ticket: Form submitted!");
                e.preventDefault();
                
                var name = $("#wp-custom-name").val().trim();
                var email = $("#wp-custom-email").val().trim();
                var message = $("#wp-custom-message").val().trim();
                
                // Parse URL parameters for UTMs
                const urlParams = new URLSearchParams(window.location.search);
                var utm_source = urlParams.get("utm_source") || "";
                var utm_medium = urlParams.get("utm_medium") || "";
                var utm_campaign = urlParams.get("utm_campaign") || "";
                
                console.log("Form data:", {name: name, email: email, message: message});
                console.log("Captured UTMs:", {source: utm_source, medium: utm_medium, campaign: utm_campaign});
                console.log("Current URL:", window.location.href);
                
                // Simple validation
                if (!name || !email || !message) {
                    alert("Please fill in all fields");
                    return false;
                }
                
                // Show loading
                var submitBtn = $("#wp-custom-submit");
                var originalText = submitBtn.text();
                submitBtn.data("original-text", originalText);
                submitBtn.html("Sending<span class=\"loading-dots\"></span>").prop("disabled", true);
                
                // AJAX request
                $.ajax({
                    url: wp_custom_ajax.ajax_url,
                    type: "POST",
                    data: {
                        action: "wp_custom_submit_ticket",
                        nonce: wp_custom_ajax.nonce,
                        name: name,
                        email: email,
                        message: message,
                        utm_source: utm_source,
                        utm_medium: utm_medium,
                        utm_campaign: utm_campaign
                    },
                    success: function(response) {
                        console.log("AJAX Success:", response);
                        if (response.success) {
                            console.log("Server Debug - Saved UTMs:", response.data.debug);
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
                        var submitBtn = $("#wp-custom-submit");
                        var originalText = submitBtn.data("original-text") || "Submit Ticket";
                        submitBtn.text(originalText).prop("disabled", false);
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

// 7. Export functionality

function wp_custom_add_export_button($which) {
    global $typenow;
    if ('ticket' === $typenow && 'top' === $which) {
        $export_url = admin_url('admin-post.php?action=wp_custom_export_tickets');
        $nonce = wp_create_nonce('wp_custom_export_tickets_nonce');
        $export_url = add_query_arg('nonce', $nonce, $export_url);
        
        // Pass current filters to the export URL
        if(isset($_GET['m'])) {
            $export_url = add_query_arg('m', $_GET['m'], $export_url);
        }
        if(isset($_GET['s'])) {
            $export_url = add_query_arg('s', $_GET['s'], $export_url);
        }
        ?>
        <div class="alignleft actions">
            <a href="<?php echo esc_url($export_url); ?>" class="button button-primary" style="margin-bottom: 5px;">Export CSV</a>
        </div>
        <?php
    }
}
add_action('manage_posts_extra_tablenav', 'wp_custom_add_export_button');

function wp_custom_handle_export_tickets() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'wp_custom_export_tickets_nonce')) {
        wp_die('Security check failed');
    }

    $args = array(
        'post_type' => 'ticket',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    
    // Apply Filters
    if(isset($_GET['m'])) {
        $m = sanitize_text_field($_GET['m']);
        if(strlen($m) === 6) { // YYYY MM format
            $year = substr($m, 0, 4);
            $month = substr($m, 4, 2);
            $args['date_query'] = array(
                array(
                    'year'  => $year,
                    'month' => $month,
                ),
            );
        }
    }
    
    if(isset($_GET['s'])) {
       $args['s'] = sanitize_text_field($_GET['s']);
    }

    $query = new WP_Query($args);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tickets-export-' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Add BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Headers
    fputcsv($output, array('ID', 'Date', 'Sender Name', 'Sender Email', 'Message', 'UTM Source', 'UTM Medium', 'UTM Campaign'));



    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Re-fetch meta to ensure freshness
            wp_cache_delete($post_id, 'post_meta');
            
            fputcsv($output, array(
                $post_id,
                get_the_date('Y-m-d H:i:s'),
                get_post_meta($post_id, '_wp_custom_sender_name', true),
                get_post_meta($post_id, '_wp_custom_sender_email', true),
                get_post_meta($post_id, '_wp_custom_message', true),
                get_post_meta($post_id, '_wp_custom_utm_source', true),
                get_post_meta($post_id, '_wp_custom_utm_medium', true),
                get_post_meta($post_id, '_wp_custom_utm_campaign', true)
            ));
        }
    }

    fclose($output);
    exit;
}
add_action('admin_post_wp_custom_export_tickets', 'wp_custom_handle_export_tickets');

// 8. Admin page styling for tickets

function wp_custom_ticket_admin_styles() {
    // Styles removed to use standard WordPress interface
}
add_action('admin_head', 'wp_custom_ticket_admin_styles');

function wp_custom_debug_info() {
    if (current_user_can('manage_options')) {
        echo '<h4>WP Custom Ticket Debug Info:</h4>';
        echo '<p><strong>Post Type Registered:</strong> ' . (post_type_exists('ticket') ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>AJAX URL:</strong> ' . admin_url('admin-ajax.php') . '</p>';
        echo '<p><strong>Current User Can Manage:</strong> ' . (current_user_can('manage_options') ? 'Yes' : 'No') . '</p>';
        echo '</div>';
    }
}

// Add debug info to admin footer (remove after testing)

// add_action('admin_footer', 'wp_custom_debug_info');

// 9. Statistics Page
function wp_custom_register_ticket_stats_page() {
    add_submenu_page(
        'edit.php?post_type=ticket',
        'Ticket Statistics',
        'Statistics',
        'manage_options',
        'ticket-stats',
        'wp_custom_ticket_stats_page_callback'
    );
}
add_action('admin_menu', 'wp_custom_register_ticket_stats_page');

function wp_custom_ticket_stats_page_callback() {
    global $wpdb;
    
    // --- DEMO DATA GENERATOR START ---
    // --- DEMO DATA GENERATOR START ---
    if (isset($_GET['generate_demo_data']) && current_user_can('manage_options')) {
        $demo_countries = [
            ['United States', 'US', 'New York'],
            ['Germany', 'DE', 'Berlin'],
            ['France', 'FR', 'Paris'],
            ['United Kingdom', 'GB', 'London'],
            ['Ukraine', 'UA', 'Kyiv'],
            ['Canada', 'CA', 'Toronto'],
            ['Australia', 'AU', 'Sydney'],
            ['Brazil', 'BR', 'Rio de Janeiro']
        ];
        
        // Fetch ALL tickets regardless of status or meta
        $posts_to_update = get_posts(array(
            'post_type' => 'ticket',
            'posts_per_page' => -1, // ALL posts
            'post_status' => 'any'
        ));
        
        $updated_count = 0;
        foreach ($posts_to_update as $p) {
            // Force update with random country
            $rand = $demo_countries[array_rand($demo_countries)];
            update_post_meta($p->ID, '_wp_custom_country', $rand[0]);
            update_post_meta($p->ID, '_wp_custom_country_code', $rand[1]);
            update_post_meta($p->ID, '_wp_custom_city', $rand[2]);
            
            // Backfill IP if missing
            if(!get_post_meta($p->ID, '_wp_custom_ip_address', true)) {
                update_post_meta($p->ID, '_wp_custom_ip_address', '8.8.8.8');
            }
            $updated_count++;
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>Successfully populated ' . $updated_count . ' tickets with demo geolocation data.</p></div>';
    
    } elseif (isset($_GET['clear_demo_data']) && current_user_can('manage_options')) {
        // --- CLEAR DEMO DATA BLOCK (Only runs if Generate is NOT running) ---
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_wp_custom_country', '_wp_custom_country_code', '_wp_custom_city')");
        echo '<div class="notice notice-success is-dismissible"><p>Successfully cleared all geolocation data from the map.</p></div>';
    }
    // --- DEMO DATA ACTIONS END ---
    
    
    // --- DATA PREPARATION START ---
    
    // 1. Last 7 Days
    $days_7_results = $wpdb->get_results("
        SELECT DATE(post_date) as date, COUNT(*) as count 
        FROM {$wpdb->posts} 
        WHERE post_type = 'ticket' 
        AND post_status = 'publish' 
        AND post_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
        GROUP BY DATE(post_date) 
        ORDER BY date ASC
    ");
    
    $data_7_days = array_fill(0, 7, 0);
    $labels_7_days = array();
    for($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels_7_days[] = date('d M', strtotime($date));
        foreach($days_7_results as $row) {
            if($row->date == $date) {
                $data_7_days[6-$i] = (int)$row->count;
                break;
            }
        }
    }
    
    // 2. This Month
    $month_results = $wpdb->get_results("
        SELECT DATE(post_date) as date, COUNT(*) as count 
        FROM {$wpdb->posts} 
        WHERE post_type = 'ticket' 
        AND post_status = 'publish' 
        AND MONTH(post_date) = MONTH(CURRENT_DATE()) 
        AND YEAR(post_date) = YEAR(CURRENT_DATE()) 
        GROUP BY DATE(post_date) 
        ORDER BY date ASC
    ");

    $m_labels = array();
    $m_data = array();
    $days_in_month = date('t');
    for($d=1; $d<=$days_in_month; $d++) {
        $m_labels[] = $d;
        $count = 0;
        $cur_date = date('Y-m-') . sprintf('%02d', $d);
        foreach($month_results as $row) {
            if($row->date == $cur_date) {
                $count = (int)$row->count;
                break;
            }
        }
        $m_data[] = $count;
    }

    // 3. This Year
    $year_results = $wpdb->get_results("
        SELECT DATE_FORMAT(post_date, '%Y-%m') as date, COUNT(*) as count 
        FROM {$wpdb->posts} 
        WHERE post_type = 'ticket' 
        AND post_status = 'publish' 
        AND YEAR(post_date) = YEAR(CURRENT_DATE()) 
        GROUP BY DATE_FORMAT(post_date, '%Y-%m') 
        ORDER BY date ASC
    ");
    
    $y_data = array_fill(0, 12, 0);
    foreach($year_results as $row) {
        $month_idx = (int)date('m', strtotime($row->date . '-01')) - 1;
        $y_data[$month_idx] = (int)$row->count;
    }

    // 4. Geolocation Data & Backfill
    // Attempt to backfill up to 3 tickets per page load if they have IP but no Country
    // This is a gentle way to populate the map without hitting API limits
    $tickets_to_backfill = get_posts(array(
        'post_type' => 'ticket',
        'posts_per_page' => 3,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_wp_custom_ip_address',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_wp_custom_country',
                'compare' => 'NOT EXISTS'
            )
        )
    ));

    foreach($tickets_to_backfill as $t) {
        $ip = get_post_meta($t->ID, '_wp_custom_ip_address', true);
        if($ip) {
            // Mock IP for local testing if needed
            if($ip == '127.0.0.1' || $ip == '::1') $ip = '8.8.8.8'; 
            
            $resp = wp_remote_get("http://ip-api.com/json/" . $ip, array('timeout' => 1));
            if (!is_wp_error($resp)) {
                $body = wp_remote_retrieve_body($resp);
                $geo = json_decode($body, true);
                if ($geo && $geo['status'] == 'success') {
                    update_post_meta($t->ID, '_wp_custom_country', $geo['country']);
                    update_post_meta($t->ID, '_wp_custom_country_code', $geo['countryCode']);
                    update_post_meta($t->ID, '_wp_custom_city', $geo['city']);
                } else {
                     // Mark as checked so we don't retry forever on failure
                     update_post_meta($t->ID, '_wp_custom_country', 'Unknown');
                }
            }
        }
    }

    // Now fetch Geo stats
    $geo_results = $wpdb->get_results("
        SELECT meta_value as country, COUNT(*) as count 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_wp_custom_country' 
        AND meta_value != 'Unknown'
        GROUP BY meta_value 
        ORDER BY count DESC
    ");
    
    $geo_data = array(['Country', 'Tickets']);
    foreach($geo_results as $row) {
        $geo_data[] = [$row->country, (int)$row->count];
    }
    
    // --- DATA PREPARATION END ---
    
    // Include Chart.js & Google Charts
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Ticket Statistics</h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="#charts" class="nav-tab nav-tab-active" id="tab-charts">Trends</a>
            <a href="#map" class="nav-tab" id="tab-map">Geolocation Map</a>
        </h2>

        <!-- CHARTS CONTENT -->
        <div id="view-charts" style="margin-top: 20px;">
            <div style="background: white; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 5px;">
                <div style="margin-bottom: 20px;">
                    <label for="stats-period" style="font-weight: bold; margin-right: 10px;">Select Period:</label>
                    <select id="stats-period" class="regular-text" style="width: auto;">
                        <option value="7days">Last 7 Days</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="ticketChart"></canvas>
                </div>
            </div>
        </div>

        <!-- MAP CONTENT -->
        <div id="view-map" style="margin-top: 20px; display: none;">
            <div style="background: white; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 5px;">
                <h3 style="margin-top:0;">Tickets by Country</h3>
                <?php if(count($geo_data) <= 1): ?>
                    <div style="text-align: center; padding: 40px;">
                        <p style="font-size: 16px; margin-bottom: 20px;">No geolocation data available yet for the map.</p>
                        <p>Old tickets may not have saved IP addresses. You can generate random demo data to see the map in action:</p>
                        <a href="<?php echo add_query_arg('generate_demo_data', '1', remove_query_arg('clear_demo_data')); ?>" class="button button-primary button-large">Generate Demo Data</a>
                    </div>
                <?php else: ?>
                    <div id="regions_div" style="width: 100%; height: 500px;"></div>
                    <p style="text-align: right; margin-top: 10px; font-size: 12px; color: #666;">
                        <a href="<?php echo add_query_arg('clear_demo_data', '1', remove_query_arg('generate_demo_data')); ?>" style="color: #a00; text-decoration: none;" onclick="return confirm('Remove all map data?');">Reset Map Data</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // TABS LOGIC
            const tabCharts = document.getElementById('tab-charts');
            const tabMap = document.getElementById('tab-map');
            const viewCharts = document.getElementById('view-charts');
            const viewMap = document.getElementById('view-map');

            tabCharts.addEventListener('click', function(e) {
                e.preventDefault();
                tabCharts.classList.add('nav-tab-active');
                tabMap.classList.remove('nav-tab-active');
                viewCharts.style.display = 'block';
                viewMap.style.display = 'none';
            });

            tabMap.addEventListener('click', function(e) {
                e.preventDefault();
                tabMap.classList.add('nav-tab-active');
                tabCharts.classList.remove('nav-tab-active');
                viewMap.style.display = 'block';
                viewCharts.style.display = 'none';
                /* Redraw map */
                if(window.drawRegionsMap) window.drawRegionsMap();
            });

            // CHART.JS LOGIC
            const ctx = document.getElementById('ticketChart').getContext('2d');
            
            // Raw Data from PHP
            const rawData = {
                '7days': {
                    labels: <?php echo json_encode($labels_7_days); ?>,
                    data: <?php echo json_encode($data_7_days); ?>,
                    label: 'Tickets (Last 7 Days)'
                },
                'month': {
                    labels: <?php echo json_encode($m_labels); ?>,
                    data: <?php echo json_encode($m_data); ?>,
                    label: 'Tickets (This Month)'
                },
                'year': {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    data: <?php echo json_encode($y_data); ?>,
                    label: 'Tickets (This Year)'
                }
            };
            
            let chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: rawData['month'].labels,
                    datasets: [{
                        label: rawData['month'].label,
                        data: rawData['month'].data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
            
            // Set initial selection to Month
            document.getElementById('stats-period').value = 'month';
            
            // Switch Logic
            document.getElementById('stats-period').addEventListener('change', function(e) {
                const val = e.target.value;
                const dataset = rawData[val];
                chartInstance.data.labels = dataset.labels;
                chartInstance.data.datasets[0].data = dataset.data;
                chartInstance.data.datasets[0].label = dataset.label;
                chartInstance.update();
            });

            // GOOGLE MAPS LOGIC
            google.charts.load('current', {
                'packages':['geochart']
            });
            google.charts.setOnLoadCallback(function() {
                window.drawRegionsMap();
            });

            window.drawRegionsMap = function() {
                var container = document.getElementById('regions_div');
                if(!container) return;
                
                var data = google.visualization.arrayToDataTable(<?php echo json_encode($geo_data); ?>);

                var options = {
                    colorAxis: {colors: ['#e0f7fa', '#0288d1']},
                    backgroundColor: '#ffffff',
                    datalessRegionColor: '#f5f5f5',
                    defaultColor: '#f5f5f5',
                };

                var chart = new google.visualization.GeoChart(container);
                chart.draw(data, options);
            }
        });
        </script>
    </div>
    <?php
}