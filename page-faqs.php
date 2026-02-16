<?php

/**
 * custom template file for Rock Stars theme.
 *
 * Template Name: Page FAQs Template Custom
 *
 * @package Rock_Star
 */



get_header(); ?>


<div class="wp-faq-container">
    <h1 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-5xl leading-tight sm:leading-tight md:leading-tight mb-5">
        <?php esc_html_e( 'Frequently Asked Questions', 'rock-stars' ); ?>
    </h1>

    <?php
    $rock_stars_faq_items = carbon_get_theme_option( 'faq_items' );
    if ( ! empty( $rock_stars_faq_items ) ) :
        foreach ( $rock_stars_faq_items as $rock_stars_faq_index => $rock_stars_faq_item ) :
            $rock_stars_faq_question   = ! empty( $rock_stars_faq_item['question'] ) ? esc_html( $rock_stars_faq_item['question'] ) : '';
            $rock_stars_faq_raw_answer = ! empty( $rock_stars_faq_item['answer'] ) ? apply_filters( 'the_content', $rock_stars_faq_item['answer'] ) : '';
            $rock_stars_faq_answer     = wp_faq_add_class_to_paragraphs( $rock_stars_faq_raw_answer );
            ?>
            <div class="wp-faq-item">
                <button class="wp-faq-button" onclick="wpToggleFaq(<?php echo esc_attr( $rock_stars_faq_index ); ?>)">
                    <h3 class="wp-faq-title text-black dark:text-white text-xl sm:text-2xl block hover:text-primary dark:hover:text-primary">
                        <?php echo $rock_stars_faq_question; ?>
                    </h3>
                    <svg class="wp-faq-arrow wp-faq-transition" id="wp-arrow-<?php echo esc_attr( $rock_stars_faq_index ); ?>" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="wp-faq-answer" id="wp-answer-<?php echo esc_attr( $rock_stars_faq_index ); ?>">
                    <div class="wp-faq-content">
                        <?php echo wp_kses_post( $rock_stars_faq_answer ); ?>
                    </div>
                </div>
            </div>
        <?php endforeach;
    endif;
    ?>
</div>


<?php get_footer(); ?>