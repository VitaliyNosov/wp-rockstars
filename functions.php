<?php
// TEMPORARY: FORCE COMMENTS OPEN (DEBUGGING)
add_filter('comments_open', '__return_true');

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
    require_once get_template_directory() . '/inc/carbon-fields-init-landing.php'; 
    require_once get_template_directory() . '/inc/graphql-register.php';
    require_once get_template_directory() . '/inc/graphql/landing-graphql.php';
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

    // Post Likes Script
    wp_enqueue_script('post-likes', get_template_directory_uri() . '/common/js/post-likes.js', [], '1.0', true);
    wp_localize_script('post-likes', 'rock_stars_likes', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rock_stars_like_nonce')
    ));


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

// Custom Styles for Post Likes
function rock_stars_like_styles() {
    ?>
    <style>
        .liked-post {
            color: #ef4444 !important; /* text-red-500 */
        }
        .liked-post svg {
            fill: currentColor !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'rock_stars_like_styles');

// Колличество слов в карточки постов

function custom_excerpt_length($length) {
    return 20; // Количество слов
}
add_filter('excerpt_length', 'custom_excerpt_length');


// Custom Admin Styles for Carbon Fields
function rock_stars_admin_style() {
    echo '<style>
        .cf-complex__tabs-item--tabbed-vertical.cf-complex__tabs-item--current,
        .cf-complex__tabs-item--tabbed-horizontal.cf-complex__tabs-item--current,
        .cf-container__tabs-item.cf-container__tabs-item--current {
            background-color: #2271b1 !important;
            color: #ffffff !important;
            border-color: #2271b1 !important;
        }
        .cf-container__tabs-item.cf-container__tabs-item--current button {
            color: #ffffff !important;
        }
        .cf-complex__tabs-item--tabbed-vertical.cf-complex__tabs-item--current:hover,
        .cf-complex__tabs-item--tabbed-horizontal.cf-complex__tabs-item--current:hover,
        .cf-container__tabs-item.cf-container__tabs-item--current:hover {
            background-color: #135e96 !important;
            color: #ffffff !important;
        }
    </style>';
}
add_action('admin_head', 'rock_stars_admin_style');
























// Include AJAX Comment Handler
require_once get_template_directory() . '/inc/functions/ajax-comments.php';

// Custom Comment Callback
function rock_stars_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <div id="comment-<?php comment_ID(); ?>" <?php comment_class('mb-6'); ?>>
        <div class="comment-body-card flex items-start bg-primary bg-opacity-[3%] dark:bg-dark p-6 rounded-[22px] rounded-tl-none">
            <div class="flex-shrink-0 mr-4">
                <?php echo get_avatar( $comment, 50, '', '', array('class' => 'rounded-full') ); ?>
            </div>
            <div class="flex-grow">
                <!-- Header: Author Name & Date -->
                <div class="flex flex-wrap items-center mb-2">
                    <h4 class="font-bold text-black dark:text-white text-base mr-4">
                        <?php echo get_comment_author(); ?>
                    </h4>
                    <span class="text-sm text-body-color">
                        <?php printf(__('%1$s at %2$s', 'rock-star'), get_comment_date(), get_comment_time()); ?>
                    </span>
                </div>
                
                <!-- Comment Body -->
                <div class="text-base text-body-color mb-3">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p class="italic text-yellow-500 mb-2"><?php _e('Your comment is awaiting moderation.', 'rock-star'); ?></p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>
            </div>
        </div>
    <?php
}

function rock_stars_enqueue_comments_script() {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // AJAX Comments Script
    wp_enqueue_script('rock-stars-ajax-comments', get_template_directory_uri() . '/common/js/ajax-comments.js', array('jquery'), time(), true);
    wp_localize_script('rock-stars-ajax-comments', 'rock_stars_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('comment_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'rock_stars_enqueue_comments_script');
