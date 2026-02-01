<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Get Popular Lucide Icons for Digital Agency
 */
function rockstars_get_quiz_icons() {
    return array(
        '' => 'No Icon',
        'layout' => 'Layout (Web Design)',
        'code' => 'Code (Development)',
        'search' => 'Search (SEO)',
        'megaphone' => 'Megaphone (Marketing)',
        'zap' => 'Zap (Quick/Fast)',
        'smartphone' => 'Smartphone (Mobile Apps)',
        'layers' => 'Layers (UI/UX)',
        'database' => 'Database (Backend)',
        'bar-chart' => 'Bar Chart (Analytics)',
        'shopping-cart' => 'Cart (E-commerce)',
        'palette' => 'Palette (Graphic Design)',
        'globe' => 'Globe (SEO/Multi-lang)',
        'users' => 'Users (Team/Community)',
        'briefcase' => 'Briefcase (Business)',
        'target' => 'Target (Ads/Strategy)',
        'video' => 'Video (Content)',
        'mail' => 'Mail (Email Marketing)',
        'message-square' => 'Message (Support/Feedback)',
        'shield' => 'Shield (Security)',
        'cpu' => 'CPU (Tech/Systems)',
        'link' => 'Link (Backlinks)',
        'trending-up' => 'Trending (Growth)',
        'pen-tool' => 'Pen Tool (Branding)',
        'activity' => 'Activity (Performance)',
        'cloud' => 'Cloud (Hosting)',
        'lock' => 'Lock (Privacy)',
        'anchor' => 'Anchor (Stability)',
        'star' => 'Star (Premium)',
        'check-circle' => 'Check (Done)',
        'clock' => 'Clock (Timing)',
        'coffee' => 'Coffee (Simple/Relax)',
        'settings' => 'Settings (Logic)',
        'mouse-pointer' => 'Click (PPC)',
        'image' => 'Image (Photos)',
        'music' => 'Music (Audio)',
        'paperclip' => 'Attachment',
        'map-pin' => 'Location',
        'smile' => 'Smile (Happy Client)',
    );
}

add_action('carbon_fields_register_fields', 'crb_register_quiz_settings');


/**
 * Quiz Settings (Carbon Fields)
 */

function crb_register_quiz_settings() {
    Container::make('theme_options', 'Quiz Builder')
        ->set_page_parent('edit.php?post_type=quiz_submission')
        ->add_tab('Settings', array(
            Field::make('color', 'quiz_accent_color', 'Accent Color')
                ->set_help_text('Main color for buttons, active steps, and borders.')
                ->set_default_value('#4A6CF7')
                ->set_width(50),
            
            Field::make('separator', 'quiz_typography_sep', 'Typography & Text'),

            Field::make('select', 'quiz_font_family', 'Font Family')
                ->set_options(array(
                    '' => 'Theme Default (Inherit)',
                    'custom' => 'Custom Google Font',
                    "'Inter', sans-serif" => 'Inter',
                    "'Roboto', sans-serif" => 'Roboto',
                    "'Open Sans', sans-serif" => 'Open Sans',
                    "'Montserrat', sans-serif" => 'Montserrat',
                    "'Merriweather', serif" => 'Merriweather (Serif)',
                    "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" => 'System Sans',
                ))
                ->set_width(33),

            Field::make('text', 'quiz_custom_font_name', 'Google Font Name')
                ->set_help_text('Enter the font family name as specified in Google Fonts (e.g. "Montserrat" or "\'Open Sans\', sans-serif").')
                ->set_width(33)
                ->set_conditional_logic(array(
                    array('field' => 'quiz_font_family', 'value' => 'custom', 'compare' => '='),
                )),

            Field::make('text', 'quiz_custom_font_url', 'Google Font CSS URL')
                ->set_help_text('Paste the "href" URL from the Google Fonts embed code (starts with https://fonts.googleapis.com/css2...).')
                ->set_width(33)
                ->set_conditional_logic(array(
                    array('field' => 'quiz_font_family', 'value' => 'custom', 'compare' => '='),
                )),

            Field::make('text', 'quiz_btn_prev', 'Back Button Text')
                ->set_default_value('Back')
                ->set_width(33),
            
            Field::make('text', 'quiz_btn_next', 'Next Button Text')
                ->set_default_value('Next')
                ->set_width(33),

            Field::make('text', 'quiz_btn_submit', 'Submit Button Text')
                ->set_default_value('Submit')
                ->set_width(33),
            
            Field::make('html', 'quiz_trigger_info')
                ->set_html('
                    <div style="background: #fff; border-left: 4px solid #4A6CF7; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #1f2937; margin-bottom: 12px; font-size: 18px;">🚀 Quiz Launch Triggers</h3>
                        <p style="font-size: 14px; margin-bottom: 16px; color: #4b5563; line-height: 1.5;">To launch the quiz modal from <strong>anywhere</strong> on your site (WordPress Menu, Elementor Buttons, Text Links, etc.), simply use one of the methods below:</p>
                        
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong style="display: block; color: #0f172a; margin-bottom: 4px; font-size: 15px;">1. CSS Class (Best for Menus & Buttons)</strong>
                                <span style="font-size: 13px; color: #64748b;">Add this class to "CSS Classes" field in Menu Items or Advanced tab in Page Builders.</span>
                            </div>
                            <code style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; color: #d63384; font-weight: bold; cursor: pointer; font-size: 14px; transition: all 0.2s;" onclick="navigator.clipboard.writeText(\'js-open-quiz\'); this.style.borderColor=\'#10b981\'; this.innerText=\'Copied!\'; setTimeout(()=>{this.innerText=\'js-open-quiz\';this.style.borderColor=\'#cbd5e1\'}, 1500);" title="Click to copy">js-open-quiz</code>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong style="display: block; color: #0f172a; margin-bottom: 4px; font-size: 15px;">2. URL Parameter (for Links)</strong>
                                <span style="font-size: 13px; color: #64748b;">Add this to the end of any URL to auto-open the quiz.</span>
                            </div>
                            <code style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; color: #2563eb; font-size: 14px;">?open-quiz=true</code>
                        </div>
                    </div>
                ')
        ))
        ->add_tab('Telegram', array(
            Field::make('checkbox', 'quiz_telegram_active', 'Enable Telegram Notifications')
                ->set_option_value('yes')
                ->set_default_value(false),
            
            Field::make('text', 'quiz_telegram_token', 'Bot Token')
                ->set_help_text('Get this from @BotFather')
                ->set_conditional_logic(array(
                    array('field' => 'quiz_telegram_active', 'value' => true, 'compare' => '='),
                )),
            
            Field::make('text', 'quiz_telegram_chat_id', 'Chat ID')
                ->set_help_text('User ID or Channel ID (e.g. -100xxxxxxxxxx)')
                ->set_conditional_logic(array(
                    array('field' => 'quiz_telegram_active', 'value' => true, 'compare' => '='),
                )),

            Field::make('text', 'quiz_telegram_subject', 'Message Header')
                ->set_default_value('🔥 New Quiz Submission')
                ->set_conditional_logic(array(
                    array('field' => 'quiz_telegram_active', 'value' => true, 'compare' => '='),
                )),
        ))
        ->add_tab('Notifications', array(
            Field::make('text', 'quiz_notification_email', 'Notification Email')
                ->set_help_text('Enter the email address(es) to receive quiz results. Separate multiple emails with commas.')
                ->set_default_value(get_option('admin_email')),
            Field::make('text', 'quiz_notification_subject', 'Email Subject')
                ->set_default_value('New Quiz Submission from {user_name}'),
        ))
        ->add_tab('Structure', array(
            Field::make('complex', 'quiz_structure', 'Quiz Steps')
                ->set_layout('tabbed-vertical')
                ->setup_labels(array(
                    'plural_name' => 'Steps',
                    'singular_name' => 'Step',
                ))
                ->add_fields('step', array(
                    Field::make('text', 'step_title', 'Step Title')
                        ->set_required(true),
                    Field::make('textarea', 'step_description', 'Step Description')
                        ->set_rows(2),
                    Field::make('complex', 'step_fields', 'Fields')
                        ->set_layout('tabbed-vertical')
                        ->setup_labels(array(
                            'plural_name' => 'Fields',
                            'singular_name' => 'Field',
                        ))
                        ->add_fields('text', 'Text Input', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')
                                ->set_help_text('Unique identifier for this field (e.g. user_name). Only letters, numbers and underscores.')
                                ->set_required(true),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('text', 'field_placeholder', 'Placeholder'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                            Field::make('text', 'field_width', 'Width (1/2, 1/3, etc)')
                                ->set_default_value('100%'),
                        ))
                        ->add_fields('email', 'Email Input', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('text', 'field_placeholder', 'Placeholder'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('textarea', 'Text Area', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('text', 'field_placeholder', 'Placeholder'),
                            Field::make('text', 'field_rows', 'Rows')->set_default_value(4),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('radio', 'Radio Buttons', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_layout', 'Layout')
                                ->set_options(array(
                                    'list' => 'List (Standard)',
                                    'tiles' => 'Tiles (Icons/Images)',
                                ))
                                ->set_default_value('list')
                                ->set_width(50),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('complex', 'field_options', 'Options')
                                ->set_layout('tabbed-horizontal')
                                ->add_fields(array(
                                    Field::make('text', 'option_value', 'Value')->set_required(true)->set_width(50),
                                    Field::make('text', 'option_label', 'Label')->set_required(true)->set_width(50),
                                    Field::make('select', 'option_icon', 'Icon')
                                        ->set_options('rockstars_get_quiz_icons')
                                        ->set_width(50),
                                    Field::make('image', 'option_image', 'Image')
                                        ->set_value_type('url')
                                        ->set_width(50),
                                ))
                                ->set_header_template('<%- option_label %>')
                                ->set_required(true),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('checkbox', 'Checkbox Group', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_layout', 'Layout')
                                ->set_options(array(
                                    'list' => 'List (Standard)',
                                    'tiles' => 'Tiles (Icons/Images)',
                                ))
                                ->set_default_value('list')
                                ->set_width(50),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('complex', 'field_options', 'Options')
                                ->set_layout('tabbed-horizontal')
                                ->add_fields(array(
                                    Field::make('text', 'option_value', 'Value')->set_required(true)->set_width(50),
                                    Field::make('text', 'option_label', 'Label')->set_required(true)->set_width(50),
                                    Field::make('select', 'option_icon', 'Icon')
                                        ->set_options('rockstars_get_quiz_icons')
                                        ->set_width(50),
                                    Field::make('image', 'option_image', 'Image')
                                        ->set_value_type('url')
                                        ->set_width(50),
                                ))
                                ->set_header_template('<%- option_label %>'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('select', 'Select Dropdown', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('text', 'field_placeholder', 'Placeholder'),
                            Field::make('complex', 'field_options', 'Options')
                                ->set_layout('tabbed-horizontal')
                                ->setup_labels(array(
                                    'plural_name' => 'Options',
                                    'singular_name' => 'Option',
                                ))
                                ->add_fields(array(
                                    Field::make('text', 'option_value', 'Value')
                                        ->set_help_text('Internal value (e.g. "kyiv")')
                                        ->set_required(true)
                                        ->set_width(50),
                                    Field::make('text', 'option_label', 'Label')
                                        ->set_help_text('Display text (e.g. "Киев")')
                                        ->set_required(true)
                                        ->set_width(50),
                                ))
                                ->set_header_template('<%- option_label %>'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('range', 'Range Slider', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('text', 'field_min', 'Minimum Value')
                                ->set_default_value('0')
                                ->set_width(25),
                            Field::make('text', 'field_max', 'Maximum Value')
                                ->set_default_value('100000')
                                ->set_width(25),
                            Field::make('text', 'field_step', 'Step')
                                ->set_default_value('1000')
                                ->set_help_text('Increment value')
                                ->set_width(25),
                            Field::make('text', 'field_default', 'Default Value')
                                ->set_default_value('50000')
                                ->set_width(25),
                            Field::make('text', 'field_prefix', 'Prefix (e.g. $)')
                                ->set_help_text('Symbol before value')
                                ->set_width(25),
                            Field::make('text', 'field_suffix', 'Suffix (e.g. USD)')
                                ->set_help_text('Symbol after value')
                                ->set_width(25),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('info', 'Info Text Only', array(
                            Field::make('rich_text', 'field_content', 'Content'),
                        ))
                        ->add_fields('file', 'File Upload', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('text', 'field_placeholder', 'Placeholder Text')->set_help_text('e.g. "Choose file..."'),
                            Field::make('text', 'field_file_types', 'Allowed Extensions')
                                ->set_help_text('Comma separated, e.g. pdf, docx, png, jpg')
                                ->set_default_value('pdf, docx, png, jpg, zip'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('phone', 'Phone Number', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('text', 'field_placeholder', 'Placeholder')->set_default_value('+7 (___) ___-__-__'),
                            Field::make('text', 'field_mask', 'Input Mask')
                                ->set_default_value('+7 (999) 999-99-99')
                                ->set_help_text('Use 9 for digits. Example: +7 (999) 999-99-99'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('switch', 'Switch / Toggle', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('text', 'field_on_label', 'ON Label')->set_default_value('Yes'),
                            Field::make('text', 'field_off_label', 'OFF Label')->set_default_value('No'),
                            Field::make('checkbox', 'field_default_state', 'Default state is ON?'),
                        ))
                        ->add_fields('date', 'Date Picker', array(
                            Field::make('text', 'field_label', 'Label')->set_required(true),
                            Field::make('text', 'field_name', 'Field ID (Name)')->set_required(true),
                            Field::make('text', 'field_placeholder', 'Placeholder')->set_default_value('Select date'),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                ))
                ->set_header_template('Step')
        ));
}


