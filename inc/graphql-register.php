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

    // Brands Section Fields
    register_graphql_field('Page', 'brandsSection', [
        'type' => 'BrandsSection',
        'description' => 'Brands Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            $logos = carbon_get_post_meta($id, 'brand_logos_list');
            return [
                'brandLogosList' => $logos ?: [],
            ];
        }
    ]);

    register_graphql_object_type('BrandItem', [
        'description' => 'Brand Logo Item',
        'fields' => [
            'brandLogo' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['brand_logo'] ?? '';
                }
            ],
            'brandLink' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['brand_link'] ?? '';
                }
            ],
            'brandAlt' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['brand_alt'] ?? '';
                }
            ],
        ],
    ]);

    register_graphql_object_type('BrandsSection', [
        'description' => 'Brands Section Fields',
        'fields' => [
            'brandLogosList' => ['type' => ['list_of' => 'BrandItem']],
        ],
    ]);

    // Portfolio Section Fields
    register_graphql_field('Page', 'portfolioSection', [
        'type' => 'PortfolioSection',
        'description' => 'Portfolio Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            $slides = carbon_get_post_meta($id, 'portfolio_slides');
            return [
                'portfolioSlides' => $slides ?: [],
            ];
        }
    ]);

    register_graphql_object_type('PortfolioSlide', [
        'description' => 'Portfolio Slide Item',
        'fields' => [
            'slideImage' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    $img_id = $item['slide_image'] ?? '';
                    if ($img_id) {
                        $img_url = wp_get_attachment_image_url($img_id, 'full');
                        return $img_url ?: '';
                    }
                    return '';
                }
            ],
            'slideUrl' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['slide_url'] ?? '';
                }
            ],
            'slideAlt' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['slide_alt'] ?? '';
                }
            ],
        ],
    ]);

    register_graphql_object_type('PortfolioSection', [
        'description' => 'Portfolio Section Fields',
        'fields' => [
            'portfolioSlides' => ['type' => ['list_of' => 'PortfolioSlide']],
        ],
    ]);

    // About Section Fields
    register_graphql_field('Page', 'aboutSection', [
        'type' => 'AboutSection',
        'description' => 'About Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            $features = carbon_get_post_meta($id, 'about_features');
            
            $img_val = carbon_get_post_meta($id, 'about_image');
            $img_url = $img_val;
            // If it's an ID (numeric), get the URL. Otherwise assume it's already a URL.
            if (is_numeric($img_val)) {
                $attachment_url = wp_get_attachment_image_url($img_val, 'full');
                if ($attachment_url) {
                    $img_url = $attachment_url;
                }
            }

            return [
                'title' => carbon_get_post_meta($id, 'about_title'),
                'subtitle' => carbon_get_post_meta($id, 'about_subtitle'),
                'image' => $img_url,
                'features' => $features ?: [],
            ];
        }
    ]);

    register_graphql_object_type('AboutFeature', [
        'description' => 'About Feature Item',
        'fields' => [
            'featureText' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['feature_text'] ?? '';
                }
            ],
        ],
    ]);

    register_graphql_object_type('AboutSection', [
        'description' => 'About Section Fields',
        'fields' => [
            'title' => ['type' => 'String'],
            'subtitle' => ['type' => 'String'],
            'image' => ['type' => 'String'],
            'features' => ['type' => ['list_of' => 'AboutFeature']],
        ],
    ]);

    // Benefits Section Fields
    register_graphql_field('Page', 'benefitsSection', [
        'type' => 'BenefitsSection',
        'description' => 'Benefits Section Data',
        'resolve' => function ($post) {
            $id = $post->ID;
            $benefits = carbon_get_post_meta($id, 'benefits_list');
            
            $img_val = carbon_get_post_meta($id, 'benefits_image');
            $img_url = $img_val;
            if (is_numeric($img_val)) {
                $attachment_url = wp_get_attachment_image_url($img_val, 'full');
                if ($attachment_url) {
                    $img_url = $attachment_url;
                }
            }

            return [
                'image' => $img_url,
                'benefits' => $benefits ?: [],
            ];
        }
    ]);

    register_graphql_object_type('BenefitItem', [
        'description' => 'Benefit Item',
        'fields' => [
            'benefitTitle' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['benefit_title'] ?? '';
                }
            ],
            'benefitDescription' => [
                'type' => 'String',
                'resolve' => function ($item) {
                    return $item['benefit_description'] ?? '';
                }
            ],
        ],
    ]);

    register_graphql_object_type('BenefitsSection', [
        'description' => 'Benefits Section Fields',
        'fields' => [
            'image' => ['type' => 'String'],
            'benefits' => ['type' => ['list_of' => 'BenefitItem']],
        ],
    ]);

    // Testimonials Section (from theme options)
    register_graphql_field('RootQuery', 'testimonialsSection', [
        'type' => 'TestimonialsSection',
        'description' => 'Testimonials Section Data from Theme Options',
        'resolve' => function () {
            $testimonials = carbon_get_theme_option('testimonial_list');
            $processed_testimonials = [];
            
            if ($testimonials && is_array($testimonials)) {
                foreach ($testimonials as $item) {
                    $photo_id = $item['photo'] ?? null;
                    $photo_url = '';
                    if ($photo_id) {
                        if (is_numeric($photo_id)) {
                            $photo_url = wp_get_attachment_image_url($photo_id, 'thumbnail') ?: '';
                        } else {
                            $photo_url = $photo_id;
                        }
                    }
                    
                    $processed_testimonials[] = [
                        'rating' => $item['rating'] ?? '5',
                        'text' => $item['text'] ?? '',
                        'photo' => $photo_url,
                        'name' => $item['name'] ?? '',
                        'position' => $item['position'] ?? '',
                    ];
                }
            }

            return [
                'title' => carbon_get_theme_option('testimonial_title'),
                'description' => carbon_get_theme_option('testimonial_description'),
                'testimonials' => $processed_testimonials,
            ];
        }
    ]);

    register_graphql_object_type('TestimonialItem', [
        'description' => 'Testimonial Item',
        'fields' => [
            'rating' => ['type' => 'String'],
            'text' => ['type' => 'String'],
            'photo' => ['type' => 'String'],
            'name' => ['type' => 'String'],
            'position' => ['type' => 'String'],
        ],
    ]);

    register_graphql_object_type('TestimonialsSection', [
        'description' => 'Testimonials Section Fields',
        'fields' => [
            'title' => ['type' => 'String'],
            'description' => ['type' => 'String'],
            'testimonials' => ['type' => ['list_of' => 'TestimonialItem']],
        ],
    ]);

    // Pricing Section (from theme options)
    register_graphql_field('RootQuery', 'pricingSection', [
        'type' => 'PricingSection',
        'description' => 'Pricing Section Data from Theme Options',
        'resolve' => function () {
            $enabled = carbon_get_theme_option('pricing_section_enabled');
            $pricing_plans = carbon_get_theme_option('pricing_plans');
            $processed_plans = [];
            
            if ($pricing_plans && is_array($pricing_plans)) {
                foreach ($pricing_plans as $plan) {
                    $features = $plan['plan_features'] ?? [];
                    $processed_features = [];
                    
                    if ($features && is_array($features)) {
                        foreach ($features as $feature) {
                            $processed_features[] = [
                                'text' => $feature['feature_text'] ?? '',
                                'status' => $feature['feature_status'] ?? 'included',
                            ];
                        }
                    }
                    
                    $processed_plans[] = [
                        'name' => $plan['plan_name'] ?? '',
                        'monthlyPrice' => $plan['plan_price_monthly'] ?? '0',
                        'yearlyPrice' => $plan['plan_price_yearly'] ?? '0',
                        'description' => $plan['plan_description'] ?? '',
                        'buttonText' => $plan['plan_button_text'] ?? 'Start Free Trial',
                        'buttonUrl' => $plan['plan_button_url'] ?? '#',
                        'isPopular' => !empty($plan['plan_is_popular']),
                        'features' => $processed_features,
                    ];
                }
            }

            return [
                'enabled' => !empty($enabled),
                'title' => carbon_get_theme_option('pricing_section_title'),
                'description' => carbon_get_theme_option('pricing_section_description'),
                'monthlyLabel' => carbon_get_theme_option('pricing_monthly_label'),
                'yearlyLabel' => carbon_get_theme_option('pricing_yearly_label'),
                'pricingPlans' => $processed_plans,
            ];
        }
    ]);

    register_graphql_object_type('PricingFeature', [
        'description' => 'Pricing Feature Item',
        'fields' => [
            'text' => ['type' => 'String'],
            'status' => ['type' => 'String'],
        ],
    ]);

    register_graphql_object_type('PricingPlan', [
        'description' => 'Pricing Plan Item',
        'fields' => [
            'name' => ['type' => 'String'],
            'monthlyPrice' => ['type' => 'String'],
            'yearlyPrice' => ['type' => 'String'],
            'description' => ['type' => 'String'],
            'buttonText' => ['type' => 'String'],
            'buttonUrl' => ['type' => 'String'],
            'isPopular' => ['type' => 'Boolean'],
            'features' => ['type' => ['list_of' => 'PricingFeature']],
        ],
    ]);

    register_graphql_object_type('PricingSection', [
        'description' => 'Pricing Section Fields',
        'fields' => [
            'enabled' => ['type' => 'Boolean'],
            'title' => ['type' => 'String'],
            'description' => ['type' => 'String'],
            'monthlyLabel' => ['type' => 'String'],
            'yearlyLabel' => ['type' => 'String'],
            'pricingPlans' => ['type' => ['list_of' => 'PricingPlan']],
        ],
    ]);

    // Blog Section - Latest Posts
    register_graphql_field('RootQuery', 'latestBlogPosts', [
        'type' => ['list_of' => 'BlogPost'],
        'description' => 'Latest Blog Posts',
        'resolve' => function () {
            // First try to get posts from 'lasts-posts' category
            $args_category = [
                'post_type' => 'post',
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'category_name' => 'lasts-posts',
                'orderby' => 'date',
                'order' => 'DESC',
            ];
            
            $category_posts = new WP_Query($args_category);
            
            // If no posts in category, get latest 3 posts
            if (!$category_posts->have_posts()) {
                wp_reset_postdata();
                $args_latest = [
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ];
                $posts_query = new WP_Query($args_latest);
            } else {
                $posts_query = $category_posts;
            }
            
            $posts = [];
            
            if ($posts_query->have_posts()) {
                while ($posts_query->have_posts()) {
                    $posts_query->the_post();
                    
                    // Get featured image
                    $featured_image = '';
                    if (has_post_thumbnail()) {
                        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    }
                    
                    // Get first category
                    $categories = get_the_category();
                    $category_name = !empty($categories) ? $categories[0]->name : 'Blog';
                    
                    // Get author info
                    $author_id = get_the_author_meta('ID');
                    $author_avatar = get_avatar_url($author_id, ['size' => 40]);
                    $author_description = get_the_author_meta('description');
                    if (empty($author_description)) {
                        $author_description = 'Автор блогу';
                    }
                    
                    // Get excerpt
                    $excerpt = get_the_excerpt();
                    if (empty($excerpt)) {
                        $excerpt = wp_trim_words(get_the_content(), 20, '...');
                    } else {
                        $excerpt = wp_trim_words($excerpt, 20, '...');
                    }
                    
                    $posts[] = [
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'excerpt' => $excerpt,
                        'permalink' => get_permalink(),
                        'featuredImage' => $featured_image,
                        'categoryName' => $category_name,
                        'authorName' => get_the_author(),
                        'authorAvatar' => $author_avatar,
                        'authorDescription' => $author_description,
                        'authorUrl' => get_author_posts_url($author_id),
                        'date' => get_the_date('j M, Y'),
                    ];
                }
            }
            
            wp_reset_postdata();
            
            return $posts;
        }
    ]);

    register_graphql_object_type('BlogPost', [
        'description' => 'Blog Post Item',
        'fields' => [
            'id' => ['type' => 'Int'],
            'title' => ['type' => 'String'],
            'excerpt' => ['type' => 'String'],
            'permalink' => ['type' => 'String'],
            'featuredImage' => ['type' => 'String'],
            'categoryName' => ['type' => 'String'],
            'authorName' => ['type' => 'String'],
            'authorAvatar' => ['type' => 'String'],
            'authorDescription' => ['type' => 'String'],
            'authorUrl' => ['type' => 'String'],
            'date' => ['type' => 'String'],
        ],
    ]);

    register_graphql_field('RootQuery', 'ticketNonce', [
        'type' => 'String',
        'description' => 'Nonce for ticket submission',
        'resolve' => function () {
            return wp_create_nonce('wp_custom_ticket_nonce');
        }
    ]);
});
