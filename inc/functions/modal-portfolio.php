<?php

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