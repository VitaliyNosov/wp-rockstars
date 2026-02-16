<?php
/**
 * Ticket System Functions
 *
 * @package Rock_Star
 */

/**
 * Register Custom Post Type 'Ticket'
 */
function rock_stars_register_ticket_post_type() {
	$args = array(
		'labels'              => array(
			'name'               => 'Tickets',
			'singular_name'      => 'Ticket',
			'menu_name'          => 'Tickets',
			'add_new'            => 'Add New Ticket',
			'add_new_item'       => 'Add New Ticket',
			'edit_item'          => 'Edit Ticket',
			'new_item'           => 'New Ticket',
			'view_item'          => 'View Ticket',
			'search_items'       => 'Search Tickets',
			'not_found'          => 'No tickets found',
			'not_found_in_trash' => 'No tickets found in trash',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-tickets-alt',
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'custom-fields' ),
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_in_rest'        => false,
		'capabilities'        => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap'        => true,
	);
	register_post_type( 'ticket', $args );
}
add_action( 'init', 'rock_stars_register_ticket_post_type' );

/**
 * Helper to get ticket meta with backward compatibility.
 *
 * @param int    $post_id Post ID.
 * @param string $key Meta key without prefix.
 * @param bool   $single Whether to return a single value.
 * @return mixed
 */
function rock_stars_get_ticket_meta( $post_id, $key, $single = true ) {
	$new_key = '_rock_stars_' . $key;
	$old_key = '_wp_custom_' . $key;

	$val = get_post_meta( $post_id, $new_key, $single );
	if ( ( $single && '' === $val ) || ( ! $single && empty( $val ) ) ) {
		$val = get_post_meta( $post_id, $old_key, $single );
	}
	return $val;
}

/**
 * Add custom columns to admin list
 */
function rock_stars_ticket_columns( $columns ) {
	$new_columns = array(
		'cb'              => $columns['cb'],
		'title'           => 'Ticket',
		'sender_name'     => 'Sender Name',
		'sender_email'    => 'Email',
		'message_preview' => 'Message Preview',
		'date'            => 'Date Submitted',
	);
	return $new_columns;
}
add_filter( 'manage_ticket_posts_columns', 'rock_stars_ticket_columns' );

/**
 * Fill custom columns with data
 */
function rock_stars_ticket_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sender_name':
			echo esc_html( rock_stars_get_ticket_meta( $post_id, 'sender_name', true ) );
			break;
		case 'sender_email':
			echo esc_html( rock_stars_get_ticket_meta( $post_id, 'sender_email', true ) );
			break;
		case 'message_preview':
			$message = rock_stars_get_ticket_meta( $post_id, 'message', true );
			echo esc_html( wp_trim_words( $message, 10, '...' ) );
			break;
	}
}
add_action( 'manage_ticket_posts_custom_column', 'rock_stars_ticket_column_content', 10, 2 );

/**
 * Add meta box to show ticket details in admin
 */
function rock_stars_ticket_meta_box() {
	add_meta_box(
		'rock-stars-ticket-details',
		'Ticket Details',
		'rock_stars_ticket_meta_box_callback',
		'ticket',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'rock_stars_ticket_meta_box' );

/**
 * Render meta box for ticket details.
 *
 * @param WP_Post $post Post object.
 */
function rock_stars_ticket_meta_box_callback( $post ) {
	$sender_name     = rock_stars_get_ticket_meta( $post->ID, 'sender_name', true );
	$sender_email    = rock_stars_get_ticket_meta( $post->ID, 'sender_email', true );
	$message         = rock_stars_get_ticket_meta( $post->ID, 'message', true );
	$submission_time = rock_stars_get_ticket_meta( $post->ID, 'submission_time', true );

	echo '<div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px; border-radius: 8px; margin: -6px -12px;">';
	echo '<h4 style="color: #64b5f6; border-bottom: 2px solid #64b5f6; padding-bottom: 8px; margin-bottom: 15px; margin-top: 0;">📧 Sender Information</h4>';
	echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Name:</strong> ' . esc_html( $sender_name ) . '</p>';
	echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Email:</strong> ' . esc_html( $sender_email ) . '</p>';
	echo '<p style="margin: 8px 0; line-height: 1.6;"><strong style="color: #81c784;">Submitted:</strong> ' . esc_html( $submission_time ) . '</p>';
	echo '<h4 style="color: #64b5f6; border-bottom: 2px solid #64b5f6; padding-bottom: 8px; margin-bottom: 15px;">💬 Message</h4>';
	echo '<div class="message-content" style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #64b5f6; margin-top: 10px; line-height: 1.7; white-space: pre-wrap;">' . esc_html( $message ) . '</div>';
	echo '</div>';
}

/**
 * AJAX handler for ticket form submission.
 */
function rock_stars_handle_ticket_submission() {
	ob_start();

	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'rock_stars_ticket_nonce' ) ) {
		ob_end_clean();
		wp_send_json_error( 'Security check failed' );
	}

	if ( ! isset( $_POST['name'], $_POST['email'], $_POST['message'] ) ) {
		ob_end_clean();
		wp_send_json_error( 'Missing required fields' );
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ) );

	$utm_source   = isset( $_POST['utm_source'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_source'] ) ) : '';
	$utm_medium   = isset( $_POST['utm_medium'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_medium'] ) ) : '';
	$utm_campaign = isset( $_POST['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_campaign'] ) ) : '';

	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		ob_end_clean();
		wp_send_json_error( 'All fields are required' );
	}

	if ( ! is_email( $email ) ) {
		ob_end_clean();
		wp_send_json_error( 'Invalid email address' );
	}

	$post_data = array(
		'post_title'   => 'Ticket from ' . $name,
		'post_content' => $message,
		'post_status'  => 'publish',
		'post_type'    => 'ticket',
		'post_author'  => 1,
	);

	$post_id = wp_insert_post( $post_data );

	if ( is_wp_error( $post_id ) ) {
		ob_end_clean();
		wp_send_json_error( 'Failed to create ticket' );
	}

	update_post_meta( $post_id, '_rock_stars_sender_name', $name );
	update_post_meta( $post_id, '_rock_stars_sender_email', $email );
	update_post_meta( $post_id, '_rock_stars_message', $message );

	if ( $utm_source ) {
		update_post_meta( $post_id, '_rock_stars_utm_source', $utm_source );
	}
	if ( $utm_medium ) {
		update_post_meta( $post_id, '_rock_stars_utm_medium', $utm_medium );
	}
	if ( $utm_campaign ) {
		update_post_meta( $post_id, '_rock_stars_utm_campaign', $utm_campaign );
	}

	update_post_meta( $post_id, '_rock_stars_submission_time', current_time( 'mysql' ) );

	// phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints_$_SERVER__REMOTE_ADDR__
	$user_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	update_post_meta( $post_id, '_rock_stars_ip_address', $user_ip );

	if ( $user_ip === '127.0.0.1' || $user_ip === '::1' ) {
		$user_ip = '8.8.8.8';
	}

	$api_url  = 'http://ip-api.com/json/' . $user_ip;
	$response = wp_remote_get( $api_url, array( 'timeout' => 2 ) );

	if ( ! is_wp_error( $response ) ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( $data && 'success' === $data['status'] ) {
			update_post_meta( $post_id, '_rock_stars_country', $data['country'] );
			update_post_meta( $post_id, '_rock_stars_country_code', $data['countryCode'] );
			update_post_meta( $post_id, '_rock_stars_city', $data['city'] );
		}
	}

	$to      = $email;
	$subject = 'We received your request | ' . get_bloginfo( 'name' );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	$template_path = get_template_directory() . '/inc/email-templates/contact-reply.php';
	if ( file_exists( $template_path ) ) {
		ob_start();
		include $template_path;
		$message_html = ob_get_clean();

		$embed_logo_callback = function( $phpmailer ) {
			$logo_path = get_template_directory() . '/images/logo.png';
			if ( file_exists( $logo_path ) ) {
				$phpmailer->AddEmbeddedImage( $logo_path, 'company-logo', 'logo.png' );
			}
		};

		add_action( 'phpmailer_init', $embed_logo_callback );
		wp_mail( $to, $subject, $message_html, $headers );
		remove_action( 'phpmailer_init', $embed_logo_callback );
	}

	ob_end_clean();
	wp_send_json_success( array( 'message' => 'Ticket submitted successfully' ) );
}
add_action( 'wp_ajax_rock_stars_submit_ticket', 'rock_stars_handle_ticket_submission' );
add_action( 'wp_ajax_nopriv_rock_stars_submit_ticket', 'rock_stars_handle_ticket_submission' );

/**
 * Enqueue scripts and styles for tickets.
 */
function rock_stars_enqueue_ticket_scripts() {
	if ( ! is_admin() ) {
		wp_enqueue_script( 'rock-stars-ticket-handler', get_template_directory_uri() . '/common/js/ticket-handler.js', array( 'jquery' ), '1.0', true );

		wp_localize_script(
			'rock-stars-ticket-handler',
			'rock_stars_ticket_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'rock_stars_ticket_nonce' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rock_stars_enqueue_ticket_scripts' );

/**
 * Add export button to the ticket list table.
 */
function rock_stars_add_export_button( $which ) {
	global $typenow;
	if ( 'ticket' === $typenow && 'top' === $which ) {
		$export_url = admin_url( 'admin-post.php?action=rock_stars_export_tickets' );
		$nonce      = wp_create_nonce( 'rock_stars_export_tickets_nonce' );
		$export_url = add_query_arg( 'nonce', $nonce, $export_url );

		if ( isset( $_GET['m'] ) ) {
			$export_url = add_query_arg( 'm', sanitize_text_field( wp_unslash( $_GET['m'] ) ), $export_url );
		}
		if ( isset( $_GET['s'] ) ) {
			$export_url = add_query_arg( 's', sanitize_text_field( wp_unslash( $_GET['s'] ) ), $export_url );
		}
		?>
		<div class="alignleft actions">
			<a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary" style="margin-bottom: 5px;">Export CSV</a>
		</div>
		<?php
	}
}
add_action( 'manage_posts_extra_tablenav', 'rock_stars_add_export_button' );

/**
 * Handle ticket export to CSV.
 */
function rock_stars_handle_export_tickets() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized user', 'rock-stars' ) );
	}

	$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'rock_stars_export_tickets_nonce' ) ) {
		wp_die( esc_html__( 'Security check failed', 'rock-stars' ) );
	}

	$args = array(
		'post_type'      => 'ticket',
		'posts_per_page' => -1,
		'post_status'    => 'any',
	);

	if ( isset( $_GET['m'] ) ) {
		$m = sanitize_text_field( wp_unslash( $_GET['m'] ) );
		if ( 6 === strlen( $m ) ) {
			$year  = substr( $m, 0, 4 );
			$month = substr( $m, 4, 2 );
			$args['date_query'] = array(
				array(
					'year'  => $year,
					'month' => $month,
				),
			);
		}
	}

	if ( isset( $_GET['s'] ) ) {
		$args['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
	}

	$query = new WP_Query( $args );

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=tickets-export-' . gmdate( 'Y-m-d' ) . '.csv' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	$output = fopen( 'php://output', 'w' ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fopen

	if ( $output ) {
		fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
		fputcsv( $output, array( 'ID', 'Date', 'Sender Name', 'Sender Email', 'Message', 'UTM Source', 'UTM Medium', 'UTM Campaign' ) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				fputcsv(
					$output,
					array(
						$post_id,
						get_the_date( 'Y-m-d H:i:s' ),
						rock_stars_get_ticket_meta( $post_id, 'sender_name', true ),
						rock_stars_get_ticket_meta( $post_id, 'sender_email', true ),
						rock_stars_get_ticket_meta( $post_id, 'message', true ),
						rock_stars_get_ticket_meta( $post_id, 'utm_source', true ),
						rock_stars_get_ticket_meta( $post_id, 'utm_medium', true ),
						rock_stars_get_ticket_meta( $post_id, 'utm_campaign', true ),
					)
				);
			}
			wp_reset_postdata();
		}
		fclose( $output ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fclose
	}
	exit;
}
add_action( 'admin_post_rock_stars_export_tickets', 'rock_stars_handle_export_tickets' );

/**
 * Register Ticket Statistics Page
 */
function rock_stars_register_ticket_stats_page() {
	add_submenu_page(
		'edit.php?post_type=ticket',
		'Ticket Statistics',
		'Statistics',
		'manage_options',
		'ticket-stats',
		'rock_stars_ticket_stats_page_callback'
	);
}
add_action( 'admin_menu', 'rock_stars_register_ticket_stats_page' );

/**
 * Render Ticket Statistics Page
 */
function rock_stars_ticket_stats_page_callback() {
	global $wpdb;

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['generate_demo_data'] ) ) {
		$demo_countries = array(
			array( 'United States', 'US', 'New York' ),
			array( 'Germany', 'DE', 'Berlin' ),
			array( 'France', 'FR', 'Paris' ),
			array( 'United Kingdom', 'GB', 'London' ),
			array( 'Ukraine', 'UA', 'Kyiv' ),
			array( 'Canada', 'CA', 'Toronto' ),
			array( 'Australia', 'AU', 'Sydney' ),
			array( 'Brazil', 'BR', 'Rio de Janeiro' ),
		);
		$posts_to_update = get_posts( array( 'post_type' => 'ticket', 'posts_per_page' => -1, 'post_status' => 'any' ) );
		foreach ( $posts_to_update as $p ) {
			$rand = $demo_countries[ array_rand( $demo_countries ) ];
			update_post_meta( $p->ID, '_rock_stars_country', $rand[0] );
			update_post_meta( $p->ID, '_rock_stars_country_code', $rand[1] );
			update_post_meta( $p->ID, '_rock_stars_city', $rand[2] );
			update_post_meta( $p->ID, '_rock_stars_ip_address', '8.8.8.8' );
		}
	} elseif ( isset( $_GET['clear_demo_data'] ) ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN (%s, %s, %s)", '_rock_stars_country', '_rock_stars_country_code', '_rock_stars_city' ) );
	}

	$labels_7_days = array();
	$data_7_days   = array();
	for ( $i = 6; $i >= 0; $i-- ) {
		$date            = gmdate( 'Y-m-d', strtotime( "-$i days" ) );
		$labels_7_days[] = gmdate( 'd M', strtotime( $date ) );
		$count           = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ticket' AND post_status = 'publish' AND DATE(post_date) = %s", $date ) );
		$data_7_days[]   = (int) $count;
	}

	$m_labels      = array();
	$m_data        = array();
	$days_in_month = gmdate( 't' );
	$current_month = gmdate( 'Y-m' );
	for ( $d = 1; $d <= $days_in_month; $d++ ) {
		$day        = str_pad( $d, 2, '0', STR_PAD_LEFT );
		$date_str   = $current_month . '-' . $day;
		$m_labels[] = $d;
		$count      = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ticket' AND post_status = 'publish' AND DATE(post_date) = %s", $date_str ) );
		$m_data[]   = (int) $count;
	}

	$y_data       = array();
	$current_year = gmdate( 'Y' );
	for ( $i = 1; $i <= 12; $i++ ) {
		$month = str_pad( $i, 2, '0', STR_PAD_LEFT );
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ticket' AND post_status = 'publish' AND post_date LIKE %s", $current_year . '-' . $month . '%' ) );
		$y_data[] = (int) $count;
	}

	$geo_results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value as country, COUNT(*) as count FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != 'Unknown' GROUP BY meta_value ORDER BY count DESC", '_rock_stars_country' ) );
	$geo_data    = array( array( 'Country', 'Tickets' ) );
	foreach ( $geo_results as $row ) {
		$geo_data[] = array( $row->country, (int) $row->count );
	}
	?>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<div class="wrap" style="max-width: 1200px; margin-top: 20px;">
		<h1 style="margin-bottom: 20px;">Ticket System Dashboard</h1>
		
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
			<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #4A6CF7;">
				<h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase;">Total Tickets</h3>
				<?php $total_tickets = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ticket' AND post_status = 'publish'" ); ?>
				<p style="margin: 10px 0 0; font-size: 32px; font-weight: 700; color: #1e293b;"><?php echo (int) $total_tickets; ?></p>
			</div>
			<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
				<h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase;">Tickets Today</h3>
				<?php $today_tickets = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ticket' AND post_status = 'publish' AND DATE(post_date) = %s", gmdate( 'Y-m-d' ) ) ); ?>
				<p style="margin: 10px 0 0; font-size: 32px; font-weight: 700; color: #1e293b;"><?php echo (int) $today_tickets; ?></p>
			</div>
			<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
				<h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase;">Top Market</h3>
				<?php $top_country = $wpdb->get_row( $wpdb->prepare( "SELECT meta_value as country, COUNT(*) as count FROM {$wpdb->postmeta} WHERE meta_key = %s GROUP BY meta_value ORDER BY count DESC LIMIT 1", '_rock_stars_country' ) ); ?>
				<p style="margin: 10px 0 0; font-size: 32px; font-weight: 700; color: #1e293b;"><?php echo $top_country ? esc_html( $top_country->country ) : 'N/A'; ?></p>
			</div>
		</div>

		<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
			<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
					<h2 style="margin: 0; font-size: 18px;">Submission Trends</h2>
					<select id="stats-period" style="padding: 5px 10px; border-radius: 6px;">
						<option value="7days">Last 7 Days</option>
						<option value="month" selected>This Month</option>
						<option value="year">Full Year</option>
					</select>
				</div>
				<div style="height: 400px; position: relative;"><canvas id="ticketChart"></canvas></div>
			</div>
			<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
					<h2 style="margin: 0; font-size: 18px;">Global Distribution</h2>
					<div style="display: flex; gap: 10px;">
						<?php if ( count( $geo_data ) > 1 ) : ?>
							<a href="<?php echo esc_url( add_query_arg( 'clear_demo_data', '1' ) ); ?>" class="button button-link-delete" style="color: #ef4444;" onclick="return confirm('Remove all map data?');">Reset Map Data</a>
						<?php else : ?>
							<a href="<?php echo esc_url( add_query_arg( 'generate_demo_data', '1' ) ); ?>" class="button button-secondary">Generate Demo Data</a>
						<?php endif; ?>
					</div>
				</div>
				<div id="regions_div" style="width: 100%; height: 400px; background: #f8fafc; border-radius: 8px;"></div>
			</div>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const ctx = document.getElementById('ticketChart').getContext('2d');
			const rawData = {
				'7days': { labels: <?php echo wp_json_encode( $labels_7_days ); ?>, data: <?php echo wp_json_encode( $data_7_days ); ?>, label: 'Tickets (Last 7 Days)' },
				'month': { labels: <?php echo wp_json_encode( $m_labels ); ?>, data: <?php echo wp_json_encode( $m_data ); ?>, label: 'Tickets (This Month)' },
				'year': { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], data: <?php echo wp_json_encode( $y_data ); ?>, label: 'Tickets (This Year)' }
			};
			let chartInstance = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: rawData['month'].labels,
					datasets: [{
						label: rawData['month'].label,
						data: rawData['month'].data,
						backgroundColor: 'rgba(74, 108, 247, 0.6)',
						borderColor: 'rgba(74, 108, 247, 1)',
						borderWidth: 1,
						borderRadius: 4
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
					plugins: { legend: { position: 'top' } }
				}
			});
			document.getElementById('stats-period').addEventListener('change', function(e) {
				const val = e.target.value;
				const dataset = rawData[val];
				chartInstance.data.labels = dataset.labels;
				chartInstance.data.datasets[0].data = dataset.data;
				chartInstance.data.datasets[0].label = dataset.label;
				chartInstance.update();
			});
			google.charts.load('current', { 'packages':['geochart'] });
			google.charts.setOnLoadCallback(drawRegionsMap);
			function drawRegionsMap() {
				var container = document.getElementById('regions_div');
				if(!container) return;
				var data = google.visualization.arrayToDataTable(<?php echo wp_json_encode( $geo_data ); ?>);
				var options = { colorAxis: {colors: ['#e0f7fa', '#4A6CF7']}, backgroundColor: '#ffffff', datalessRegionColor: '#f1f5f9', defaultColor: '#f1f5f9' };
				var chart = new google.visualization.GeoChart(container);
				chart.draw(data, options);
			}
		});
		</script>
	</div>
	<?php
}
