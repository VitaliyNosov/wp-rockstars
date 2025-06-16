<?php
/**
 * Template Name: Template Page Posts
 */
get_header();
?>

<!-- ====== Page Title Section Start -->
<section class="relative z-10 pt-[150px] overflow-hidden">
    <div class="container">
        <div class="flex flex-wrap items-center mx-[-16px]">
            <div class="w-full md:w-8/12 lg:w-7/12 px-4">
                <div class="max-w-[570px] mb-12 md:mb-0">
                    <h1 class="font-bold text-black dark:text-white text-2xl sm:text-3xl mb-5">Blog Grids</h1>
                    <p class="font-medium text-base text-body-color leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. In varius eros eget sapien consectetur ultrices.
                        Ut quis dapibus libero.
                    </p>
                </div>
            </div>
            <div class="w-full md:w-4/12 lg:w-5/12 px-4">
                <div class="text-end">
                    <ul class="flex items-center md:justify-end">
                        <li class="flex items-center">
                            <a href="index.html" class="font-medium text-base text-body-color pr-1 hover:text-primary">Home</a>
                            <span class="block w-2 h-2 border-t-2 border-r-2 border-body-color rotate-45 mr-3"></span>
                        </li>
                        <li class="font-medium text-base text-primary">Blog Grids</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div>
        <span class="absolute top-0 left-0 z-[-1]">
            <svg width="287" height="254" viewBox="0 0 287 254" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.1" d="M286.5 0.5L-14.5 254.5V69.5L286.5 0.5Z" fill="url(#paint0_linear_111:578)" />
                <defs>
                    <linearGradient id="paint0_linear_111:578" x1="-40.5" y1="117" x2="301.926" y2="-97.1485" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4A6CF7" />
                        <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>
        </span>
        <span class="absolute right-0 top-0 z-[-1]">
            <svg width="628" height="258" viewBox="0 0 628 258" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.1" d="M669.125 257.002L345.875 31.9983L524.571 -15.8832L669.125 257.002Z" fill="url(#paint0_linear_0:1)" />
                <path opacity="0.1" d="M0.0716344 182.78L101.988 -15.0769L142.154 81.4093L0.0716344 182.78Z" fill="url(#paint1_linear_0:1)" />
                <defs>
                    <linearGradient id="paint0_linear_0:1" x1="644" y1="221" x2="429.946" y2="37.0429" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4A6CF7" />
                        <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="paint1_linear_0:1" x1="18.3648" y1="166.016" x2="105.377" y2="32.3398" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4A6CF7" />
                        <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>
        </span>
    </div>
</section>
<!-- ====== Page Title Section End -->

<section class="pt-[120px] pb-[120px]">
    <div class="container">
        <div class="flex flex-wrap mx-[-16px] justify-center">
            <?php
            // Принудительно устанавливаем paged = 1 для главной страницы
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            
            // Если это первая страница без параметра paged, устанавливаем явно
            if (!get_query_var('paged') && !is_paged()) {
                $paged = 1;
            }

            $query = new WP_Query([
                'post_type' => 'post',
                'posts_per_page' => 9,
                'paged' => $paged,
                'ignore_sticky_posts' => true,
            ]);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $categories = get_the_category();
                    $category_name = !empty($categories) ? esc_html($categories[0]->name) : '';
                    $author_id = get_the_author_meta('ID');
            ?>
                    <div class="w-full md:w-2/3 lg:w-1/2 xl:w-1/3 px-4">
                        <div class="relative bg-white dark:bg-dark shadow-one rounded-md overflow-hidden mb-10 wow fadeInUp" data-wow-delay=".1s">
                            <a href="<?php the_permalink(); ?>" class="w-full block relative">
                                <?php if ($category_name) : ?>
                                    <span class="absolute top-6 right-6 bg-primary rounded-full inline-flex items-center justify-center py-2 px-4 font-semibold text-sm text-white">
                                        <?php echo $category_name; ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (has_post_thumbnail()) : ?>
                                    <img src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title_attribute(); ?>" class="w-full" />
                                <?php endif; ?>
                            </a>
                            <div class="p-6 sm:p-8 md:py-8 md:px-6 lg:p-8 xl:py-8 xl:px-5 2xl:p-8">
                                <h3>
                                    <a href="<?php the_permalink(); ?>" class="font-bold text-black dark:text-white text-xl sm:text-2xl block mb-4 hover:text-primary dark:hover:text-primary">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <p class="text-base text-body-color font-medium pb-6 mb-6 border-b border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                                    <?php echo get_the_excerpt(); ?>
                                </p>
                                <div class="flex items-center">
                                    <div class="flex items-center pr-5 mr-5 xl:pr-3 2xl:pr-5 xl:mr-3 2xl:mr-5 border-r border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                                        <div class="max-w-[40px] w-full h-[40px] rounded-full overflow-hidden mr-4">
                                            <?php echo get_avatar($author_id, 40); ?>
                                        </div>
                                        <div class="w-full">
                                            <h4 class="text-sm font-medium text-dark dark:text-white mb-1">
                                                By
                                                <a href="<?php echo get_author_posts_url($author_id); ?>" class="text-dark dark:text-white hover:text-primary dark:hover:text-primary">
                                                    <?php the_author(); ?>
                                                </a>
                                            </h4>
                                            <p class="text-xs text-body-color"><?php echo get_the_author_meta('description'); ?></p>
                                        </div>
                                    </div>
                                    <div class="inline-block">
                                        <h4 class="text-sm font-medium text-dark dark:text-white mb-1">Date</h4>
                                        <p class="text-xs text-body-color"><?php echo get_the_date(); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endwhile;

                // Navigation section
                $big = 999999999; // уникальное число для замены

                $links = paginate_links([
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, $paged),
                    'total' => $query->max_num_pages,
                    'mid_size' => 2,
                    'prev_text' => 'Prev',
                    'next_text' => 'Next',
                    'type' => 'array',
                    'show_all' => false,
                    'end_size' => 1,
                ]);

                if (is_array($links)) : ?>
                    <div class="w-full">
                        <ul class="flex items-center pt-8 justify-center">
                            <?php foreach ($links as $link) :
                                $is_active = strpos($link, 'current') !== false;
                                $is_disabled = strpos($link, 'dots') !== false;

                                if ($is_disabled) : ?>
                                    <li class="mx-1">
                                        <span class="flex items-center justify-center rounded-md bg-body-color bg-opacity-[15%] text-body-color px-4 text-sm min-w-[36px] h-9 cursor-not-allowed">...</span>
                                    </li>
                                <?php else : ?>
                                    <li class="mx-1">
                                        <?php
                                        $link = str_replace(
                                            'page-numbers',
                                            'flex items-center justify-center rounded-md bg-body-color bg-opacity-[15%] hover:bg-primary hover:bg-opacity-100 transition hover:text-white text-body-color px-4 text-sm min-w-[36px] h-9' . ($is_active ? ' bg-primary text-white' : ''),
                                            $link
                                        );
                                        echo $link;
                                        ?>
                                    </li>
                                <?php endif;
                            endforeach; ?>
                        </ul>
                    </div>
                <?php endif;

                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>
<!-- ====== Blog Section End -->

<?php get_footer(); ?>