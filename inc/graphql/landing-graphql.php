<?php
/**
 * Register GraphQL fields for the Landing Page template.
 *
 * @package Rock_Star
 */

add_action( 'graphql_register_types', 'register_landing_page_graphql_fields' );

function register_landing_page_graphql_fields() {

    // --- 1. Register Sub-Types for Complex Fields ---

    // Section 2: Content Blocks
    register_graphql_object_type( 'LandingContentBlock', [
        'fields' => [
            'type'    => [ 'type' => 'String' ],
            'content' => [ 'type' => 'String' ],
            'text'    => [ 'type' => 'String' ],
            'author'  => [ 'type' => 'String' ],
            'image'   => [ 'type' => 'String' ],
            'caption' => [ 'type' => 'String' ],
            'items'   => [ 'type' => 'String' ],
        ]
    ] );
    
    register_graphql_object_type( 'LandingTag', [
        'fields' => [
            'label' => [ 'type' => 'String' ],
            'url'   => [ 'type' => 'String' ],
        ]
    ] );

    // Section 3: Tabs
    register_graphql_object_type( 'LandingTab', [
        'fields' => [
            'title' => [ 'type' => 'String' ],
            'desc'  => [ 'type' => 'String' ],
            'icon'  => [ 'type' => 'String' ],
            'image' => [ 'type' => 'String' ],
        ]
    ] );

    // Section 4: Icon Cards
    register_graphql_object_type( 'LandingIconCard', [
        'fields' => [
            'icon'  => [ 'type' => 'String' ],
            'title' => [ 'type' => 'String' ],
            'desc'  => [ 'type' => 'String' ],
        ]
    ] );

    // Section 5: FAQ
    register_graphql_object_type( 'LandingFaq', [
        'fields' => [
            'icon'     => [ 'type' => 'String' ],
            'question' => [ 'type' => 'String' ],
            'answer'   => [ 'type' => 'String' ],
        ]
    ] );

    // Section 6: Pricing
    register_graphql_object_type( 'LandingPricingFeature', [
        'fields' => [
            'text' => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'LandingPricingCard', [
        'fields' => [
            'title'        => [ 'type' => 'String' ],
            'priceMonthly' => [ 'type' => 'String' ],
            'priceAnnual'  => [ 'type' => 'String' ],
            'desc'         => [ 'type' => 'String' ],
            'features'     => [ 'type' => [ 'list_of' => 'LandingPricingFeature' ] ],
            'btnText'      => [ 'type' => 'String' ],
            'btnUrl'       => [ 'type' => 'String' ],
            'isPopular'    => [ 'type' => 'Boolean' ],
        ]
    ] );


    // --- 2. Register Main Data Object ---
    register_graphql_object_type( 'LandingPageData', [
        'description' => 'Data from Carbon Fields for the Landing Page Template',
        'fields' => [
            // Hero
            'heroTitle' => [ 'type' => 'String' ],
            'heroDescription' => [ 'type' => 'String' ],
            'heroBtn1Text' => [ 'type' => 'String' ],
            'heroBtn1Url' => [ 'type' => 'String' ],
            'heroBtn2Text' => [ 'type' => 'String' ],
            'heroBtn2Url' => [ 'type' => 'String' ],
            'heroImage' => [ 'type' => 'String' ],
            'heroShowReviews' => [ 'type' => 'Boolean' ],
            'heroReview1Rating' => [ 'type' => 'String' ],
            'heroReview1Count' => [ 'type' => 'String' ],
            'heroReview2Rating' => [ 'type' => 'String' ],
            'heroReview2Count' => [ 'type' => 'String' ],

            // Section 2
            'sec2Title' => [ 'type' => 'String' ],
            'sec2Content' => [ 'type' => [ 'list_of' => 'LandingContentBlock' ] ],
            'sec2Tags' => [ 'type' => [ 'list_of' => 'LandingTag' ] ],

            // Section 3
            'sec3Header' => [ 'type' => 'String' ],
            'sec3Title'  => [ 'type' => 'String' ],
            'sec3Tabs'   => [ 'type' => [ 'list_of' => 'LandingTab' ] ],

            // Section 4
            'sec4Header' => [ 'type' => 'String' ],
            'sec4Cards'  => [ 'type' => [ 'list_of' => 'LandingIconCard' ] ],

            // Section 5
            'sec5Title' => [ 'type' => 'String' ],
            'sec5Faqs'  => [ 'type' => [ 'list_of' => 'LandingFaq' ] ],

            // Section 6
            'sec6Title' => [ 'type' => 'String' ],
            'sec6Desc'  => [ 'type' => 'String' ],
            'sec6Cards' => [ 'type' => [ 'list_of' => 'LandingPricingCard' ] ],

            // Section 8 (Section 7 is Blog related)
            'sec7Title' => [ 'type' => 'String' ],
            'sec7Desc' => [ 'type' => 'String' ],
            'sec7ReadMoreText' => [ 'type' => 'String' ],
            'sec7ReadMoreUrl' => [ 'type' => 'String' ],
            'sec7CategoryId' => [ 'type' => 'Integer' ], 

            // Section 8 (Subscribe)
            'sec8Title' => [ 'type' => 'String' ],
            'sec8Placeholder' => [ 'type' => 'String' ],
            'sec8BtnText' => [ 'type' => 'String' ],
            'subscribeNonce' => [ 'type' => 'String' ],
        ],
    ] );

    register_graphql_field( 'Page', 'landingData', [
        'type' => 'LandingPageData',
        'description' => 'Landing Page custom fields data',
        'resolve' => function( $post ) {
            $id = $post->ID;
            
            // Helper to get meta
            $get = function($k) use ($id) { return carbon_get_post_meta($id, $k); };

            // Process Sec 2 Content
            $raw_content = $get('sec2_content');
            $sec2_content = [];
            if(is_array($raw_content)) {
                foreach($raw_content as $block) {
                    $b = [
                        'type' => $block['_type'],
                        'content' => isset($block['content']) ? $block['content'] : '',
                        'text' => isset($block['text']) ? $block['text'] : '',
                        'author' => isset($block['author']) ? $block['author'] : '',
                        'image' => isset($block['image']) ? $block['image'] : '',
                        'caption' => isset($block['caption']) ? $block['caption'] : '',
                        'items' => '',
                    ];
                    if($block['_type'] === 'list' && !empty($block['items'])) {
                        $b['items'] = json_encode($block['items']); 
                    }
                    $sec2_content[] = $b;
                }
            }

            // Process Sec 2 Tags
            $raw_tags = $get('sec2_tags');
            $sec2_tags = [];
            if(is_array($raw_tags)) {
                foreach($raw_tags as $t) {
                    $sec2_tags[] = [ 'label' => $t['tag_label'], 'url' => $t['tag_url'] ];
                }
            }

            // Process Sec 3 Tabs
            $raw_tabs = $get('sec3_tabs');
            $sec3_tabs = [];
            if(is_array($raw_tabs)) {
                foreach($raw_tabs as $t) {
                    $sec3_tabs[] = [
                        'title' => $t['tab_title'],
                        'desc' => $t['tab_desc'],
                        'icon' => $t['tab_icon'],
                        'image' => $t['tab_image'],
                    ];
                }
            }

            // Process Sec 4 Cards
            $raw_sec4 = $get('sec4_cards');
            $sec4_cards = [];
            if(is_array($raw_sec4)) {
                foreach($raw_sec4 as $c) {
                    $sec4_cards[] = [
                        'icon' => $c['card_icon'],
                        'title' => $c['card_title'],
                        'desc' => $c['card_desc'],
                    ];
                }
            }

            // Process Sec 5 FAQs
            $raw_faqs = $get('sec5_faqs');
            $sec5_faqs = [];
            if(is_array($raw_faqs)) {
                foreach($raw_faqs as $f) {
                    $sec5_faqs[] = [
                        'icon' => $f['faq_icon'],
                        'question' => $f['faq_question'],
                        'answer' => $f['faq_answer'],
                    ];
                }
            }

            // Process Sec 6 Pricing
            $raw_pricing = $get('sec6_cards');
            $sec6_cards = [];
            if(is_array($raw_pricing)) {
                foreach($raw_pricing as $c) {
                    $feats = [];
                    if(!empty($c['card_features'])) {
                        foreach($c['card_features'] as $ft) $feats[] = ['text' => $ft['feature_text']];
                    }
                    $sec6_cards[] = [
                        'title' => $c['card_title'],
                        'priceMonthly' => $c['card_price_monthly'],
                        'priceAnnual' => $c['card_price_annually'],
                        'desc' => $c['card_desc'],
                        'features' => $feats,
                        'btnText' => $c['button_text'],
                        'btnUrl' => $c['button_url'],
                        'isPopular' => $c['is_popular'] ? true : false,
                    ];
                }
            }

            // Process Sec 7 Category
            $sec7_cat = $get('sec7_category');
            $sec7_cat_id = 0;
            if(!empty($sec7_cat) && isset($sec7_cat[0]['id'])) {
                $sec7_cat_id = $sec7_cat[0]['id'];
            }

            return [
                'heroTitle' => $get('hero_title'),
                'heroDescription' => $get('hero_description'),
                'heroBtn1Text' => $get('hero_btn_1_text'),
                'heroBtn1Url' => $get('hero_btn_1_url'),
                'heroBtn2Text' => $get('hero_btn_2_text'),
                'heroBtn2Url' => $get('hero_btn_2_url'),
                'heroImage' => $get('hero_image'),
                'heroShowReviews' => $get('hero_show_reviews') === 'yes',
                'heroReview1Rating' => $get('hero_reviews_rating_1'),
                'heroReview1Count' => $get('hero_reviews_count_1'),
                'heroReview2Rating' => $get('hero_reviews_rating_2'),
                'heroReview2Count' => $get('hero_reviews_count_2'),

                'sec2Title' => $get('sec2_title'),
                'sec2Content' => $sec2_content,
                'sec2Tags' => $sec2_tags,

                'sec3Header' => $get('sec3_header'),
                'sec3Title' => $get('sec3_title'),
                'sec3Tabs' => $sec3_tabs,

                'sec4Header' => $get('sec4_header'),
                'sec4Cards' => $sec4_cards,

                'sec5Title' => $get('sec5_title'),
                'sec5Faqs' => $sec5_faqs,

                'sec6Title' => $get('sec6_title'),
                'sec6Desc' => $get('sec6_desc'),
                'sec6Cards' => $sec6_cards,

                'sec7Title' => $get('sec7_title'),
                'sec7Desc' => $get('sec7_desc'),
                'sec7ReadMoreText' => $get('sec7_read_more_text'),
                'sec7ReadMoreUrl' => $get('sec7_read_more_url'),
                'sec7CategoryId' => $sec7_cat_id,

                'sec8Title' => $get('sec8_title'),
                'sec8Placeholder' => $get('sec8_input_placeholder'),
                'sec8BtnText' => $get('sec8_btn_text'),
                'subscribeNonce' => wp_create_nonce('wp_custom_subscribe_nonce'),
            ];
        }
    ] );
}
