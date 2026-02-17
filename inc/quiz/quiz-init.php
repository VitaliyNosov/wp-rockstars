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
function rock_stars_quiz_enqueue_frontend_assets() {
	// Lucide Icons
	wp_enqueue_script( 'lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true );

	// Flatpickr (Premium Date Picker)
	wp_enqueue_style( 'flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13' );
	wp_enqueue_script( 'flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true );
	wp_enqueue_script( 'flatpickr-ru', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js', array( 'flatpickr-js' ), '4.6.13', true );

	// Quiz Main Styles
	wp_enqueue_style( 'quiz-frontend', get_template_directory_uri() . '/inc/quiz/assets/css/quiz-frontend.css', array(), '1.0.1' );

	// Dynamic Styles (Accent Color & Font)
	if ( function_exists( 'carbon_get_theme_option' ) ) {
		$rock_stars_accent_color = carbon_get_theme_option( 'quiz_accent_color' ) ?: '#4A6CF7';
		$rock_stars_font_family  = carbon_get_theme_option( 'quiz_font_family' );

		$rock_stars_inline_css = ':root { --quiz-primary: ' . esc_attr( $rock_stars_accent_color ) . '; }';

		if ( 'custom' === $rock_stars_font_family ) {
			$rock_stars_font_name = carbon_get_theme_option( 'quiz_custom_font_name' );
			if ( $rock_stars_font_name ) {
				$rock_stars_quoted_font = ( false === strpos( $rock_stars_font_name, "'" ) && false === strpos( $rock_stars_font_name, '"' ) )
					? "'" . $rock_stars_font_name . "', sans-serif"
					: $rock_stars_font_name;
				$rock_stars_inline_css .= ' .quiz-container { font-family: ' . esc_attr( $rock_stars_quoted_font ) . '; }';
			}
		} elseif ( ! empty( $rock_stars_font_family ) ) {
			$rock_stars_inline_css .= ' .quiz-container { font-family: ' . esc_attr( $rock_stars_font_family ) . '; }';
		}

		wp_add_inline_style( 'quiz-frontend', $rock_stars_inline_css );
	}

	// Quiz Main Script
	wp_enqueue_script(
		'quiz-widget',
		get_template_directory_uri() . '/inc/quiz/assets/js/quiz-widget.js',
		array( 'jquery', 'lucide-icons', 'flatpickr-js' ),
		'1.0.5',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rock_stars_quiz_enqueue_frontend_assets' );

/**
 * Enqueue Admin Assets
 */
function rock_stars_quiz_enqueue_admin_assets( $hook ) {
	wp_enqueue_script( 'lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true );

	// Quiz Admin Styles
	wp_enqueue_style( 'quiz-admin', get_template_directory_uri() . '/inc/quiz/assets/css/quiz-admin.css', array(), '1.0.1' );

	wp_enqueue_script(
		'quiz-admin-js',
		get_template_directory_uri() . '/inc/quiz/assets/js/quiz-admin.js',
		array( 'jquery' ),
		'1.0.2',
		true
	);

	// Pass icon list to JS (icons defined in quiz-settings.php)
	if ( function_exists( 'rock_stars_get_quiz_icons' ) ) {
		wp_localize_script(
			'quiz-admin-js',
			'quizIconData',
			array(
				'icons' => rock_stars_get_quiz_icons(),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'rock_stars_quiz_enqueue_admin_assets' );
