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
        Frequently Asked Questions
    </h1>

    <?php
    $faq_items = carbon_get_theme_option('faq_items');
    if (!empty($faq_items)):
        foreach ($faq_items as $index => $item):
            $question = esc_html($item['question']);
            $raw_answer = apply_filters('the_content', $item['answer']);
            $answer = wp_faq_add_class_to_paragraphs($raw_answer);
            ?>
            <div class="wp-faq-item">
                <button class="wp-faq-button" onclick="wpToggleFaq(<?php echo $index; ?>)">
                    <h3 class="wp-faq-title text-black dark:text-white text-xl sm:text-2xl block hover:text-primary dark:hover:text-primary">
                        <?php echo $question; ?>
                    </h3>
                    <svg class="wp-faq-arrow wp-faq-transition" id="wp-arrow-<?php echo $index; ?>" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="wp-faq-answer" id="wp-answer-<?php echo $index; ?>">
                    <div class="wp-faq-content">
                        <?php echo $answer; ?>
                    </div>
                </div>
            </div>
        <?php endforeach;
    endif;
    ?>
</div>


<?php get_footer(); ?>