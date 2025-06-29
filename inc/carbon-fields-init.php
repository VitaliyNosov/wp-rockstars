<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Section one

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Content: Section one')
        ->where('post_type', '=', 'page')
        ->add_fields([
            Field::make('text', 'hero_title', 'Title'),
            Field::make('textarea', 'hero_description', 'Description'),

            Field::make('text', 'hero_button1_text', 'Button 1 — text'),
            Field::make('text', 'hero_button1_url', 'Button 1 — link'),

            Field::make('text', 'hero_button2_text', 'Button 2 — text'),
            Field::make('text', 'hero_button2_url', 'Button 2 — link'),
        ]);
});

// Section two

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Block: Main features')
        ->where('post_type', '=', 'page')
        ->add_fields([
            Field::make('text', 'features_section_title', 'Section header'),
            Field::make('textarea', 'features_section_description', 'Section Description'),

            Field::make('complex', 'features_list', 'Features')
                ->set_collapsed(true)
                ->add_fields([
                    Field::make('textarea', 'feature_icon_svg', 'SVG-code icons'),
                    Field::make('text', 'feature_title', 'Feature header'),
                    Field::make('textarea', 'feature_description', 'Feature Description'),
                ]),
        ]);
});


// Section three  ??? - Нужно еще сделать!

// Section five

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Секция: Логотипы брендов')
        ->where('post_type', '=', 'page')
        ->add_fields([
            Field::make('complex', 'brand_logos_list', 'Логотипы брендов')
                ->set_collapsed(true)
                ->add_fields([
                    Field::make('image', 'brand_logo', 'Логотип')->set_value_type('url'),
                    Field::make('text', 'brand_link', 'Ссылка'),
                    Field::make('text', 'brand_alt', 'Alt текст'),
                ]),
        ]);
});


// Section six (slider front page) 

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Portfolio Slides')
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


// Section seven

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Секция: О компании (About)')
        ->where('post_type', '=', 'page')
        ->add_fields([
            Field::make('text', 'about_title', 'Заголовок'),
            Field::make('textarea', 'about_subtitle', 'Подзаголовок'),
            Field::make('image', 'about_image', 'Изображение справа')->set_value_type('url'),

            Field::make('complex', 'about_features', 'Список преимуществ')
                ->set_collapsed(true)
                ->add_fields([
                    Field::make('text', 'feature_text', 'Текст преимущества'),
                ]),
        ]);
});


// Section nine

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Секция: Преимущества с изображением слева')
        ->where('post_type', '=', 'page')
        ->add_fields([
            Field::make('image', 'benefits_image', 'Изображение слева')->set_value_type('url'),

            Field::make('complex', 'benefits_list', 'Список преимуществ')
                ->set_collapsed(true)
                ->add_fields([
                    Field::make('text', 'benefit_title', 'Заголовок'),
                    Field::make('textarea', 'benefit_description', 'Описание'),
                ])
                ->set_max(3), // максимум 3 элемента
        ]);
});


// Section ten

Container::make('theme_options', 'Отзывы')
  ->add_fields([
    Field::make('text', 'testimonial_title', 'Заголовок секции'),
    Field::make('textarea', 'testimonial_description', 'Описание секции'),
    Field::make('complex', 'testimonial_list', 'Отзывы')
      ->set_layout('tabbed-horizontal')
      ->add_fields([
        Field::make('text', 'name', 'Имя'),
        Field::make('text', 'position', 'Должность'),
        Field::make('image', 'photo', 'Фото'),
        Field::make('textarea', 'text', 'Текст отзыва'),
        Field::make('select', 'rating', 'Рейтинг (звезды)')
          ->set_options([
            5 => '★★★★★',
            4 => '★★★★☆',
            3 => '★★★☆☆',
            2 => '★★☆☆☆',
            1 => '★☆☆☆☆',
          ])
      ]),
  ]);


// Section eleven



