<?php
/**
 * Quiz Module Initialization
 * This file serves as the main entry point (hook) for all quiz functionality.
 */

// 1. Include PHP components
$quiz_dir = __DIR__ . '/';

require_once $quiz_dir . 'quiz-settings.php';
require_once $quiz_dir . 'quiz-submissions.php';
require_once $quiz_dir . 'quiz-modal.php';

/**
 * Enqueue Frontend Assets
 */
add_action('wp_enqueue_scripts', function() {
    // Lucide Icons
    wp_enqueue_script('lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true);
    
    // Flatpickr (Premium Date Picker)
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
    wp_enqueue_script('flatpickr-ru', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js', ['flatpickr-js'], '4.6.13', true);

    // Quiz Main Styles
    wp_enqueue_style('quiz-frontend', get_template_directory_uri() . '/inc/quiz/assets/css/quiz-frontend.css', [], '1.0.1');

    // Dynamic Styles (Accent Color & Font)
    if (function_exists('carbon_get_theme_option')) {
        $accent_color = carbon_get_theme_option('quiz_accent_color') ?: '#4A6CF7';
        $font_family = carbon_get_theme_option('quiz_font_family');
        
        $inline_css = ":root { --quiz-primary: {$accent_color}; }";
        
        if ($font_family === 'custom') {
            $font_name = carbon_get_theme_option('quiz_custom_font_name');
            if ($font_name) {
                $quoted_font = (strpos($font_name, "'") === false && strpos($font_name, '"') === false) 
                    ? "'" . $font_name . "', sans-serif" 
                    : $font_name;
                $inline_css .= " .quiz-container { font-family: {$quoted_font}; }";
            }
        } elseif (!empty($font_family)) {
            $inline_css .= " .quiz-container { font-family: {$font_family}; }";
        }

        wp_add_inline_style('quiz-frontend', $inline_css);
    }

    // Quiz Main Script
    wp_enqueue_script(
        'quiz-widget', 
        get_template_directory_uri() . '/inc/quiz/assets/js/quiz-widget.js', 
        array('jquery', 'lucide-icons', 'flatpickr-js'), 
        '1.0.5', 
        true
    );
});

/**
 * Enqueue Admin Assets
 */
add_action('admin_enqueue_scripts', function($hook) {
    wp_enqueue_script('lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true);
    
    // Quiz Admin Styles
    wp_enqueue_style('quiz-admin', get_template_directory_uri() . '/inc/quiz/assets/css/quiz-admin.css', [], '1.0.1');

    wp_enqueue_script(
        'quiz-admin-js', 
        get_template_directory_uri() . '/inc/quiz/assets/js/quiz-admin.js', 
        array('jquery'), 
        '1.0.2', 
        true
    );
    
    // Pass icon list to JS (icons defined in quiz-settings.php)
    if (function_exists('rockstars_get_quiz_icons')) {
        wp_localize_script('quiz-admin-js', 'quizIconData', array(
            'icons' => rockstars_get_quiz_icons()
        ));
    }
});
