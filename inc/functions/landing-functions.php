<?php


function add_landing_page_body_class($classes) {
    global $post;
    
    if (isset($post->ID)) {
        $template = get_post_meta($post->ID, '_wp_page_template', true);
        
        if ($template) {
            $template_lower = strtolower($template);
            
            if (strpos($template_lower, 'about') !== false) {
                $classes[] = 'landing-page-id';
            }
            
            if (strpos($template_lower, 'landings-template.php') !== false) {
                $classes[] = 'landing-page-id';
            }
        }
    }
    
    return $classes;
}
add_filter('body_class', 'add_landing_page_body_class');


add_action('wp_enqueue_scripts', function () {
    $body_classes = get_body_class();

    if (in_array('landing-page-id', $body_classes)) {
        wp_enqueue_style(
            'landings-mod',
            get_template_directory_uri() . '/common/css/landings-mod.css',
            [],
            null
        );

        wp_enqueue_script(
            'tailwind-cdn',
            'https://cdn.tailwindcss.com',
            [],
            null,
            true
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










