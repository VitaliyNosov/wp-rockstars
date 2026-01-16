<?php
/**
 * Quiz Submissions - Custom Post Type and AJAX Handler
 */

// 1. Register Custom Post Type 'Quiz Submission'
function quiz_register_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Quiz Submissions',
            'singular_name' => 'Quiz Submission',
            'menu_name' => 'Quiz Submissions',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Submission',
            'edit_item' => 'Edit Submission',
            'new_item' => 'New Submission',
            'view_item' => 'View Submission',
            'search_items' => 'Search Submissions',
            'not_found' => 'No submissions found',
            'not_found_in_trash' => 'No submissions found in trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-list-view',
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'custom-fields'),
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_rest' => false,
        'capabilities' => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true
    );
    register_post_type('quiz_submission', $args);
}
add_action('init', 'quiz_register_post_type');

// Helper to get structure
function quiz_get_structure_helper() {
    $structure = [];
    if (function_exists('get_quiz_structure')) {
        $structure = get_quiz_structure();
    } elseif (function_exists('carbon_get_theme_option')) {
         $structure = carbon_get_theme_option('quiz_structure');
    }
    return $structure;
}

// 2. Add custom columns to admin list
function quiz_custom_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'Submission',
        'user_name' => 'Name',
        'user_email' => 'Email',
        'quiz_actions' => 'Actions', // New column for Quick View
        'date' => 'Date'
    );
    return $new_columns;
}
add_filter('manage_quiz_submission_posts_columns', 'quiz_custom_columns');

// 3. Fill custom columns with data
function quiz_column_content($column, $post_id) {
    if ($column === 'user_name') {
        echo esc_html(get_post_meta($post_id, '_quiz_user_name', true));
    }
    if ($column === 'user_email') {
        echo esc_html(get_post_meta($post_id, '_quiz_user_email', true));
    }
    if ($column === 'quiz_actions') {
        echo '<button type="button" class="button button-small open-quiz-modal" data-id="' . $post_id . '"><span class="dashicons dashicons-visibility" style="vertical-align: text-top; color: #444;"></span> View Details</button>';
    }
}
add_action('manage_quiz_submission_posts_custom_column', 'quiz_column_content', 10, 2);

// 4. Add meta box to show quiz details in admin
function quiz_meta_box() {
    add_meta_box(
        'quiz-details',
        'Quiz Details',
        'quiz_meta_box_callback',
        'quiz_submission',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'quiz_meta_box');

function quiz_meta_box_callback($post) {
    echo quiz_get_submission_html($post->ID);
}

// SHARED RENDER LOGIC (Used by MetaBox and AJAX Modal)
function quiz_get_submission_html($post_id) {
    $user_name = get_post_meta($post_id, '_quiz_user_name', true);
    $user_email = get_post_meta($post_id, '_quiz_user_email', true);
    $submission_time = get_post_meta($post_id, '_quiz_submission_time', true);
    $ip_address = get_post_meta($post_id, '_quiz_ip_address', true);

    ob_start();
    ?>
    <div class="quiz-submission-view" style="background: #ffffff; color: #1e293b; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        
        <!-- Header -->
        <h4 style="color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 8px; margin-bottom: 15px; margin-top: 0; font-size: 1.2em;">👤 User Information</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8fafc; padding: 15px; border-radius: 6px;">
            <p style="margin: 0; line-height: 1.6; color: #334155;"><strong style="color: #64748b;">Name:</strong> <span style="font-weight: 500; color: #0f172a;"><?php echo esc_html($user_name ?: 'Anonymous'); ?></span></p>
            <p style="margin: 0; line-height: 1.6; color: #334155;"><strong style="color: #64748b;">Email:</strong> <span style="font-weight: 500; color: #0f172a;"><?php echo esc_html($user_email ?: 'N/A'); ?></span></p>
            <p style="margin: 0; line-height: 1.6; color: #334155;"><strong style="color: #64748b;">Submitted:</strong> <?php echo esc_html($submission_time); ?></p>
            <p style="margin: 0; line-height: 1.6; color: #334155;"><strong style="color: #64748b;">IP Address:</strong> <?php echo esc_html($ip_address); ?></p>
        </div>

        <h4 style="color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 8px; margin-bottom: 15px; margin-top: 20px; font-size: 1.2em;">📋 Quiz Responses</h4>
        
        <?php
        $structure = quiz_get_structure_helper();
        $displayed_keys = [];

        // 1. PRIMARY LOOP
        if (!empty($structure)) {
            foreach ($structure as $step) {
                $step_title = isset($step['step_title']) ? $step['step_title'] : 'Step';
                $has_data_in_step = false;
                ob_start(); 
                
                if (!empty($step['step_fields'])) {
                    foreach ($step['step_fields'] as $field) {
                        $key = $field['field_name'];
                        $label = $field['field_label'];
                        $meta_key = '_quiz_' . $key;
                        $val = get_post_meta($post_id, $meta_key, true);
                        
                        if ($val === '' || $val === false || $val === []) continue;
                        
                        $displayed_keys[] = $meta_key;
                        $has_data_in_step = true;
                        
                        // Format Value
                        $display_value = $val;
                        if (!empty($field['field_options'])) {
                             $options = [];
                             $lines = explode("\n", $field['field_options']);
                             foreach ($lines as $line) {
                                $parts = explode(':', $line, 2);
                                if(count($parts) === 2) $options[trim($parts[0])] = trim($parts[1]);
                                else $options[trim($line)] = trim($line);
                             }
                             if (is_array($val)) {
                                 $mapped = [];
                                 foreach($val as $v) $mapped[] = isset($options[$v]) ? $options[$v] : $v;
                                 $display_value = implode(', ', $mapped);
                             } else {
                                 $display_value = isset($options[$val]) ? $options[$val] : $val;
                             }
                        } elseif (is_array($val)) {
                            $display_value = implode(', ', $val);
                        }
                        
                        echo '<div style="background: #f1f5f9; padding: 12px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #cbd5e1;">';
                        echo '<strong style="color: #64748b; display:block; margin-bottom: 6px; font-size: 0.85em; text-transform:uppercase; letter-spacing:0.5px;">' . esc_html($label) . '</strong>';
                        echo '<div style="color: #0f172a; white-space: pre-wrap; line-height: 1.5; font-size: 1.05em;">' . wp_kses_post($display_value) . '</div>';
                        echo '</div>';
                    }
                }
                $step_content = ob_get_clean();
                if ($has_data_in_step) {
                    echo '<h5 style="color: #94a3b8; margin: 25px 0 10px 0; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">' . esc_html($step_title) . '</h5>';
                    echo $step_content;
                }
            }
        }
        
        // 2. SECONDARY LOOP (Fail-Safe)
        $all_meta = get_post_meta($post_id);
        $system_keys = ['_quiz_user_name', '_quiz_user_email', '_quiz_submission_time', '_quiz_ip_address', '_quiz_debug_dump', '_edit_lock', '_edit_last'];
        $orphan_content = '';
        
        foreach ($all_meta as $key => $values) {
            if (strpos($key, '_quiz_') !== 0) continue;
            if (in_array($key, $system_keys)) continue;
            if (in_array($key, $displayed_keys)) continue; 
            
            $clean_key = str_replace('_quiz_', '', $key);
            $val = $values[0];
            $val = maybe_unserialize($val); 
            if (empty($val)) continue;
            
            $orphan_content .= '<div style="background: #fef2f2; padding: 12px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #ef4444;">';
            $orphan_content .= '<strong style="color: #ef4444; display:block; margin-bottom: 4px;">' . esc_html(ucfirst(str_replace('_', ' ', $clean_key))) . ' <span style="opacity:0.7; font-size:0.8em;">(Unmapped Field)</span></strong>';
            $orphan_content .= '<div style="color: #7f1d1d; white-space: pre-wrap;">' . print_r($val, true) . '</div>';
            $orphan_content .= '</div>';
        }
        
        if (!empty($orphan_content)) {
            echo '<h5 style="color: #ef4444; margin: 25px 0 10px 0; border-top: 1px dashed #fecaca; padding-top: 15px;">⚠️ Additional Data (Not in current Quiz Structure)</h5>';
            echo $orphan_content;
        }
        
        ?>
    </div>
    <?php
    return ob_get_clean();
}

// 7. AJAX Handler for Quick View
function quiz_ajax_get_details() {
    // Basic formatting for AJAX response
    $id = intval($_POST['id']);
    if (!$id || get_post_type($id) !== 'quiz_submission') {
        wp_send_json_error('Invalid ID');
    }
    
    $html = quiz_get_submission_html($id);
    wp_send_json_success($html);
}
add_action('wp_ajax_quiz_get_details', 'quiz_ajax_get_details');

// 8. Admin Footer Scripts
function quiz_admin_footer_scripts() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'quiz_submission') return;
    ?>
    <style>
        /* Simple Admin Modal */
        #quiz-admin-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 100000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }
        #quiz-admin-modal.active { display: flex; }
        .quiz-admin-modal-content {
            background: #fff;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            position: relative;
        }
        .quiz-admin-modal-body {
            /* Content injected here */
        }
        .quiz-admin-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 30px;
            color: #333;
            cursor: pointer;
            z-index: 10;
            opacity: 0.6;
        }
        .quiz-admin-close:hover { opacity: 1; }
    </style>
    
    <div id="quiz-admin-modal">
        <div class="quiz-admin-modal-content">
            <span class="quiz-admin-close">&times;</span>
            <div class="quiz-admin-modal-body" id="quiz-modal-target"></div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.open-quiz-modal').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var modal = $('#quiz-admin-modal');
            var target = $('#quiz-modal-target');
            
            target.html('<div style="padding: 50px; text-align: center; color: #666;">Loading...</div>');
            modal.addClass('active');
            
            $.post(ajaxurl, {
                action: 'quiz_get_details',
                id: id
            }, function(response) {
                if (response.success) {
                    target.html(response.data);
                } else {
                    target.html('<div style="padding: 20px; color: red;">Error loading data</div>');
                }
            });
        });
        
        $('.quiz-admin-close, #quiz-admin-modal').on('click', function(e) {
            if (e.target === this) {
                $('#quiz-admin-modal').removeClass('active');
            }
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'quiz_admin_footer_scripts');

// 5. AJAX handler for quiz submission (Keep existing)
function quiz_handle_submission() {
    
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quiz_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Get Basic Data (with smart fallback)
    $raw_user_name = isset($_POST['user_name']) ? sanitize_text_field($_POST['user_name']) : '';
    $user_name = ($raw_user_name && $raw_user_name !== 'Anonymous') ? $raw_user_name : '';
    
    $user_email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';

    // If Name/Email are generic/empty, try to find them in other fields
    if (empty($user_name) || $user_name === 'Anonymous') {
        foreach ($_POST as $key => $val) {
            // Skip system keys
            if (in_array($key, ['action', 'nonce', 'user_name', 'user_email'])) continue;
            
            // Check for Name-like keys
            // checks for: name, fio, imya (russian), client, full_name
            if (is_string($val) && !empty($val)) {
                $k = strtolower($key);
                if (
                    strpos($k, 'name') !== false || 
                    strpos($k, 'fio') !== false || 
                    $k === 'imya' || 
                    strpos($k, 'client') !== false
                ) {
                    $user_name = sanitize_text_field($val);
                    break; // Found a name candidate
                }
            }
        }
    }
    
    if (empty($user_name)) $user_name = 'Anonymous';

    if (empty($user_email)) {
        foreach ($_POST as $key => $val) {
             if (in_array($key, ['action', 'nonce', 'user_name', 'user_email'])) continue;
             
             if (is_string($val) && is_email($val)) {
                 $user_email = sanitize_email($val);
                 break; // Found an email candidate
             }
        }
    }
    
    // Create post
    $post_data = array(
        'post_title' => 'Quiz from ' . $user_name,
        'post_content' => 'Quiz submission from ' . ($user_email ?: 'Unknown Email'),
        'post_status' => 'publish',
        'post_type' => 'quiz_submission',
        'post_author' => 1
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        wp_send_json_error('Failed to save submission');
    }
    
    // Save Standard Meta
    update_post_meta($post_id, '_quiz_user_name', $user_name);
    update_post_meta($post_id, '_quiz_user_email', $user_email);
    update_post_meta($post_id, '_quiz_submission_time', current_time('mysql'));
    update_post_meta($post_id, '_quiz_ip_address', $_SERVER['REMOTE_ADDR']);
    
    // Save All Dynamic Fields
    $exclude_keys = ['action', 'nonce', 'user_name', 'user_email'];
    
    foreach ($_POST as $key => $value) {
        if (in_array($key, $exclude_keys)) continue;
        
        if (is_array($value)) {
            $clean_val = array_map('sanitize_text_field', $value);
        } else {
            $clean_val = sanitize_textarea_field($value);
        }
        
        update_post_meta($post_id, '_quiz_' . $key, $clean_val);
    }
    
    // Debug: Save raw POST data
    update_post_meta($post_id, '_quiz_debug_dump', print_r($_POST, true));
    
    wp_send_json_success(array(
        'message' => 'Quiz submitted successfully',
        'submission_id' => $post_id
    ));
}
add_action('wp_ajax_submit_quiz', 'quiz_handle_submission');
add_action('wp_ajax_nopriv_submit_quiz', 'quiz_handle_submission');

// 6. Enqueue scripts (Keep existing)
function quiz_enqueue_scripts() {
    if (!is_admin()) {
        // Localize script for AJAX
        wp_localize_script('jquery', 'quiz_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quiz_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'quiz_enqueue_scripts');
