<?php
/**
 * Plugin Name: Rock Stars Quiz Standalone
 * Plugin URI: https://rock-stars.com
 * Description: A standalone version of the Rock Stars Quiz module.
 * Version: 1.0.0
 * Author: Rock Stars Team
 * Author URI: https://rock-stars.com
 * Text Domain: rock-stars-quiz
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define Constants
define('QUIZ_P_VERSION', '1.0.0');
define('QUIZ_P_DIR', plugin_dir_path(__FILE__));
define('QUIZ_P_URL', plugin_dir_url(__FILE__));

/**
 * Check for Carbon Fields dependency
 */
add_action('admin_notices', 'quiz_p_check_dependencies');
function quiz_p_check_dependencies() {
    if (!class_exists('\\Carbon_Fields\\Carbon_Fields')) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Rock Stars Quiz Standalone requires Carbon Fields library to be active.', 'rock-stars-quiz'); ?></p>
        </div>
        <?php
    }
}

/**
 * Initialize Plugin Components
 */
function quiz_p_init() {
    // Load components
    require_once QUIZ_P_DIR . 'includes/quiz-settings.php';
    require_once QUIZ_P_DIR . 'includes/quiz-submissions.php';
    
    // Actions
    add_action('wp_footer', 'quiz_p_render_modal');
    add_action('wp_enqueue_scripts', 'quiz_p_enqueue_assets');
    add_action('admin_enqueue_scripts', 'quiz_p_admin_assets');
}
add_action('plugins_loaded', 'quiz_p_init');

/**
 * Enqueue Frontend Assets
 */
function quiz_p_enqueue_assets() {
    // External Libraries (CDN)
    wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest', array(), null, true);
    wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13');
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true);
    wp_enqueue_script('flatpickr-ru', 'https://npmcdn.com/flatpickr/dist/l10n/ru.js', array('flatpickr'), '4.6.13', true);

    // Styles
    wp_enqueue_style('quiz-p-frontend', QUIZ_P_URL . 'assets/css/quiz-frontend.css', array('flatpickr'), QUIZ_P_VERSION);
    
    // Scripts
    wp_enqueue_script('quiz-p-widget', QUIZ_P_URL . 'assets/js/quiz-widget.js', array('jquery', 'lucide', 'flatpickr'), QUIZ_P_VERSION, true);
    
    // Localize
    $accent_color = function_exists('carbon_get_theme_option') ? carbon_get_theme_option('quiz_accent_color') : '#4A6CF7';
    
    $custom_css = "
        :root {
            --quiz-primary: {$accent_color};
        }
    ";
    wp_add_inline_style('quiz-p-frontend', $custom_css);
    
    // Get total steps for JS
    if (function_exists('carbon_get_theme_option')) {
        $structure = carbon_get_theme_option('quiz_structure');
        $total_steps = is_array($structure) ? count($structure) : 1;
        
        wp_localize_script('quiz-p-widget', 'quiz_p_ajax', array(
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('quiz_p_nonce'),
            'total_steps' => $total_steps
        ));
    }
}

/**
 * Enqueue Admin Assets
 */
function quiz_p_admin_assets() {
    wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest', array(), null, true);
    wp_enqueue_style('quiz-p-admin-css', QUIZ_P_URL . 'assets/css/quiz-admin.css', array(), QUIZ_P_VERSION);
    wp_enqueue_script('quiz-p-admin-js', QUIZ_P_URL . 'assets/js/quiz-admin.js', array('jquery', 'lucide'), QUIZ_P_VERSION, true);
}

/**
 * Render Modal in Footer
 */
function quiz_p_render_modal() {
    include_once QUIZ_P_DIR . 'includes/quiz-modal.php';
    if (function_exists('quiz_p_render_modal_html')) {
        quiz_p_render_modal_html();
    }
}

