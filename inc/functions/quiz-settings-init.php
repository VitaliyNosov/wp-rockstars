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
 * Enqueue Admin Scripts for Quiz Builder
 */
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script('lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true);
    wp_enqueue_script('quiz-admin-js', get_template_directory_uri() . '/common/js/quiz-admin.js', array('jquery'), '1.0.1', true);
    
    // Pass icon list to JS
    wp_localize_script('quiz-admin-js', 'quizIconData', array(
        'icons' => rockstars_get_quiz_icons()
    ));
});

function crb_register_quiz_settings() {
    Container::make('theme_options', 'Quiz Builder')
        ->set_page_parent('edit.php?post_type=quiz_submission')
        ->add_fields(array(
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

add_action('admin_head', 'quiz_admin_styles');
function quiz_admin_styles() {
    ?>
    <style>
        /* Exact CSS numbering based on provided HTML structure */
        
        /* 1. Reset counter on the UL container */
        .cf-complex__tabs-list {
            counter-reset: quiz-step-counter;
        }

        /* 2. Increment counter on each LI item */
        .cf-complex__tabs-item {
            counter-increment: quiz-step-counter;
        }
        
        /* 3. Simply append the number to existing "Step" text */
        /* No hiding, no custom colors - just native styles */
        .cf-complex__tabs-title:after {
            content: " " counter(quiz-step-counter);
        }
    </style>
    <?php
}
