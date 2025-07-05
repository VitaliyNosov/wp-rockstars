<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Section one

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Content: Section one')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'page-template-custom.php')
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
        ->where('post_template', '=', 'page-template-custom.php')
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

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Видео секция')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'page-template-custom.php')
        ->add_fields([
            Field::make('text', 'video_section_title', 'Заголовок')
                ->set_default_value('We are ready to help'),

            Field::make('textarea', 'video_section_description', 'Описание')
                ->set_default_value('There are many variations of passages of Lorem Ipsum available but the majority have suffered alteration in some form.')
                ->set_rows(4),

            Field::make('image', 'video_preview_image', 'Превью для видео')
                ->set_value_type('url'),

            Field::make('text', 'video_youtube_url', 'Ссылка на YouTube видео')
                ->set_help_text('Например: https://www.youtube.com/watch?v=6ZGxizUr99I'),

            Field::make('image', 'video_background_shape', 'Фоновое изображение снизу')
                ->set_value_type('url'),
        ]);
});

add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
});

// Section five

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Секция: Логотипы брендов')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'page-template-custom.php')
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
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'page-template-custom.php')
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
        ->where('post_template', '=', 'page-template-custom.php')
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
        ->where('post_template', '=', 'page-template-custom.php')
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


// Section ten - это theme_options, не привязано к страницам

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


// Section eleven - это theme_options, не привязано к страницам

add_action('carbon_fields_register_fields', 'pricing_section_fields');

function pricing_section_fields() {
    Container::make('theme_options', __('Настройки ценообразования'))
        ->set_page_menu_title(__('Ценообразование'))
        ->set_page_menu_position(25)
        ->add_tab(__('Основные настройки'), array(
            Field::make('checkbox', 'pricing_section_enabled', __('Включить секцию ценообразования'))
                ->set_default_value(true),
                
            Field::make('text', 'pricing_section_title', __('Заголовок секции'))
                ->set_default_value('Simple and Affordable Pricing')
                ->set_conditional_logic(array(
                    array(
                        'field' => 'pricing_section_enabled',
                        'value' => true,
                    )
                )),
                
            Field::make('textarea', 'pricing_section_description', __('Описание секции'))
                ->set_default_value('There are many variations of passages of Lorem Ipsum available but the majority have suffered alteration in some form.')
                ->set_conditional_logic(array(
                    array(
                        'field' => 'pricing_section_enabled',
                        'value' => true,
                    )
                )),
                
            Field::make('text', 'pricing_monthly_label', __('Текст "Месячно"'))
                ->set_default_value('Monthly')
                ->set_conditional_logic(array(
                    array(
                        'field' => 'pricing_section_enabled',
                        'value' => true,
                    )
                )),
                
            Field::make('text', 'pricing_yearly_label', __('Текст "Годично"'))
                ->set_default_value('Yearly')
                ->set_conditional_logic(array(
                    array(
                        'field' => 'pricing_section_enabled',
                        'value' => true,
                    )
                )),
        ))
        ->add_tab(__('Тарифные планы'), array(
            Field::make('complex', 'pricing_plans', __('Тарифные планы'))
                ->add_fields(array(
                    Field::make('text', 'plan_name', __('Название плана'))
                        ->set_width(50),
                        
                    Field::make('text', 'plan_price_monthly', __('Цена в месяц'))
                        ->set_width(25),
                        
                    Field::make('text', 'plan_price_yearly', __('Цена в год'))
                        ->set_width(25),
                        
                    Field::make('textarea', 'plan_description', __('Описание плана'))
                        ->set_rows(3),
                        
                    Field::make('text', 'plan_button_text', __('Текст кнопки'))
                        ->set_default_value('Start Free Trial')
                        ->set_width(50),
                        
                    Field::make('text', 'plan_button_url', __('Ссылка кнопки'))
                        ->set_width(50),
                        
                    Field::make('complex', 'plan_features', __('Возможности плана'))
                        ->add_fields(array(
                            Field::make('text', 'feature_text', __('Текст возможности')),
                            Field::make('select', 'feature_status', __('Статус'))
                                ->set_options(array(
                                    'included' => __('Включено'),
                                    'excluded' => __('Исключено'),
                                ))
                                ->set_default_value('included')
                        ))
                        ->set_header_template('<%- feature_text %>')
                        ->set_collapsed(true),
                        
                    Field::make('checkbox', 'plan_is_popular', __('Популярный план'))
                        ->set_help_text(__('Отметить как рекомендуемый план')),
                ))
                ->set_header_template('<%- plan_name %> - $<%- plan_price_monthly %>/мес')
                ->set_collapsed(true)
                ->set_default_value(array(
                    array(
                        'plan_name' => 'Lite',
                        'plan_price_monthly' => '40',
                        'plan_price_yearly' => '400',
                        'plan_description' => 'Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.',
                        'plan_button_text' => 'Start Free Trial',
                        'plan_button_url' => '#',
                        'plan_features' => array(
                            array('feature_text' => 'All UI Components', 'feature_status' => 'included'),
                            array('feature_text' => 'Use with Unlimited Projects', 'feature_status' => 'included'),
                            array('feature_text' => 'Commercial Use', 'feature_status' => 'included'),
                            array('feature_text' => 'Email Support', 'feature_status' => 'included'),
                            array('feature_text' => 'Lifetime Access', 'feature_status' => 'excluded'),
                            array('feature_text' => 'Free Lifetime Updates', 'feature_status' => 'excluded'),
                        )
                    ),
                    array(
                        'plan_name' => 'Basic',
                        'plan_price_monthly' => '399',
                        'plan_price_yearly' => '3990',
                        'plan_description' => 'Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.',
                        'plan_button_text' => 'Start Free Trial',
                        'plan_button_url' => '#',
                        'plan_is_popular' => true,
                        'plan_features' => array(
                            array('feature_text' => 'All UI Components', 'feature_status' => 'included'),
                            array('feature_text' => 'Use with Unlimited Projects', 'feature_status' => 'included'),
                            array('feature_text' => 'Commercial Use', 'feature_status' => 'included'),
                            array('feature_text' => 'Email Support', 'feature_status' => 'included'),
                            array('feature_text' => 'Lifetime Access', 'feature_status' => 'included'),
                            array('feature_text' => 'Free Lifetime Updates', 'feature_status' => 'excluded'),
                        )
                    ),
                    array(
                        'plan_name' => 'Plus',
                        'plan_price_monthly' => '589',
                        'plan_price_yearly' => '5890',
                        'plan_description' => 'Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.',
                        'plan_button_text' => 'Start Free Trial',
                        'plan_button_url' => '#',
                        'plan_features' => array(
                            array('feature_text' => 'All UI Components', 'feature_status' => 'included'),
                            array('feature_text' => 'Use with Unlimited Projects', 'feature_status' => 'included'),
                            array('feature_text' => 'Commercial Use', 'feature_status' => 'included'),
                            array('feature_text' => 'Email Support', 'feature_status' => 'included'),
                            array('feature_text' => 'Lifetime Access', 'feature_status' => 'included'),
                            array('feature_text' => 'Free Lifetime Updates', 'feature_status' => 'included'),
                        )
                    ),
                ))
                ->set_conditional_logic(array(
                    array(
                        'field' => 'pricing_section_enabled',
                        'value' => true,
                    )
                )),
        ));
}

// Section FAQ - это theme_options, не привязано к страницам

add_action('carbon_fields_register_fields', 'register_faq_admin_page');
function register_faq_admin_page() {
    Container::make('theme_options', 'FAQ')
        ->set_icon('dashicons-editor-help')
        ->set_page_menu_position(99)
        ->add_fields([
            Field::make('complex', 'faq_items', 'Список вопросов')
                ->set_layout('tabbed-horizontal')
                ->add_fields('faq_item', [
                    Field::make('text', 'question', 'Вопрос'),
                    Field::make('rich_text', 'answer', 'Ответ'),
                ]),
        ]);
}

function wp_faq_add_class_to_paragraphs($content) {
    return preg_replace(
        '/<p(.*?)>/i',
        '<p class="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed"$1>',
        $content
    );
}