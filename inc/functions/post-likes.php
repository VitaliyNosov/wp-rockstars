<?php

add_action('wp_ajax_rock_stars_like_post', 'rock_stars_like_post');
add_action('wp_ajax_nopriv_rock_stars_like_post', 'rock_stars_like_post');

function rock_stars_like_post() {
    // Проверка nonce для безопасности
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rock_stars_like_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $action = isset($_POST['like_action']) ? $_POST['like_action'] : 'add';

    if ($post_id > 0) {
        // Получаем текущее количество лайков
        $likes = get_post_meta($post_id, '_post_likes_count', true);
        $likes = $likes ? intval($likes) : 0;

        if ($action === 'add') {
            $likes++;
        } elseif ($action === 'remove') {
            $likes--;
            if ($likes < 0) $likes = 0;
        }

        // Обновляем мета-поле
        update_post_meta($post_id, '_post_likes_count', $likes);

        // Возвращаем новое значение
        wp_send_json_success(array('likes' => $likes));
    } else {
        wp_send_json_error('Invalid post ID');
    }

    wp_die();
}
