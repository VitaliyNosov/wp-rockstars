<?php
/**
 * Landing page specific functions
 *
 * @package Rock_Star
 */

/**
 * Add body class for landing pages.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rock_stars_add_landing_page_body_class( $classes ) {
	$queried_object = get_queried_object();

	if ( $queried_object instanceof WP_Post ) {
		$template = get_post_meta( $queried_object->ID, '_wp_page_template', true );

		if ( $template ) {
			$template_lower = strtolower( $template );

			if ( false !== strpos( $template_lower, 'about' ) ) {
				$classes[] = 'landing-page-id';
			}

			if ( false !== strpos( $template_lower, 'landings-template.php' ) ) {
				$classes[] = 'landing-page-id';
			}
		}
	}

	return $classes;
}
add_filter( 'body_class', 'rock_stars_add_landing_page_body_class' );

/**
 * Enqueue landing page assets.
 */
function rock_stars_enqueue_landing_assets() {
	if ( ! is_page() ) {
		return;
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$template = get_post_meta( $post_id, '_wp_page_template', true );
	$is_landing = false;

	if ( $template ) {
		$template_lower = strtolower( $template );
		if ( false !== strpos( $template_lower, 'about' ) || false !== strpos( $template_lower, 'landings-template.php' ) ) {
			$is_landing = true;
		}
	}

	if ( $is_landing ) {
		wp_enqueue_style(
			'rock-stars-landings-mod',
			get_template_directory_uri() . '/common/css/landings-mod.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'rock-stars-tailwindcss',
			get_template_directory_uri() . '/common/landing-template/tailwindcss.js',
			array(),
			'3.4.1',
			true
		);

		wp_enqueue_script(
			'rock-stars-preline-js',
			get_template_directory_uri() . '/common/landing-template/preline.js',
			array(),
			'2.0.3',
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rock_stars_enqueue_landing_assets' );
