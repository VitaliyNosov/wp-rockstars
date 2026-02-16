<?php
/**
 * Modal portfolio
 *
 * @package Rock_Star
 */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'portfolio-modal', get_template_directory_uri() . '/common/js/portfolio-modal.js', array(), null, true );

	// Localize for VIP compliance (passing the same nonce for proxy).
	wp_localize_script( 'portfolio-modal', 'rock_stars_portfolio_proxy', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'rock_stars_proxy_nonce' ),
	) );
} );

add_action( 'wp_ajax_nopriv_rock_stars_proxy_site', 'rock_stars_proxy_site' );
add_action( 'wp_ajax_rock_stars_proxy_site', 'rock_stars_proxy_site' );

/**
 * Proxy function to fetch site content.
 */
function rock_stars_proxy_site() {
	ob_start(); // Start buffering to catch any stray output.

	// Verify nonce for security.
	$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'rock_stars_proxy_nonce' ) ) {
		ob_end_clean();
		wp_send_json_error( 'Security check failed', 403 );
	}

	if ( empty( $_GET['url'] ) ) {
		ob_end_clean();
		wp_send_json_error( 'Missing URL', 400 );
	}

	$url        = esc_url_raw( wp_unslash( $_GET['url'] ) );
	$parsed_url = wp_parse_url( $url );

	if ( ! isset( $parsed_url['host'] ) ) {
		ob_end_clean();
		wp_send_json_error( 'Invalid URL', 400 );
	}

	// Normalizing host.
	$host = strtolower( $parsed_url['host'] );
	$host = preg_replace( '/^www\./', '', $host );

	// Allowed domains.
	$allowed_hosts = array( 'seeintl.org', 'waapple.org', 'snopud.com', 'cchpca.org' );

	if ( ! in_array( $host, $allowed_hosts, true ) ) {
		ob_end_clean();
		wp_send_json_error( 'URL not allowed', 403 );
	}

	// VIP: Use a safe timeout (max 2-3 seconds usually).
	$response = wp_remote_get( $url, array( 'timeout' => 2 ) );

	if ( is_wp_error( $response ) ) {
		ob_end_clean();
		wp_send_json_error( 'Request failed' );
	}

	$body         = wp_remote_retrieve_body( $response );
	$content_type = wp_remote_retrieve_header( $response, 'content-type' );

	// Clean buffer before non-JSON response.
	if ( ob_get_length() ) {
		ob_clean();
	}

	header( 'Content-Type: ' . $content_type );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	wp_die();
}