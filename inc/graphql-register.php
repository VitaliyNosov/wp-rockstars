<?php

add_action('graphql_register_types', function () {
    // Hero Section Fields
    register_graphql_field('Page', 'heroSection', [
        'type' => 'HeroSection',
        'description' => 'Hero Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            return [
                'title' => carbon_get_post_meta($id, 'hero_title'),
                'description' => carbon_get_post_meta($id, 'hero_description'),
                'button1Text' => carbon_get_post_meta($id, 'hero_button1_text'),
                'button1Url' => carbon_get_post_meta($id, 'hero_button1_url'),
                'button2Text' => carbon_get_post_meta($id, 'hero_button2_text'),
                'button2Url' => carbon_get_post_meta($id, 'hero_button2_url'),
            ];
        }
    ]);

    register_graphql_object_type('HeroSection', [
        'description' => 'Hero Section Fields',
        'fields' => [
            'title' => ['type' => 'String'],
            'description' => ['type' => 'String'],
            'button1Text' => ['type' => 'String'],
            'button1Url' => ['type' => 'String'],
            'button2Text' => ['type' => 'String'],
            'button2Url' => ['type' => 'String'],
        ],
    ]);

    // Features Section Fields
    register_graphql_field('Page', 'featuresSection', [
        'type' => 'FeaturesSection',
        'description' => 'Features Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            return [
                'featuresSectionTitle' => carbon_get_post_meta($id, 'features_section_title'),
                'featuresSectionDescription' => carbon_get_post_meta($id, 'features_section_description'),
                'featuresList' => carbon_get_post_meta($id, 'features_list'),
            ];
        }
    ]);

    register_graphql_object_type('FeatureItem', [
        'description' => 'Feature Item',
        'fields' => [
            'featureIconSvg' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['feature_icon_svg'] ?? '';
                }
            ],
            'featureTitle' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['feature_title'] ?? '';
                }
            ],
            'featureDescription' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['feature_description'] ?? '';
                }
            ],
        ],
    ]);

    register_graphql_object_type('FeaturesSection', [
        'description' => 'Features Section Fields',
        'fields' => [
            'featuresSectionTitle' => ['type' => 'String'],
            'featuresSectionDescription' => ['type' => 'String'],
            'featuresList' => ['type' => ['list_of' => 'FeatureItem']],
        ],
    ]);

    // Video Section Fields
    register_graphql_field('Page', 'videoSection', [
        'type' => 'VideoSection',
        'description' => 'Video Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            return [
                'videoSectionTitle' => carbon_get_post_meta($id, 'video_section_title'),
                'videoSectionDescription' => carbon_get_post_meta($id, 'video_section_description'),
                'videoPreviewImage' => carbon_get_post_meta($id, 'video_preview_image'),
                'videoYoutubeUrl' => carbon_get_post_meta($id, 'video_youtube_url'),
                'videoBackgroundShape' => carbon_get_post_meta($id, 'video_background_shape'),
            ];
        }
    ]);

    register_graphql_object_type('VideoSection', [
        'description' => 'Video Section Fields',
        'fields' => [
            'videoSectionTitle' => ['type' => 'String'],
            'videoSectionDescription' => ['type' => 'String'],
            'videoPreviewImage' => ['type' => 'String'],
            'videoYoutubeUrl' => ['type' => 'String'],
            'videoBackgroundShape' => ['type' => 'String'],
        ],
    ]);
});
