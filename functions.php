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


