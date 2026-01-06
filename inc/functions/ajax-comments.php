<?php
/**
 * AJAX Comment Handling
 *
 * @package Rock_Star
 */

function rock_stars_ajax_comment_handler() {
    // Check nonce
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'comment_nonce')) {
        wp_send_json_error('Security check failed.');
    }

    $comment_data = array(
        'comment_post_ID' => isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0,
        'author'          => isset($_POST['author']) ? trim($_POST['author']) : '',
        'email'           => isset($_POST['email']) ? trim($_POST['email']) : '',
        'url'             => isset($_POST['url']) ? trim($_POST['url']) : '',
        'comment'         => isset($_POST['comment']) ? trim($_POST['comment']) : '',
        'comment_type'    => '',
        'comment_parent'  => isset($_POST['comment_parent']) ? (int) $_POST['comment_parent'] : 0,
        'user_id'         => get_current_user_id(),
    );

    // Validate standard WordPress comment data
    $comment = wp_handle_comment_submission($comment_data);

    if (is_wp_error($comment)) {
        // Prepare Debug Info
        $debug_info = " [Recv: ";
        $debug_info .= "Name='" . (isset($_POST['author']) ? substr($_POST['author'], 0, 10) : 'NULL') . "', ";
        $debug_info .= "Email='" . (isset($_POST['email']) ? substr($_POST['email'], 0, 10) : 'NULL') . "', ";
        $debug_info .= "ID='" . (isset($_POST['comment_post_ID']) ? $_POST['comment_post_ID'] : 'NULL') . "']";

        $data = intval($comment->get_error_data());
        if (!empty($data)) {
            wp_send_json_error($comment->get_error_message() . $debug_info);
        } else {
            wp_send_json_error('Unknown error. ' . $debug_info);
        }
    }

    /*
     * If successful, render the comment markup to return to the frontend.
     * We use a dummy dummy WP_Comment_Query loop or just calling the callback directly?
     * Standard way involves getting the comment and rendering it.
     */
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);

    // Render the new comment
    ob_start();
    $GLOBALS['comment'] = $comment;
    
    // We reuse the callback logic from comments.php. 
    // Ideally this callback should have been a named function, 
    // but since it was an anonymous function in comments.php, we'll replicate the structure here 
    // or refactor comments.php to use a named function.
    // For now, let's replicate the structure to generate the same HTML.
    
    $args = array(
        'max_depth' => get_option('thread_comments_depth'),
        'style' => 'div'
    );
    $depth = 1; // New comments at top level usually, or check parent
    if ($comment->comment_parent > 0) {
        // Logic for depth calculation is complex without walking the tree, 
        // but for AJAX appending usually we just append.
    }
    
    // Render
    // Use the shared callback to ensure identical HTML structure
    rock_stars_comment_callback($comment, $args, $depth);
    echo '</div>'; // Manually close the div since the callback leaves it open for Walker_Comment compatibility
    $comment_html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $comment_html,
        'message' => 'Comment submitted successfully'
    ));
}

add_action('wp_ajax_rock_stars_submit_comment', 'rock_stars_ajax_comment_handler');
add_action('wp_ajax_nopriv_rock_stars_submit_comment', 'rock_stars_ajax_comment_handler');
