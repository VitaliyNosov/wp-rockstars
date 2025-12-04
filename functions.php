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


// Carbon libs

use Carbon_Fields\Carbon_Fields;

add_action( 'after_setup_theme', function() {
    require_once __DIR__ . '/vendor/autoload.php'; // путь к autoload.php

    Carbon_Fields::boot();

    require_once get_template_directory() . '/inc/carbon-fields-init.php'; 
    require_once get_template_directory() . '/inc/graphql-register.php';
});


// Files functions -  inc/functions/

$functions_dir = get_template_directory() . '/inc/functions';

foreach (glob($functions_dir . '/*.php') as $file) {
    require_once $file;
}



// Enqueue styles and scripts

function theme_enqueue_assets() {

    // Enqueue main stylesheet

    wp_enqueue_style('theme-style-tailwind', get_template_directory_uri() . '/common/css/style.css', [], '1.0');
    wp_enqueue_style('theme-style-mod-tailwind', get_template_directory_uri() . '/common/css/style-mod.css', [], '1.0');

 
    // Enqueue JavaScript files - in footer

    wp_enqueue_script('bundle-js', get_template_directory_uri() . '/common/js/bundle.js', array(), '1.0', true);
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/common/js/custom.js', array(), '1.0', true);
    wp_enqueue_script('earth-js', get_template_directory_uri() . '/common/js/earth.js', array(), '1.0', true);
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js', [], null, true);
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
    wp_enqueue_style('glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', [], '3.2.0');
    wp_enqueue_script('glightbox-js', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', [], '3.2.0', true);
    wp_enqueue_style( 'plyr-css', 'https://cdn.plyr.io/3.7.8/plyr.css', [], null );
	wp_enqueue_script( 'plyr-js', 'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js', [], null, true );
    wp_enqueue_script( 'wavesurfer-js', 'https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js', [], null, true );
    // Инициализация в DOMContentLoaded
    wp_add_inline_script('glightbox-js', '
      document.addEventListener("DOMContentLoaded", function() {
        const lightbox = GLightbox({ selector: ".glightbox" });
      });
    ');


}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');


// Tailwind и Preline для лендингов

add_action('wp_enqueue_scripts', function () {
    // Массив ID страниц, на которых нужно подключать Tailwind и Preline
    $allowed_pages = [1548, 1562]; // Добавь сюда другие ID страниц, если нужно

    if (is_page($allowed_pages)) {
        wp_enqueue_script(
            'tailwind-cdn',
            'https://cdn.tailwindcss.com',
            [],
            null,
            false
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

// Add CORS headers for uploaded media files
add_action('template_redirect', function() {
    // Check if this is a request for an uploaded file
    if (strpos($_SERVER['REQUEST_URI'], '/wp-content/uploads/') !== false) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }
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























