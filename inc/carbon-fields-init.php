<?php

// Slider front page 

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Portfolio slides')
        ->where('post_type', '=', 'page')  // for pages
        ->add_fields([
            Field::make('complex', 'portfolio_slides', 'Portfolio slides')
                ->set_collapsed(true)
                ->add_fields([
                    Field::make('image', 'slide_image', 'Slide image'),
                    Field::make('text', 'slide_alt', 'Alt text'),
                    Field::make('text', 'slide_url', 'Link to slide'),
                ])
        ]);
});



