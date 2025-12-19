<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'post_meta', 'Landing Page Options' )
    ->where( 'post_template', '=', 'landings-template.php' )
    ->add_tab( 'Hero Section', array(
        // --- TEXT CONTENT ---
        Field::make( 'text', 'hero_title', 'Hero Title' ),
        Field::make( 'textarea', 'hero_description', 'Hero Description' )
            ->set_rows( 4 ),
        
        // --- BUTTONS ---
        Field::make( 'text', 'hero_btn_1_text', 'Button 1 Text' ),
        Field::make( 'text', 'hero_btn_1_url', 'Button 1 URL' ),
        
        Field::make( 'text', 'hero_btn_2_text', 'Button 2 Text' ),
        Field::make( 'text', 'hero_btn_2_url', 'Button 2 URL' ),
        
        // --- MEDIA ---
        Field::make( 'image', 'hero_image', 'Hero Image (Right Side)' )
            ->set_value_type( 'url' ),

        // --- REVIEWS ---
        Field::make( 'checkbox', 'hero_show_reviews', 'Show Reviews?' )
            ->set_option_value( 'yes' ),
        
        Field::make( 'text', 'hero_reviews_rating_1', 'Review 1 Rating' )
            ->set_default_value( '4.6' ),
        Field::make( 'text', 'hero_reviews_count_1', 'Review 1 Count' )
            ->set_default_value( '12k reviews' ),
            
        Field::make( 'text', 'hero_reviews_rating_2', 'Review 2 Rating' )
            ->set_default_value( '4.8' ),
        Field::make( 'text', 'hero_reviews_count_2', 'Review 2 Count' )
            ->set_default_value( '5k reviews' ),
    ) )
    ->add_tab( 'Section Two (Blog)', array(
        // --- HEADER & INTRO ---
        Field::make( 'text', 'sec2_title', 'Section Header' )
            ->set_default_value( 'Section header name' ),
            
        // --- CONTENT BUILDER ---
        Field::make( 'complex', 'sec2_content', 'Content Builder' )
            ->set_layout( 'tabbed-horizontal' )
            ->add_fields( 'paragraph', 'Paragraph', array(
                Field::make( 'textarea', 'content', 'Text' )
                    ->set_rows( 4 ),
            ) )
            ->add_fields( 'heading', 'Heading', array(
                Field::make( 'text', 'text', 'Heading Text' ),
            ) )
            ->add_fields( 'quote', 'Quote', array(
                Field::make( 'textarea', 'text', 'Quote Text' )
                    ->set_rows( 2 ),
                Field::make( 'text', 'author', 'Author' ),
            ) )
            ->add_fields( 'image', 'Image', array(
                Field::make( 'image', 'image', 'Image' )
                    ->set_value_type( 'url' ),
                Field::make( 'text', 'caption', 'Caption' ),
            ) )
            ->add_fields( 'list', 'List', array(
                 Field::make( 'complex', 'items', 'List Items' )
                    ->add_fields( array(
                        Field::make( 'text', 'text', 'Item Text' ),
                    ) ),
            ) ),
        
        Field::make( 'complex', 'sec2_tags', 'Tags/Links' )
            ->add_fields( array(
                Field::make( 'text', 'tag_label', 'Label' ),
                Field::make( 'text', 'tag_url', 'URL' ),
            ) ),
    ) )
    ->add_tab( 'Section Three (Features)', array(
         Field::make( 'text', 'sec3_header', 'Section Header' )
            ->set_default_value( 'Section header name' ),
         Field::make( 'text', 'sec3_title', 'Main Title' )
            ->set_default_value( 'Fully customizable rules to match your unique needs' ),
            
         Field::make( 'complex', 'sec3_tabs', 'Tabs' )
            ->set_layout( 'tabbed-vertical' )
            ->add_fields( array(
                Field::make( 'text', 'tab_title', 'Tab Title' ),
                Field::make( 'textarea', 'tab_desc', 'Tab Description' )
                    ->set_rows( 2 ),
                Field::make( 'textarea', 'tab_icon', 'Tab Icon (SVG Code)' )
                    ->set_rows( 4 ),
                Field::make( 'image', 'tab_image', 'Tab Image' )
                    ->set_value_type( 'url' ),
            ) ),
    ) )
    ->add_tab( 'Section Four (Icon Blocks)', array(
         Field::make( 'text', 'sec4_header', 'Section Header' )
            ->set_default_value( 'Section header name' ),
            
         Field::make( 'complex', 'sec4_cards', 'Cards' )
            ->add_fields( array(
                Field::make( 'textarea', 'card_icon', 'Icon (SVG Code)' )
                    ->set_rows( 4 ),
                Field::make( 'text', 'card_title', 'Title' ),
                Field::make( 'textarea', 'card_desc', 'Description' )
                    ->set_rows( 2 ),
            ) ),
    ) )
    ->add_tab( 'Section Five (FAQ)', array(
         Field::make( 'text', 'sec5_title', 'Section Title' )
            ->set_default_value( 'You might be wondering...' ),
            
         Field::make( 'complex', 'sec5_faqs', 'FAQs' )
            ->add_fields( array(
                Field::make( 'textarea', 'faq_icon', 'Icon (SVG Code)' )
                    ->set_rows( 4 ),
                Field::make( 'text', 'faq_question', 'Question' ),
                Field::make( 'textarea', 'faq_answer', 'Answer' )
                    ->set_rows( 2 ),
            ) ),
    ) )
    ->add_tab( 'Section Six (Pricing)', array(
         Field::make( 'text', 'sec6_title', 'Section Title' )
            ->set_default_value( 'Pricing' ),
         Field::make( 'textarea', 'sec6_desc', 'Description' )
            ->set_rows( 2 ),
            
         Field::make( 'complex', 'sec6_cards', 'Pricing Cards' )
            ->set_layout( 'tabbed-vertical' )
            ->add_fields( array(
                Field::make( 'text', 'card_title', 'Plan Name' ),
                Field::make( 'text', 'card_price_monthly', 'Monthly Price' ),
                Field::make( 'text', 'card_price_annually', 'Annual Price' ),
                Field::make( 'textarea', 'card_desc', 'Description' )
                     ->set_rows( 2 ),
                Field::make( 'complex', 'card_features', 'Features' )
                    ->add_fields( array(
                        Field::make( 'text', 'feature_text', 'Feature' ),
                    ) ),
                Field::make( 'text', 'button_text', 'Button Text' ),
                Field::make( 'text', 'button_url', 'Button URL' ),
                Field::make( 'checkbox', 'is_popular', 'Is Popular?' ),
            ) ),
    ) )
    ->add_tab( 'Section Seven (Blog)', array(
         Field::make( 'text', 'sec7_title', 'Section Title' )
            ->set_default_value( 'The Blog' ),
         Field::make( 'textarea', 'sec7_desc', 'Description' )
            ->set_rows( 2 ),
            
         Field::make( 'association', 'sec7_category', 'Select Category' )
            ->set_types( array(
                array(
                    'type' => 'term',
                    'taxonomy' => 'category',
                ),
            ) )
            ->set_max( 1 ),
            
         Field::make( 'text', 'sec7_read_more_text', 'Button Text' )
            ->set_default_value( 'Read more' ),
         Field::make( 'text', 'sec7_read_more_url', 'Button URL' ),
    ) )
    ->add_tab( 'Section Eight (Subscribe)', array(
         Field::make( 'text', 'sec8_title', 'Section Title' )
            ->set_default_value( 'Sign up to our newsletter' ),
            
         Field::make( 'text', 'sec8_input_placeholder', 'Input Placeholder' )
            ->set_default_value( 'Enter your email' ),
            
         Field::make( 'text', 'sec8_btn_text', 'Button Text' )
            ->set_default_value( 'Subscribe' ),
    ) );
