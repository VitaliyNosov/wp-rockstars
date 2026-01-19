<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_register_quiz_settings');
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
                            Field::make('select', 'field_purpose', 'Purpose')
                                ->set_options(array(
                                    'none' => 'Regular Field',
                                    'name' => 'User Name (Identity)',
                                    'email' => 'User Email (Identity)',
                                ))
                                ->set_default_value('none')
                                ->set_width(50),
                            Field::make('textarea', 'field_options', 'Options')
                                ->set_help_text('Enter one option per line in format: value:Label (e.g. "web:Web Development")')
                                ->set_required(true),
                            Field::make('checkbox', 'field_required', 'Required?'),
                        ))
                        ->add_fields('checkbox', 'Checkbox Group', array(
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
                            Field::make('textarea', 'field_options', 'Options')
                                ->set_help_text('Enter one option per line in format: value:Label'),
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
                        ->add_fields('info', 'Info Text Only', array(
                            Field::make('rich_text', 'field_content', 'Content'),
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
