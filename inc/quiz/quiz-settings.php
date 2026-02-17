<?php
/**
 * Quiz Settings (Carbon Fields Builder)
 *
 * @package Rockstars
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Get Popular Lucide Icons for Digital Agency
 *
 * @return array
 */
function rock_stars_get_quiz_icons() {
	return array(
		''               => __( 'No Icon', 'rock-stars' ),
		'layout'         => __( 'Layout (Web Design)', 'rock-stars' ),
		'code'           => __( 'Code (Development)', 'rock-stars' ),
		'search'         => __( 'Search (SEO)', 'rock-stars' ),
		'megaphone'      => __( 'Megaphone (Marketing)', 'rock-stars' ),
		'zap'            => __( 'Zap (Quick/Fast)', 'rock-stars' ),
		'smartphone'     => __( 'Smartphone (Mobile Apps)', 'rock-stars' ),
		'layers'         => __( 'Layers (UI/UX)', 'rock-stars' ),
		'database'       => __( 'Database (Backend)', 'rock-stars' ),
		'bar-chart'      => __( 'Bar Chart (Analytics)', 'rock-stars' ),
		'shopping-cart'  => __( 'Cart (E-commerce)', 'rock-stars' ),
		'palette'        => __( 'Palette (Graphic Design)', 'rock-stars' ),
		'globe'          => __( 'Globe (SEO/Multi-lang)', 'rock-stars' ),
		'users'          => __( 'Users (Team/Community)', 'rock-stars' ),
		'briefcase'      => __( 'Briefcase (Business)', 'rock-stars' ),
		'target'         => __( 'Target (Ads/Strategy)', 'rock-stars' ),
		'video'          => __( 'Video (Content)', 'rock-stars' ),
		'mail'           => __( 'Mail (Email Marketing)', 'rock-stars' ),
		'message-square' => __( 'Message (Support/Feedback)', 'rock-stars' ),
		'shield'         => __( 'Shield (Security)', 'rock-stars' ),
		'cpu'            => __( 'CPU (Tech/Systems)', 'rock-stars' ),
		'link'           => __( 'Link (Backlinks)', 'rock-stars' ),
		'trending-up'    => __( 'Trending (Growth)', 'rock-stars' ),
		'pen-tool'       => __( 'Pen Tool (Branding)', 'rock-stars' ),
		'activity'       => __( 'Activity (Performance)', 'rock-stars' ),
		'cloud'          => __( 'Cloud (Hosting)', 'rock-stars' ),
		'lock'           => __( 'Lock (Privacy)', 'rock-stars' ),
		'anchor'         => __( 'Anchor (Stability)', 'rock-stars' ),
		'star'           => __( 'Star (Premium)', 'rock-stars' ),
		'check-circle'   => __( 'Check (Done)', 'rock-stars' ),
		'clock'          => __( 'Clock (Timing)', 'rock-stars' ),
		'coffee'         => __( 'Coffee (Simple/Relax)', 'rock-stars' ),
		'settings'       => __( 'Settings (Logic)', 'rock-stars' ),
		'mouse-pointer'  => __( 'Click (PPC)', 'rock-stars' ),
		'image'          => __( 'Image (Photos)', 'rock-stars' ),
		'music'          => __( 'Music (Audio)', 'rock-stars' ),
		'paperclip'      => __( 'Attachment', 'rock-stars' ),
		'map-pin'        => __( 'Location', 'rock-stars' ),
		'smile'          => __( 'Smile (Happy Client)', 'rock-stars' ),
	);
}

/**
 * Register Quiz settings with Carbon Fields.
 */
function rock_stars_quiz_register_settings() {
	Container::make( 'theme_options', __( 'Quiz Builder', 'rock-stars' ) )
		->set_page_parent( 'edit.php?post_type=quiz_submission' )
		->add_tab(
			__( 'Settings', 'rock-stars' ),
			array(
				Field::make( 'color', 'rock_stars_quiz_accent_color', __( 'Accent Color', 'rock-stars' ) )
					->set_help_text( __( 'Main color for buttons, active steps, and borders.', 'rock-stars' ) )
					->set_default_value( '#4A6CF7' )
					->set_width( 50 ),

				Field::make( 'separator', 'rock_stars_quiz_typography_sep', __( 'Typography & Text', 'rock-stars' ) ),

				Field::make( 'select', 'rock_stars_quiz_font_family', __( 'Font Family', 'rock-stars' ) )
					->set_options(
						array(
							''                        => __( 'Theme Default (Inherit)', 'rock-stars' ),
							'custom'                  => __( 'Custom Google Font', 'rock-stars' ),
							"'Inter', sans-serif"     => 'Inter',
							"'Roboto', sans-serif"    => 'Roboto',
							"'Open Sans', sans-serif" => 'Open Sans',
							"'Montserrat', sans-serif" => 'Montserrat',
							"'Merriweather', serif"  => 'Merriweather (Serif)',
							"-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" => 'System Sans',
						)
					)
					->set_width( 33 ),

				Field::make( 'text', 'rock_stars_quiz_custom_font_name', __( 'Google Font Name', 'rock-stars' ) )
					->set_help_text( __( 'Enter the font family name as specified in Google Fonts (e.g. "Montserrat" or "\'Open Sans\', sans-serif").', 'rock-stars' ) )
					->set_width( 33 )
					->set_conditional_logic(
						array(
							array(
								'field'   => 'rock_stars_quiz_font_family',
								'value'   => 'custom',
								'compare' => '=',
							),
						)
					),

				Field::make( 'text', 'rock_stars_quiz_custom_font_url', __( 'Google Font CSS URL', 'rock-stars' ) )
					->set_help_text( __( 'Paste the "href" URL from the Google Fonts embed code (starts with https://fonts.googleapis.com/css2...).', 'rock-stars' ) )
					->set_width( 33 )
					->set_conditional_logic(
						array(
							array(
								'field'   => 'rock_stars_quiz_font_family',
								'value'   => 'custom',
								'compare' => '=',
							),
						)
					),

				Field::make( 'text', 'rock_stars_quiz_btn_prev', __( 'Back Button Text', 'rock-stars' ) )
					->set_default_value( __( 'Back', 'rock-stars' ) )
					->set_width( 33 ),

				Field::make( 'text', 'rock_stars_quiz_btn_next', __( 'Next Button Text', 'rock-stars' ) )
					->set_default_value( __( 'Next', 'rock-stars' ) )
					->set_width( 33 ),

				Field::make( 'text', 'rock_stars_quiz_btn_submit', __( 'Submit Button Text', 'rock-stars' ) )
					->set_default_value( __( 'Submit', 'rock-stars' ) )
					->set_width( 33 ),

				Field::make( 'html', 'rock_stars_quiz_trigger_info' )
					->set_html(
						'<div style="background: #fff; border-left: 4px solid #4A6CF7; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #1f2937; margin-bottom: 12px; font-size: 18px;">' . esc_html__( '🚀 Quiz Launch Triggers', 'rock-stars' ) . '</h3>
                        <p style="font-size: 14px; margin-bottom: 16px; color: #4b5563; line-height: 1.5;">' . sprintf( esc_html__( 'To launch the quiz modal from %1$sanywhere%2$s on your site (WordPress Menu, Elementor Buttons, Text Links, etc.), simply use one of the methods below:', 'rock-stars' ), '<strong>', '</strong>' ) . '</p>
                        
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong style="display: block; color: #0f172a; margin-bottom: 4px; font-size: 15px;">' . esc_html__( '1. CSS Class (Best for Menus & Buttons)', 'rock-stars' ) . '</strong>
                                <span style="font-size: 13px; color: #64748b;">' . esc_html__( 'Add this class to "CSS Classes" field in Menu Items or Advanced tab in Page Builders.', 'rock-stars' ) . '</span>
                            </div>
                            <code style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; color: #d63384; font-weight: bold; cursor: pointer; font-size: 14px; transition: all 0.2s;" onclick="navigator.clipboard.writeText(\'js-open-quiz\'); this.style.borderColor=\'#10b981\'; this.innerText=\'Copied!\'; setTimeout(()=>{this.innerText=\'js-open-quiz\';this.style.borderColor=\'#cbd5e1\'}, 1500);" title="' . esc_attr__( 'Click to copy', 'rock-stars' ) . '">js-open-quiz</code>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong style="display: block; color: #0f172a; margin-bottom: 4px; font-size: 15px;">' . esc_html__( '2. URL Parameter (for Links)', 'rock-stars' ) . '</strong>
                                <span style="font-size: 13px; color: #64748b;">' . esc_html__( 'Add this to the end of any URL to auto-open the quiz.', 'rock-stars' ) . '</span>
                            </div>
                            <code style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; color: #2563eb; font-size: 14px;">?open-quiz=true</code>
                        </div>
                    </div>'
					),
			)
		)
		->add_tab(
			__( 'Telegram', 'rock-stars' ),
			array(
				Field::make( 'checkbox', 'rock_stars_quiz_telegram_active', __( 'Enable Telegram Notifications', 'rock-stars' ) )
					->set_option_value( 'yes' )
					->set_default_value( false ),

				Field::make( 'text', 'rock_stars_quiz_telegram_token', __( 'Bot Token', 'rock-stars' ) )
					->set_help_text( __( 'Get this from @BotFather', 'rock-stars' ) )
					->set_conditional_logic(
						array(
							array(
								'field'   => 'rock_stars_quiz_telegram_active',
								'value'   => true,
								'compare' => '=',
							),
						)
					),

				Field::make( 'text', 'rock_stars_quiz_telegram_chat_id', __( 'Chat ID', 'rock-stars' ) )
					->set_help_text( __( 'User ID or Channel ID (e.g. -100xxxxxxxxxx)', 'rock-stars' ) )
					->set_conditional_logic(
						array(
							array(
								'field'   => 'rock_stars_quiz_telegram_active',
								'value'   => true,
								'compare' => '=',
							),
						)
					),

				Field::make( 'text', 'rock_stars_quiz_telegram_subject', __( 'Message Header', 'rock-stars' ) )
					->set_default_value( __( '🔥 New Quiz Submission', 'rock-stars' ) )
					->set_conditional_logic(
						array(
							array(
								'field'   => 'rock_stars_quiz_telegram_active',
								'value'   => true,
								'compare' => '=',
							),
						)
					),
			)
		)
		->add_tab(
			__( 'Notifications', 'rock-stars' ),
			array(
				Field::make( 'text', 'rock_stars_quiz_notification_email', __( 'Notification Email', 'rock-stars' ) )
					->set_help_text( __( 'Enter the email address(es) to receive quiz results. Separate multiple emails with commas.', 'rock-stars' ) )
					->set_default_value( get_option( 'admin_email' ) ),
				Field::make( 'text', 'rock_stars_quiz_notification_subject', __( 'Email Subject', 'rock-stars' ) )
					->set_default_value( __( 'New Quiz Submission from {user_name}', 'rock-stars' ) ),
			)
		)
		->add_tab(
			__( 'Structure', 'rock-stars' ),
			array(
				Field::make( 'complex', 'rock_stars_quiz_structure', __( 'Quiz Steps', 'rock-stars' ) )
					->set_layout( 'tabbed-vertical' )
					->setup_labels(
						array(
							'plural_name'   => __( 'Steps', 'rock-stars' ),
							'singular_name' => __( 'Step', 'rock-stars' ),
						)
					)
					->add_fields(
						'step',
						array(
							Field::make( 'text', 'step_title', __( 'Step Title', 'rock-stars' ) )
								->set_required( true ),
							Field::make( 'textarea', 'step_description', __( 'Step Description', 'rock-stars' ) )
								->set_rows( 2 ),
							Field::make( 'complex', 'step_fields', __( 'Fields', 'rock-stars' ) )
								->set_layout( 'tabbed-vertical' )
								->setup_labels(
									array(
										'plural_name'   => __( 'Fields', 'rock-stars' ),
										'singular_name' => __( 'Field', 'rock-stars' ),
									)
								)
								->add_fields(
									'text',
									__( 'Text Input', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )
											->set_help_text( __( 'Unique identifier for this field (e.g. user_name). Only letters, numbers and underscores.', 'rock-stars' ) )
											->set_required( true ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
										Field::make( 'text', 'field_width', __( 'Width (1/2, 1/3, etc)', 'rock-stars' ) )
											->set_default_value( '100%' ),
									)
								)
								->add_fields(
									'email',
									__( 'Email Input', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'textarea',
									__( 'Text Area', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) ),
										Field::make( 'text', 'field_rows', __( 'Rows', 'rock-stars' ) )->set_default_value( 4 ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'radio',
									__( 'Radio Buttons', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_layout', __( 'Layout', 'rock-stars' ) )
											->set_options(
												array(
													'list'  => __( 'List (Standard)', 'rock-stars' ),
													'tiles' => __( 'Tiles (Icons/Images)', 'rock-stars' ),
												)
											)
											->set_default_value( 'list' )
											->set_width( 50 ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'complex', 'field_options', __( 'Options', 'rock-stars' ) )
											->set_layout( 'tabbed-horizontal' )
											->add_fields(
												array(
													Field::make( 'text', 'option_value', __( 'Value', 'rock-stars' ) )->set_required( true )->set_width( 50 ),
													Field::make( 'text', 'option_label', __( 'Label', 'rock-stars' ) )->set_required( true )->set_width( 50 ),
													Field::make( 'select', 'option_icon', __( 'Icon', 'rock-stars' ) )
														->set_options( 'rock_stars_get_quiz_icons' )
														->set_width( 50 ),
													Field::make( 'image', 'option_image', __( 'Image', 'rock-stars' ) )
														->set_value_type( 'url' )
														->set_width( 50 ),
												)
											)
											->set_header_template( '<%- option_label %>' )
											->set_required( true ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'checkbox',
									__( 'Checkbox Group', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_layout', __( 'Layout', 'rock-stars' ) )
											->set_options(
												array(
													'list'  => __( 'List (Standard)', 'rock-stars' ),
													'tiles' => __( 'Tiles (Icons/Images)', 'rock-stars' ),
												)
											)
											->set_default_value( 'list' )
											->set_width( 50 ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'complex', 'field_options', __( 'Options', 'rock-stars' ) )
											->set_layout( 'tabbed-horizontal' )
											->add_fields(
												array(
													Field::make( 'text', 'option_value', __( 'Value', 'rock-stars' ) )->set_required( true )->set_width( 50 ),
													Field::make( 'text', 'option_label', __( 'Label', 'rock-stars' ) )->set_required( true )->set_width( 50 ),
													Field::make( 'select', 'option_icon', __( 'Icon', 'rock-stars' ) )
														->set_options( 'rock_stars_get_quiz_icons' )
														->set_width( 50 ),
													Field::make( 'image', 'option_image', __( 'Image', 'rock-stars' ) )
														->set_value_type( 'url' )
														->set_width( 50 ),
												)
											)
											->set_header_template( '<%- option_label %>' ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'select',
									__( 'Select Dropdown', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) ),
										Field::make( 'complex', 'field_options', __( 'Options', 'rock-stars' ) )
											->set_layout( 'tabbed-horizontal' )
											->setup_labels(
												array(
													'plural_name'   => __( 'Options', 'rock-stars' ),
													'singular_name' => __( 'Option', 'rock-stars' ),
												)
											)
											->add_fields(
												array(
													Field::make( 'text', 'option_value', __( 'Value', 'rock-stars' ) )
														->set_help_text( __( 'Internal value (e.g. "kyiv")', 'rock-stars' ) )
														->set_required( true )
														->set_width( 50 ),
													Field::make( 'text', 'option_label', __( 'Label', 'rock-stars' ) )
														->set_help_text( __( 'Display text (e.g. "Киев")', 'rock-stars' ) )
														->set_required( true )
														->set_width( 50 ),
												)
											)
											->set_header_template( '<%- option_label %>' ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'range',
									__( 'Range Slider', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'select', 'field_purpose', __( 'Purpose', 'rock-stars' ) )
											->set_options(
												array(
													'none'  => __( 'Regular Field', 'rock-stars' ),
													'name'  => __( 'User Name (Identity)', 'rock-stars' ),
													'email' => __( 'User Email (Identity)', 'rock-stars' ),
												)
											)
											->set_default_value( 'none' )
											->set_width( 50 ),
										Field::make( 'text', 'field_min', __( 'Minimum Value', 'rock-stars' ) )
											->set_default_value( '0' )
											->set_width( 25 ),
										Field::make( 'text', 'field_max', __( 'Maximum Value', 'rock-stars' ) )
											->set_default_value( '100000' )
											->set_width( 25 ),
										Field::make( 'text', 'field_step', __( 'Step', 'rock-stars' ) )
											->set_default_value( '1000' )
											->set_help_text( __( 'Increment value', 'rock-stars' ) )
											->set_width( 25 ),
										Field::make( 'text', 'field_default', __( 'Default Value', 'rock-stars' ) )
											->set_default_value( '50000' )
											->set_width( 25 ),
										Field::make( 'text', 'field_prefix', __( 'Prefix (e.g. $)', 'rock-stars' ) )
											->set_help_text( __( 'Symbol before value', 'rock-stars' ) )
											->set_width( 25 ),
										Field::make( 'text', 'field_suffix', __( 'Suffix (e.g. USD)', 'rock-stars' ) )
											->set_help_text( __( 'Symbol after value', 'rock-stars' ) )
											->set_width( 25 ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'info',
									__( 'Info Text Only', 'rock-stars' ),
									array(
										Field::make( 'rich_text', 'field_content', __( 'Content', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'file',
									__( 'File Upload', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder Text', 'rock-stars' ) )->set_help_text( __( 'e.g. "Choose file..."', 'rock-stars' ) ),
										Field::make( 'text', 'field_file_types', __( 'Allowed Extensions', 'rock-stars' ) )
											->set_help_text( __( 'Comma separated, e.g. pdf, docx, png, jpg', 'rock-stars' ) )
											->set_default_value( 'pdf, docx, png, jpg, zip' ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'phone',
									__( 'Phone Number', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) )->set_default_value( '+7 (___) ___-__-__' ),
										Field::make( 'text', 'field_mask', __( 'Input Mask', 'rock-stars' ) )
											->set_default_value( '+7 (999) 999-99-99' )
											->set_help_text( __( 'Use 9 for digits. Example: +7 (999) 999-99-99', 'rock-stars' ) ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'switch',
									__( 'Switch / Toggle', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_on_label', __( 'ON Label', 'rock-stars' ) )->set_default_value( __( 'Yes', 'rock-stars' ) ),
										Field::make( 'text', 'field_off_label', __( 'OFF Label', 'rock-stars' ) )->set_default_value( __( 'No', 'rock-stars' ) ),
										Field::make( 'checkbox', 'field_default_state', __( 'Default state is ON?', 'rock-stars' ) ),
									)
								)
								->add_fields(
									'date',
									__( 'Date Picker', 'rock-stars' ),
									array(
										Field::make( 'text', 'field_label', __( 'Label', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_name', __( 'Field ID (Name)', 'rock-stars' ) )->set_required( true ),
										Field::make( 'text', 'field_placeholder', __( 'Placeholder', 'rock-stars' ) )->set_default_value( __( 'Select date', 'rock-stars' ) ),
										Field::make( 'checkbox', 'field_required', __( 'Required?', 'rock-stars' ) ),
									)
								)
						)
					)
					->set_header_template( __( 'Step', 'rock-stars' ) )
			)
		);
}
add_action( 'carbon_fields_register_fields', 'rock_stars_quiz_register_settings' );


