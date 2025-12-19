<?php
/**
 * Template Name: Landing Page
 *
 * @package Rock_Star
 */

get_header(); ?>

<div class="landing-template-wrapper">

<div class="landing-margin-class">
    <!-- section one -->
    <!-- Hero -->
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Grid -->
      <div class="grid md:grid-cols-2 gap-4 md:gap-8 xl:gap-20 md:items-center">
        <div>
          <h1 class="block text-3xl font-bold text-gray-800 sm:text-4xl lg:text-6xl lg:leading-tight dark:text-white">
            <?php 
            $hero_title = carbon_get_the_post_meta( 'hero_title' );
            if ( $hero_title ) {
                $words = explode( ' ', $hero_title );
                if ( count( $words ) > 0 ) {
                    $last_word = array_pop( $words );
                    // Output all words except the last one, escaped
                    echo implode( ' ', array_map( 'esc_html', $words ) );
                    // Output space if there are preceding words
                    if ( count( $words ) > 0 ) {
                        echo ' ';
                    }
                    // Output the last word wrapped in span
                    echo '<span class="text-blue-600">' . esc_html( $last_word ) . '</span>';
                }
            }
            ?>
          </h1>
          <p class="mt-3 text-lg dark:text-white dark:text-neutral-400">
            <?php echo esc_html( carbon_get_the_post_meta( 'hero_description' ) ); ?>
          </p>
    
          <!-- Buttons -->
          <div class="mt-7 grid gap-3 w-full sm:inline-flex">
            <?php if ( $btn1_text = carbon_get_the_post_meta( 'hero_btn_1_text' ) ) : ?>
                <a class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="<?php echo esc_url( carbon_get_the_post_meta( 'hero_btn_1_url' ) ); ?>">
                  <?php echo esc_html( $btn1_text ); ?>
                  <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            <?php endif; ?>
            
            <?php if ( $btn2_text = carbon_get_the_post_meta( 'hero_btn_2_text' ) ) : ?>
                <a class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-gray-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" href="<?php echo esc_url( carbon_get_the_post_meta( 'hero_btn_2_url' ) ); ?>">
                  <?php echo esc_html( $btn2_text ); ?>
                </a>
            <?php endif; ?>
          </div>
          <!-- End Buttons -->
    
          <?php if ( carbon_get_the_post_meta( 'hero_show_reviews' ) ) : ?>
          <!-- Review -->
          <div class="mt-6 lg:mt-10 grid grid-cols-2 gap-x-5">
            <!-- Review -->
            <div class="py-5">
              <div class="flex gap-x-1">
                <?php for($i=0; $i<5; $i++): ?>
                <svg class="size-4 dark:text-white dark:text-neutral-200" width="51" height="51" viewBox="0 0 51 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M27.0352 1.6307L33.9181 16.3633C34.2173 16.6768 34.5166 16.9903 34.8158 16.9903L50.0779 19.1845C50.9757 19.1845 51.275 20.4383 50.6764 21.0652L39.604 32.3498C39.3047 32.6632 39.3047 32.9767 39.3047 33.2901L41.998 49.2766C42.2973 50.217 41.1002 50.8439 40.5017 50.5304L26.4367 43.3208C26.1375 43.3208 25.8382 43.3208 25.539 43.3208L11.7732 50.8439C10.8754 51.1573 9.97763 50.5304 10.2769 49.59L12.9702 33.6036C12.9702 33.2901 12.9702 32.9767 12.671 32.6632L1.29923 21.0652C0.700724 20.4383 0.999979 19.4979 1.89775 19.4979L17.1598 17.3037C17.459 17.3037 17.7583 16.9903 18.0575 16.6768L24.9404 1.6307C25.539 0.69032 26.736 0.69032 27.0352 1.6307Z" fill="currentColor"/>
                </svg>
                <?php endfor; ?>
              </div>
    
              <p class="mt-3 text-sm dark:text-white dark:text-neutral-200">
                <span class="font-bold"><?php echo esc_html( carbon_get_the_post_meta( 'hero_reviews_rating_1' ) ); ?></span> /5 - from <?php echo esc_html( carbon_get_the_post_meta( 'hero_reviews_count_1' ) ); ?>
              </p>
    
              <div class="mt-5">
                 <!-- Placeholder for Star Logo 1 - Keeping SVG for now as it seems hardcoded logo -->
                 <svg class="h-auto w-16 dark:text-white dark:text-white" width="80" height="27" viewBox="0 0 80 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20.558 9.74046H11.576V12.3752H17.9632C17.6438 16.0878 14.5301 17.7245 11.6159 17.7245C7.86341 17.7245 4.58995 14.7704 4.58995 10.6586C4.58995 6.62669 7.70373 3.51291 11.6159 3.51291C14.6498 3.51291 16.4063 5.42908 16.4063 5.42908L18.2426 3.51291C18.2426 3.51291 15.8474 0.878184 11.4961 0.878184C5.94724 0.838264 1.67578 5.50892 1.67578 10.5788C1.67578 15.5289 5.70772 20.3592 11.6558 20.3592C16.8854 20.3592 20.7177 16.8063 20.7177 11.4969C20.7177 10.3792 20.558 9.74046 20.558 9.74046Z" fill="currentColor"/>
                  <path d="M27.8621 7.78442C24.1894 7.78442 21.5547 10.6587 21.5547 14.012C21.5547 17.4451 24.1096 20.3593 27.9419 20.3593C31.415 20.3593 34.2094 17.7645 34.2094 14.0918C34.1695 9.94011 30.896 7.78442 27.8621 7.78442ZM27.902 10.2994C29.6984 10.2994 31.415 11.7764 31.415 14.0918C31.415 16.4072 29.7383 17.8842 27.902 17.8842C25.906 17.8842 24.3491 16.2874 24.3491 14.0519C24.3092 11.8962 25.8661 10.2994 27.902 10.2994Z" fill="currentColor"/>
                  <path d="M41.5964 7.78442C37.9238 7.78442 35.2891 10.6587 35.2891 14.012C35.2891 17.4451 37.844 20.3593 41.6763 20.3593C45.1493 20.3593 47.9438 17.7645 47.9438 14.0918C47.9038 9.94011 44.6304 7.78442 41.5964 7.78442ZM41.6364 10.2994C43.4328 10.2994 45.1493 11.7764 45.1493 14.0918C45.1493 16.4072 43.4727 17.8842 41.6364 17.8842C39.6404 17.8842 38.0835 16.2874 38.0835 14.0519C38.0436 11.8962 39.6004 10.2994 41.6364 10.2994Z" fill="currentColor"/>
                  <path d="M55.0475 7.82434C51.6543 7.82434 49.0195 10.7784 49.0195 14.0918C49.0195 17.8443 52.0934 20.3992 54.9676 20.3992C56.764 20.3992 57.6822 19.7205 58.4407 18.8822V20.1198C58.4407 22.2754 57.1233 23.5928 55.1273 23.5928C53.2111 23.5928 52.2531 22.1557 51.8938 21.3573L49.4587 22.3553C50.297 24.1517 52.0135 26.0279 55.0874 26.0279C58.4407 26.0279 60.9956 23.9122 60.9956 19.481V8.18362H58.3608V9.26147C57.6423 8.38322 56.5245 7.82434 55.0475 7.82434ZM55.287 10.2994C56.9237 10.2994 58.6403 11.7365 58.6403 14.1317C58.6403 16.6068 56.9636 17.9241 55.2471 17.9241C53.4507 17.9241 51.774 16.4471 51.774 14.1716C51.8139 11.6966 53.5305 10.2994 55.287 10.2994Z" fill="currentColor"/>
                  <path d="M72.8136 7.78442C69.62 7.78442 66.9453 10.2994 66.9453 14.0519C66.9453 18.004 69.9393 20.3593 73.093 20.3593C75.7278 20.3593 77.4044 18.8822 78.3625 17.6048L76.1669 16.1277C75.608 17.006 74.6499 17.8443 73.093 17.8443C71.3365 17.8443 70.5381 16.8862 70.0192 15.9281L78.4423 12.4152L78.0032 11.3772C77.1649 9.46107 75.2886 7.78442 72.8136 7.78442ZM72.8934 10.2196C74.0511 10.2196 74.8495 10.8184 75.2487 11.5768L69.6599 13.9321C69.3405 12.0958 71.097 10.2196 72.8934 10.2196Z" fill="currentColor"/>
                  <path d="M62.9531 19.9999H65.7076V1.47693H62.9531V19.9999Z" fill="currentColor"/>
                 </svg>
              </div>
            </div>
            <!-- End Review -->
    
            <!-- Review -->
            <div class="py-5">
              <div class="flex gap-x-1">
                <?php for($i=0; $i<5; $i++): ?>
                <svg class="size-4 dark:text-white dark:text-neutral-200" width="51" height="51" viewBox="0 0 51 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M27.0352 1.6307L33.9181 16.3633C34.2173 16.6768 34.5166 16.9903 34.8158 16.9903L50.0779 19.1845C50.9757 19.1845 51.275 20.4383 50.6764 21.0652L39.604 32.3498C39.3047 32.6632 39.3047 32.9767 39.3047 33.2901L41.998 49.2766C42.2973 50.217 41.1002 50.8439 40.5017 50.5304L26.4367 43.3208C26.1375 43.3208 25.8382 43.3208 25.539 43.3208L11.7732 50.8439C10.8754 51.1573 9.97763 50.5304 10.2769 49.59L12.9702 33.6036C12.9702 33.2901 12.9702 32.9767 12.671 32.6632L1.29923 21.0652C0.700724 20.4383 0.999979 19.4979 1.89775 19.4979L17.1598 17.3037C17.459 17.3037 17.7583 16.9903 18.0575 16.6768L24.9404 1.6307C25.539 0.69032 26.736 0.69032 27.0352 1.6307Z" fill="currentColor"/>
                </svg>
                <?php endfor; ?>
              </div>
    
              <p class="mt-3 text-sm dark:text-white dark:text-neutral-200">
                <span class="font-bold"><?php echo esc_html( carbon_get_the_post_meta( 'hero_reviews_rating_2' ) ); ?></span> /5 - from <?php echo esc_html( carbon_get_the_post_meta( 'hero_reviews_count_2' ) ); ?>
              </p>
    
              <div class="mt-5">
                <!-- Placeholder for Star Logo 2 -->
                <svg class="h-auto w-16 dark:text-white dark:text-white" width="110" height="28" viewBox="0 0 110 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M66.6601 8.35107C64.8995 8.35107 63.5167 8.72875 62.1331 9.48265C62.1331 5.4582 62.1331 1.81143 62.2594 0.554199L53.8321 2.06273V2.81736L54.7124 2.94301C55.8433 3.19431 56.2224 3.82257 56.4715 5.33255C56.725 8.35107 56.5979 24.4496 56.4715 27.0912C58.7354 27.5945 61.1257 27.9722 63.5159 27.9722C70.1819 27.9722 74.2064 23.8213 74.2064 17.281C74.2064 12.1249 70.9366 8.35107 66.6601 8.35107ZM63.7672 26.5878C63.2639 26.5878 62.6342 26.5878 62.258 26.4629C62.1316 24.7023 62.0067 17.281 62.1316 10.7413C62.8862 10.4893 63.3888 10.3637 64.0185 10.3637C66.7872 10.3637 68.2965 13.6335 68.2965 17.6572C68.2957 22.6898 66.4088 26.5878 63.7672 26.5878ZM22.1363 1.0568H0V2.18838L1.25796 2.31403C2.89214 2.56533 3.52184 3.57127 3.77242 5.9608C4.15082 10.4886 4.02445 18.6646 3.77242 22.5619C3.52112 24.9522 2.89287 26.0845 1.25796 26.2087L0 26.4615V27.4674H14.2123V26.4615L12.703 26.2087C11.0681 26.0838 10.4392 24.9522 10.1879 22.5619C10.0615 20.9263 9.93583 18.2847 9.93583 15.0156L12.9543 15.1413C14.8413 15.1413 15.7208 16.6505 16.0985 18.7881H17.2308V9.86106H16.0985C15.7201 11.9993 14.8413 13.5078 12.9543 13.5078L9.93655 13.6342C9.93655 9.35773 10.0622 5.33328 10.1886 2.94374H14.59C17.9869 2.94374 19.7475 5.08125 21.0047 8.85513L22.2626 8.47745L22.1363 1.0568Z" fill="currentColor"/>
                  <path d="M29.3053 8.09998C35.5944 8.09998 38.7385 12.3764 38.7385 18.0358C38.7385 23.4439 35.2167 27.9731 28.9276 27.9731C22.6393 27.9731 19.4951 23.6959 19.4951 18.0358C19.4951 12.6277 23.0162 8.09998 29.3053 8.09998ZM28.9276 9.35793C26.1604 9.35793 25.4058 13.1311 25.4058 18.0358C25.4058 22.8149 26.6637 26.7137 29.1796 26.7137C32.0703 26.7137 32.8264 22.9405 32.8264 18.0358C32.8264 13.2567 31.5699 9.35793 28.9276 9.35793ZM75.8403 18.1622C75.8403 13.0054 79.1101 8.09998 85.5248 8.09998C90.8057 8.09998 93.3224 11.9995 93.3224 17.1555H81.6253C81.4989 21.8089 83.7628 25.2051 88.2913 25.2051C90.3038 25.2051 91.3098 24.7033 92.5685 23.8223L93.0703 24.4505C91.8124 26.2111 89.0459 27.9731 85.5248 27.9731C79.8647 27.9724 75.8403 23.9479 75.8403 18.1622ZM81.6253 15.7726L87.5366 15.6463C87.5366 13.1311 87.159 9.35793 85.0214 9.35793C82.8839 9.35793 81.7502 12.8791 81.6253 15.7726ZM108.291 9.10663C106.782 8.47693 104.77 8.09998 102.506 8.09998C97.8538 8.09998 94.9594 10.8665 94.9594 14.137C94.9594 17.4075 97.0955 18.7904 100.118 19.7971C103.261 20.9279 104.142 21.8089 104.142 23.3182C104.142 24.8275 103.01 26.2103 100.997 26.2103C98.6084 26.2103 96.8464 24.8275 95.4635 21.0536L94.5825 21.3063L94.7089 26.84C96.2181 27.4683 98.9846 27.9724 101.375 27.9724C106.28 27.9724 109.425 25.4557 109.425 21.5576C109.425 18.9161 108.041 17.4075 104.771 16.1489C101.249 14.766 99.992 13.8857 99.992 12.2501C99.992 10.6152 101.126 9.48286 102.635 9.48286C104.897 9.48286 106.407 10.8665 107.54 14.2627L108.42 14.0114L108.291 9.10663ZM55.0883 8.6033C52.9508 7.3468 49.1769 7.97433 47.1651 12.5028L47.29 8.1007L38.8642 9.73561V10.4902L39.7444 10.6159C40.8775 10.7423 41.3794 11.3705 41.5057 13.0062C41.757 16.0247 41.6314 21.3078 41.5057 23.9486C41.3794 25.4564 40.8775 26.2111 39.7444 26.3374L38.8642 26.4638V27.4697H50.5606V26.4638L49.0513 26.3374C47.7941 26.2111 47.4164 25.4564 47.29 23.9486C47.0387 21.5584 47.0387 16.7793 47.1651 13.7608C47.7933 12.8798 50.5606 12.1259 53.0757 13.7608L55.0883 8.6033Z" fill="currentColor"/>
                </svg>
              </div>
            </div>
            <!-- End Review -->
          </div>
          <!-- End Review -->
          <?php endif; ?>
    
        </div>
        <!-- End Col -->
    
        <div class="relative ms-4">
          <?php if ( $hero_img_url = carbon_get_the_post_meta( 'hero_image' ) ) : ?>
            <img class="w-full rounded-md" src="<?php echo esc_url( $hero_img_url ); ?>" alt="Hero Image">
          <?php else: ?>
            <img class="w-full rounded-md" src="https://images.unsplash.com/photo-1665686377065-08ba896d16fd?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=700&h=800&q=80" alt="Hero Image">
          <?php endif; ?>
          <div class="absolute inset-0 -z-1 bg-linear-to-tr from-gray-200 via-white/0 to-white/0 size-full rounded-md mt-4 -mb-4 me-4 -ms-4 lg:mt-6 lg:-mb-6 lg:me-6 lg:-ms-6 dark:from-neutral-800 dark:via-neutral-900/0 dark:to-neutral-900/0"></div>
    
          <!-- SVG-->
          <div class="absolute bottom-0 start-0">
            <svg class="w-2/3 ms-auto h-auto text-white dark:text-neutral-900" width="630" height="451" viewBox="0 0 630 451" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="531" y="352" width="99" height="99" fill="currentColor"/>
              <rect x="140" y="352" width="106" height="99" fill="currentColor"/>
              <rect x="482" y="402" width="64" height="49" fill="currentColor"/>
              <rect x="433" y="402" width="63" height="49" fill="currentColor"/>
              <rect x="384" y="352" width="49" height="50" fill="currentColor"/>
              <rect x="531" y="328" width="50" height="50" fill="currentColor"/>
              <rect x="99" y="303" width="49" height="58" fill="currentColor"/>
              <rect x="99" y="352" width="49" height="50" fill="currentColor"/>
              <rect x="99" y="392" width="49" height="59" fill="currentColor"/>
              <rect x="44" y="402" width="66" height="49" fill="currentColor"/>
              <rect x="234" y="402" width="62" height="49" fill="currentColor"/>
              <rect x="334" y="303" width="50" height="49" fill="currentColor"/>
              <rect x="581" width="49" height="49" fill="currentColor"/>
              <rect x="581" width="49" height="64" fill="currentColor"/>
              <rect x="482" y="123" width="49" height="49" fill="currentColor"/>
              <rect x="507" y="124" width="49" height="24" fill="currentColor"/>
              <rect x="531" y="49" width="99" height="99" fill="currentColor"/>
            </svg>
          </div>
          <!-- End SVG-->
        </div>
        <!-- End Col -->
      </div>
      <!-- End Grid -->
    </div>
    <!-- End Hero -->
    <!-- End Hero -->

    <!-- section-two -->
    <?php if ( $sec2_title = carbon_get_the_post_meta( 'sec2_title' ) ): ?>
    <div class="header-landing-section section-margin-top">
            <h2 class="text-center text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
                <?php echo esc_html( $sec2_title ); ?> 
            </h2>
    </div>
    <?php endif; ?>

    <!-- Blog Article -->
    <div class="max-w-[75rem] px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
      <div class="max-w-1xl">
       
        <!-- Content -->
        <div class="space-y-5 md:space-y-8">
            
          <?php 
          $content_blocks = carbon_get_the_post_meta( 'sec2_content' );
          if ( ! empty( $content_blocks ) ):
            foreach ( $content_blocks as $block ):
                switch ( $block['_type'] ):
                    case 'paragraph':
                        ?>
                        <p class="mt-3 text-lg dark:text-white dark:text-neutral-400"><?php echo esc_html( $block['content'] ); ?></p>
                        <?php
                        break;
                    
                    case 'heading':
                        ?>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-semibold dark:text-white"><?php echo esc_html( $block['text'] ); ?></h3>
                        </div>
                        <?php
                        break;

                    case 'quote':
                        ?>
                        <blockquote class="text-center p-4 sm:px-7">
                            <p class="text-xl font-medium text-gray-800 md:text-2xl md:leading-normal xl:text-2xl xl:leading-normal dark:text-neutral-200">
                              <?php echo esc_html( $block['text'] ); ?>
                            </p>
                            <?php if ( ! empty( $block['author'] ) ): ?>
                            <p class="mt-3 text-lg dark:text-white dark:text-neutral-400">
                              <?php echo esc_html( $block['author'] ); ?>
                            </p>
                            <?php endif; ?>
                        </blockquote>
                        <?php
                        break;

                    case 'image':
                        ?>
                        <?php if ( ! empty( $block['image'] ) ): ?>
                        <figure>
                            <img class="w-full object-cover rounded-xl" src="<?php echo esc_url( $block['image'] ); ?>" alt="Blog Image">
                            <?php if ( ! empty( $block['caption'] ) ): ?>
                            <figcaption class="mt-3 text-sm text-center text-gray-500 dark:text-neutral-500">
                              <?php echo esc_html( $block['caption'] ); ?>
                            </figcaption>
                            <?php endif; ?>
                        </figure>
                        <?php endif; ?>
                        <?php
                        break;

                    case 'list':
                        if ( ! empty( $block['items'] ) ):
                        ?>
                        <ul class="list-disc list-outside space-y-5 ps-5 text-lg dark:text-white dark:text-neutral-400">
                            <?php foreach ( $block['items'] as $item ): ?>
                            <li class="ps-2"><?php echo esc_html( $item['text'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                        endif;
                        break;

                endswitch;
            endforeach;
          endif;
          ?>

          <?php 
          $tags = carbon_get_the_post_meta( 'sec2_tags' );
          if ( ! empty( $tags ) ): 
          ?>
          <div>
            <?php foreach ( $tags as $tag ): ?>
            <a class="m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" href="<?php echo esc_url( $tag['tag_url'] ); ?>">
              <?php echo esc_html( $tag['tag_label'] ); ?>
            </a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

        </div>
        <!-- End Content -->
      </div>
    </div>
    <!-- End Blog Article -->

    <!-- section-three -->
    <?php if ( $sec3_header = carbon_get_the_post_meta( 'sec3_header' ) ): ?>
    <div class="header-landing-section">
        <h2 class="text-center text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
            <?php echo esc_html( $sec3_header ); ?>
          </h2>
    </div>
    <?php endif; ?>

    <!-- Features -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
      <div class="relative p-6 md:p-16">
        <!-- Grid -->
        <div class="relative z-10 lg:grid lg:grid-cols-12 lg:gap-16 lg:items-center">
          <div class="mb-10 lg:mb-0 lg:col-span-6 lg:col-start-8 lg:order-2">
            <h2 class="text-2xl dark:text-white font-bold sm:text-3xl dark:text-neutral-200">
              <?php echo esc_html( carbon_get_the_post_meta( 'sec3_title' ) ); ?>
            </h2>

            <!-- Tab Navs -->
            <nav class="grid gap-4 mt-5 md:mt-10" aria-label="Tabs" role="tablist" aria-orientation="vertical">
              <?php 
              $tabs = carbon_get_the_post_meta( 'sec3_tabs' );
              if ( ! empty( $tabs ) ):
                foreach ( $tabs as $index => $tab ):
                    $tab_id = $index + 1;
                    $is_active = ( $index === 0 );
                    $active_class = $is_active ? 'active' : '';
                    $aria_selected = $is_active ? 'true' : 'false';
              ?>
              <button type="button" class="hs-tab-active:bg-white hs-tab-active:shadow-md hs-tab-active:hover:border-transparent text-start hover:bg-gray-2001 focus:outline-hidden focus:bg-gray-2001 p-4 md:p-5 rounded-xl dark:hs-tab-active:bg-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 <?php echo $active_class; ?>" id="tabs-with-card-item-<?php echo $tab_id; ?>" aria-selected="<?php echo $aria_selected; ?>" data-hs-tab="#tabs-with-card-<?php echo $tab_id; ?>" aria-controls="tabs-with-card-<?php echo $tab_id; ?>" role="tab">
                <span class="flex gap-x-6">
                  <span class="shrink-0 mt-2 size-6 md:size-7 hs-tab-active:text-blue-600 dark:text-white dark:hs-tab-active:text-blue-500 dark:text-neutral-200 flex items-center justify-center">
                    <?php echo $tab['tab_icon']; // SVG Output ?>
                  </span>
                  <span class="grow">
                    <span class="block text-lg font-semibold hs-tab-active:text-blue-600 dark:text-white dark:hs-tab-active:text-blue-500 dark:text-neutral-200"><?php echo esc_html( $tab['tab_title'] ); ?></span>
                    <span class="block mt-1 dark:text-white dark:hs-tab-active:text-gray-200 dark:text-neutral-200"><?php echo esc_html( $tab['tab_desc'] ); ?></span>
                  </span>
                </span>
              </button>
              <?php endforeach; endif; ?>
            </nav>
            <!-- End Tab Navs -->
          </div>
          <!-- End Col -->

          <div class="lg:col-span-6">
            <div class="relative">
              <!-- Tab Content -->
              <div>
                <?php 
                if ( ! empty( $tabs ) ):
                    foreach ( $tabs as $index => $tab ):
                        $tab_id = $index + 1;
                        $hidden_class = ( $index !== 0 ) ? 'hidden' : '';
                ?>
                <div id="tabs-with-card-<?php echo $tab_id; ?>" class="<?php echo $hidden_class; ?>" role="tabpanel" aria-labelledby="tabs-with-card-item-<?php echo $tab_id; ?>">
                  <img class="shadow-xl shadow-gray-200 rounded-xl dark:shadow-gray-900/20" src="<?php echo esc_url( $tab['tab_image'] ); ?>" alt="<?php echo esc_attr( $tab['tab_title'] ); ?>">
                </div>
                <?php endforeach; endif; ?>
              </div>
              <!-- End Tab Content -->

              <!-- SVG Element -->
              <div class="hidden absolute top-0 end-0 translate-x-20 md:block lg:translate-x-20">
                <svg class="w-16 h-auto text-orange-500" width="121" height="135" viewBox="0 0 121 135" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5 16.4754C11.7688 27.4499 21.2452 57.3224 5 89.0164" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                  <path d="M33.6761 112.104C44.6984 98.1239 74.2618 57.6776 83.4821 5" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                  <path d="M50.5525 130C68.2064 127.495 110.731 117.541 116 78.0874" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                </svg>
              </div>
              <!-- End SVG Element -->
            </div>
          </div>
          <!-- End Col -->
        </div>
        <!-- End Grid -->

        <!-- Background Color -->
        <div class="absolute inset-0 grid grid-cols-12 size-full">
          <div class="col-span-full lg:col-span-7 lg:col-start-6 bg-gray-100 w-full h-5/6 rounded-xl sm:h-3/4 lg:h-full dark:bg-neutral-800"></div>
        </div>
        <!-- End Background Color -->
      </div>
    </div>
    <!-- End Features -->
    <!-- End Features -->

    <!-- section-four -->
    <?php if ( $sec4_header = carbon_get_the_post_meta( 'sec4_header' ) ): ?>
    <div class="header-landing-section">
        <h2 class="text-center text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">
            <?php echo esc_html( $sec4_header ); ?>
        </h2>
    </div>
    <?php endif; ?>

    <!-- Icon Blocks -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 items-center gap-6 md:gap-10">
        
        <?php 
        $cards = carbon_get_the_post_meta( 'sec4_cards' );
        if ( ! empty( $cards ) ):
            foreach ( $cards as $card ):
        ?>
        <!-- Card -->
        <div class="size-full bg-white shadow-lg rounded-lg p-5 dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700">
          <div class="flex items-center gap-x-4 mb-3">
            <div class="inline-flex justify-center items-center size-15.5 rounded-full border-4 border-blue-50 bg-blue-100 dark:border-blue-900 dark:bg-blue-800">
              <span class="shrink-0 size-6 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                  <?php echo $card['card_icon']; // SVG Output ?>
              </span>
            </div>
            <div class="shrink-0">
              <h3 class="block text-lg font-semibold text-gray-800 dark:text-white"><?php echo esc_html( $card['card_title'] ); ?></h3>
            </div>
          </div>
          <p class="text-gray-600 dark:text-neutral-400"><?php echo esc_html( $card['card_desc'] ); ?></p>
        </div>
        <!-- End Card -->
        <?php endforeach; endif; ?>

      </div>
    </div>
    <!-- End Icon Blocks -->
    <!-- End Icon Blocks -->

    <!-- section-five -->
    <!-- FAQ -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
      <!-- Title -->
      <?php if ( $sec5_title = carbon_get_the_post_meta( 'sec5_title' ) ): ?>
      <div class="max-w-2xl mx-auto mb-10 lg:mb-14">
        <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white"><?php echo esc_html( $sec5_title ); ?></h2>
      </div>
      <?php endif; ?>
      <!-- End Title -->

      <div class="max-w-2xl mx-auto divide-y divide-gray-200 dark:divide-neutral-700">
        <?php 
        $faqs = carbon_get_the_post_meta( 'sec5_faqs' );
        if ( ! empty( $faqs ) ):
            foreach ( $faqs as $faq ):
        ?>
        <div class="py-8 first:pt-0 last:pb-0">
          <div class="flex gap-x-5">
            <span class="shrink-0 mt-1 size-6 text-gray-500 dark:text-neutral-500 flex items-center justify-center">
              <?php echo $faq['faq_icon']; // SVG Output ?>
            </span>

            <div class="grow">
              <h3 class="md:text-lg font-semibold dark:text-white dark:text-neutral-200">
                <?php echo esc_html( $faq['faq_question'] ); ?>
              </h3>
              <p class="mt-1 text-gray-500 dark:text-neutral-500">
                <?php echo esc_html( $faq['faq_answer'] ); ?>
              </p>
            </div>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <!-- End FAQ -->
    <!-- End FAQ -->

    <!-- section-six -->
    <!-- Pricing -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
      <!-- Title -->
      <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
        <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white"><?php echo esc_html( carbon_get_the_post_meta( 'sec6_title' ) ); ?></h2>
        <p class="mt-1 text-gray-600 dark:text-neutral-400"><?php echo esc_html( carbon_get_the_post_meta( 'sec6_desc' ) ); ?></p>
      </div>
      <!-- End Title -->

      <!-- Switch -->
      <div class="flex justify-center items-center gap-x-3">
        <label for="pricing-switch" class="text-sm text-gray-800 dark:text-neutral-200">Monthly</label>
        <label for="pricing-switch" class="relative inline-block w-11 h-6 cursor-pointer">
          <input type="checkbox" id="pricing-switch" class="peer sr-only" checked>
          <span class="absolute inset-0 bg-gray-200 rounded-full transition-colors duration-200 ease-in-out peer-checked:bg-blue-600 dark:bg-neutral-700 dark:peer-checked:bg-blue-500 peer-disabled:opacity-50 peer-disabled:pointer-events-none"></span>
          <span class="absolute top-1/2 start-0.5 -translate-y-1/2 size-5 bg-white rounded-full shadow-xs transition-transform duration-200 ease-in-out peer-checked:translate-x-full dark:bg-neutral-400 dark:peer-checked:bg-white"></span>
        </label>
        <label for="pricing-switch" class="relative text-sm text-gray-800 dark:text-neutral-200">
          Annually
          <span class="absolute -top-10 start-auto -end-28">
            <span class="flex items-center">
              <svg class="w-14 h-8 -me-6" width="45" height="25" viewBox="0 0 45 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M43.2951 3.47877C43.8357 3.59191 44.3656 3.24541 44.4788 2.70484C44.5919 2.16427 44.2454 1.63433 43.7049 1.52119L43.2951 3.47877ZM4.63031 24.4936C4.90293 24.9739 5.51329 25.1423 5.99361 24.8697L13.8208 20.4272C14.3011 20.1546 14.4695 19.5443 14.1969 19.0639C13.9242 18.5836 13.3139 18.4152 12.8336 18.6879L5.87608 22.6367L1.92723 15.6792C1.65462 15.1989 1.04426 15.0305 0.563943 15.3031C0.0836291 15.5757 -0.0847477 16.1861 0.187863 16.6664L4.63031 24.4936ZM43.7049 1.52119C32.7389 -0.77401 23.9595 0.99522 17.3905 5.28788C10.8356 9.57127 6.58742 16.2977 4.53601 23.7341L6.46399 24.2659C8.41258 17.2023 12.4144 10.9287 18.4845 6.96211C24.5405 3.00476 32.7611 1.27399 43.2951 3.47877L43.7049 1.52119Z" fill="currentColor" class="fill-gray-300 dark:fill-neutral-700"/>
              </svg>
              <span class="mt-3 inline-block whitespace-nowrap text-[11px] leading-5 font-semibold uppercase bg-blue-600 text-white rounded-full py-1 px-2.5">Save up to 10%</span>
            </span>
          </span>
        </label>
      </div>
      <!-- End Switch -->

      <!-- Grid -->
      <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:items-center">
        <?php 
        $pricing_cards = carbon_get_the_post_meta( 'sec6_cards' );
        if ( ! empty( $pricing_cards ) ):
            foreach ( $pricing_cards as $card ):
                $is_popular = $card['is_popular'];
                $border_class = $is_popular ? 'border-2 border-blue-600 shadow-xl' : 'border border-gray-200';
                $dark_border_class = $is_popular ? 'dark:border-blue-700' : 'dark:border-neutral-800';
                
                // Button classes based on popularity
                $btn_classes = $is_popular 
                    ? 'border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700' 
                    : 'border-gray-200 bg-white dark:text-white shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800';
        ?>
        <!-- Card -->
        <div class="flex flex-col <?php echo $border_class; ?> text-center rounded-xl p-8 <?php echo $dark_border_class; ?>">
          <?php if ( $is_popular ): ?>
          <p class="mb-3"><span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-white custom-color-bage">Most popular</span></p>
          <?php endif; ?>
          
          <h4 class="font-medium text-lg dark:text-white dark:text-neutral-200"><?php echo esc_html( $card['card_title'] ); ?></h4>
          <span class="mt-7 font-bold text-5xl dark:text-white dark:text-neutral-200" 
                data-monthly="<?php echo esc_attr( $card['card_price_monthly'] ); ?>" 
                data-annual="<?php echo esc_attr( $card['card_price_annually'] ); ?>">
             <?php 
             $price = $card['card_price_monthly'];
             if ( is_numeric( $price ) ) {
                 echo '<span class="font-bold text-2xl me-1">$</span>' . esc_html( $price );
             } else {
                 echo esc_html( $price );
             }
             ?>
          </span>
          <p class="mt-2 text-sm text-gray-500 dark:text-neutral-500"><?php echo esc_html( $card['card_desc'] ); ?></p>

          <?php if ( ! empty( $card['card_features'] ) ): ?>
          <ul class="mt-7 space-y-2.5 text-sm">
            <?php foreach ( $card['card_features'] as $feature ): ?>
            <li class="flex gap-x-2">
              <svg class="shrink-0 mt-0.5 size-4 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="dark:text-white dark:text-neutral-400">
                <?php echo esc_html( $feature['feature_text'] ); ?>
              </span>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>

          <a class="mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border disabled:opacity-50 disabled:pointer-events-none <?php echo $btn_classes; ?>" href="<?php echo esc_url( $card['button_url'] ); ?>">
            <?php echo esc_html( $card['button_text'] ); ?>
          </a>
        </div>
        <!-- End Card -->
        <?php endforeach; endif; ?>
      </div>
      <!-- End Grid -->
    </div>
</div>
    <!-- End Pricing -->

    <!-- Card Blog -->
    <?php
    $sec7_title = carbon_get_the_post_meta( 'sec7_title' );
    $sec7_desc  = carbon_get_the_post_meta( 'sec7_desc' );
    $sec7_read_more_text = carbon_get_the_post_meta( 'sec7_read_more_text' );
    $sec7_read_more_url  = carbon_get_the_post_meta( 'sec7_read_more_url' );
    $sec7_cat_data = carbon_get_the_post_meta( 'sec7_category' );
    
    // Default to empty array if nothing selected
    if ( ! is_array( $sec7_cat_data ) ) {
        $sec7_cat_data = array();
    }

    // Get the first selected term ID (since we set limit to 1)
    $cat_id = 0;
    if ( ! empty( $sec7_cat_data ) ) {
        $first_term = reset( $sec7_cat_data );
        if ( isset( $first_term['id'] ) ) {
            $cat_id = $first_term['id'];
        }
    }

    // Only display if we have a valid category ID
    if ( $cat_id ) :
        $blog_query = new WP_Query( array(
            'cat'            => $cat_id,
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ) );

        if ( $blog_query->have_posts() ) :
    ?>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
      <!-- Title -->
      <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
        <?php if ( $sec7_title ) : ?>
        <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white"><?php echo esc_html( $sec7_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $sec7_desc ) : ?>
        <p class="mt-1 text-gray-600 dark:text-neutral-400"><?php echo esc_html( $sec7_desc ); ?></p>
        <?php endif; ?>
      </div>
      <!-- End Title -->

      <!-- Grid -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
        <!-- Card -->
        <a class="group flex flex-col h-full border border-gray-200 hover:border-transparent hover:shadow-lg focus:outline-hidden focus:border-transparent focus:shadow-lg transition duration-300 rounded-xl p-5 dark:border-neutral-700" href="<?php the_permalink(); ?>">
          <div class="aspect-w-16 aspect-h-11">
            <?php if ( has_post_thumbnail() ) : ?>
                <img class="w-full object-cover rounded-xl" src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'medium' ); ?>" alt="<?php the_title_attribute(); ?>">
            <?php else : ?>
                <!-- Fallback image if needed, or just empty -->
                <img class="w-full object-cover rounded-xl" src="https://via.placeholder.com/560x315" alt="Blog Image">
            <?php endif; ?>
          </div>
          <div class="my-6">
            <h3 class="text-xl font-semibold dark:text-white dark:text-neutral-300 dark:group-hover:text-white">
              <?php the_title(); ?>
            </h3>
            <p class="mt-5 text-gray-600 dark:text-neutral-400">
              <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
            </p>
          </div>
          <div class="mt-auto flex items-center gap-x-3">
            <img class="size-8 rounded-full" src="<?php echo get_avatar_url( get_the_author_meta( 'ID' ) ); ?>" alt="Avatar">
            <div>
              <h5 class="text-sm dark:text-white dark:text-neutral-200">By <?php echo get_the_author(); ?></h5>
            </div>
          </div>
        </a>
        <!-- End Card -->
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <!-- End Grid -->

      <!-- Card -->
      <?php if ( $sec7_read_more_text && $sec7_read_more_url ) : ?>
      <div class="mt-12 text-center">
        <a class="py-3 px-4 inline-flex items-center gap-x-1 text-sm font-medium rounded-full border border-gray-200 text-blue-600 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-blue-500 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" href="<?php echo esc_url( $sec7_read_more_url ); ?>">
          <?php echo esc_html( $sec7_read_more_text ); ?>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <?php endif; ?>
      <!-- End Card -->
    </div>
    <!-- End Card Blog -->
    <?php endif; endif; ?>



    <!-- Subscribe -->
    <?php
    $sec8_title = carbon_get_the_post_meta( 'sec8_title' );
    $sec8_placeholder = carbon_get_the_post_meta( 'sec8_input_placeholder' );
    $sec8_btn_text = carbon_get_the_post_meta( 'sec8_btn_text' );
    ?>
    <div class="max-w-6xl py-10 px-4 sm:px-6 lg:px-8 lg:py-16 mx-auto">
      <div class="max-w-xl text-center mx-auto">
        <div class="mb-5">
          <h2 class="text-2xl font-bold md:text-3xl md:leading-tight dark:text-white">
            <?php echo esc_html( $sec8_title ); ?>
          </h2>
        </div>

        <form id="wp-custom-subscribe-form">
          <div class="mt-5 lg:mt-8 flex flex-col items-center gap-2 sm:flex-row sm:gap-3">
            <div class="w-full">
              <label for="subscribe-email" class="sr-only">Email</label>
              <input type="email" id="subscribe-email" name="email" class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary" placeholder="<?php echo esc_attr( $sec8_placeholder ); ?>" required>
            </div>
            <button type="submit" class="w-full sm:w-auto whitespace-nowrap py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
              <?php echo esc_html( $sec8_btn_text ); ?>
            </button>
          </div>
          <div id="subscribe-message" class="mt-3 text-sm hidden"></div>
        </form>
      </div>
    </div>
    <!-- End Subscribe -->

</div>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pricing Switch Logic
    const toggle = document.getElementById('pricing-switch');
    const prices = document.querySelectorAll('[data-monthly][data-annual]');
    
    if (toggle) {
        toggle.addEventListener('change', function() {
            const showMonthly = this.checked;
            prices.forEach(priceEl => {
                const monthlyVal = priceEl.getAttribute('data-monthly');
                const annualVal = priceEl.getAttribute('data-annual');
                const val = showMonthly ? monthlyVal : annualVal;
                
                if (!isNaN(parseFloat(val)) && isFinite(val)) {
                    priceEl.innerHTML = `<span class="font-bold text-2xl me-1">$</span>${val}`;
                } else {
                    priceEl.textContent = val;
                }
            });
        });
    }

    // Subscribe Form Logic
    const subscribeForm = document.getElementById('wp-custom-subscribe-form');
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = document.getElementById('subscribe-email');
            const messageDiv = document.getElementById('subscribe-message');
            const submitBtn = subscribeForm.querySelector('button[type="submit"]');
            
            if (!emailInput.value) return;

            // Save original text
            const originalBtnText = submitBtn.textContent;
            
            // Lock min-width to avoid shrinking
            submitBtn.style.minWidth = submitBtn.offsetWidth + 'px';
            
            // Set loading state
            submitBtn.disabled = true;
            let dots = 0;
            const baseText = "Sending"; 
            
            // Use a wrapper span to ensure flex gap doesn't affect internal spacing
            // And fixed-width span for dots
            submitBtn.innerHTML = `<span>${baseText}<span style="display:inline-block; width: 1.5em; text-align: left;"></span></span>`;
            const dotsSpan = submitBtn.querySelector('span span'); // Select the inner span
            
            // Animation 1..2..3 dots
            const intervalId = setInterval(() => {
                dots = (dots + 1) % 4; 
                let dotString = "";
                for (let i = 0; i < dots; i++) dotString += ".";
                if (dotsSpan) dotsSpan.textContent = dotString;
            }, 500);
            
            const ajaxUrl = (typeof wp_custom_ajax !== 'undefined') ? wp_custom_ajax.ajax_url : '/wp-admin/admin-ajax.php';
            
            const formData = new FormData();
            formData.append('action', 'wp_custom_subscribe');
            formData.append('email', emailInput.value);
            formData.append('nonce', '<?php echo wp_create_nonce( "wp_custom_subscribe_nonce" ); ?>'); 

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomSuccessModal(); // Show Modal
                    subscribeForm.reset();
                    messageDiv.classList.add('hidden'); // Hide text message
                } else {
                    messageDiv.classList.remove('hidden');
                    messageDiv.textContent = data.data || 'Sending error.';
                    messageDiv.className = 'mt-3 text-sm text-red-500';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.classList.remove('hidden');
                messageDiv.textContent = 'An error occurred.';
                messageDiv.className = 'mt-3 text-sm text-red-500';
            })
            .finally(() => {
                // Stop animation
                clearInterval(intervalId);
                submitBtn.innerHTML = originalBtnText; // Restore original HTML (text)
                submitBtn.disabled = false;
                submitBtn.style.minWidth = ''; // Release min-width
                submitBtn.style.width = ''; // Release width if set previously
            });
        });

        function showCustomSuccessModal() {
            var modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);';
            modal.innerHTML = `
                <div style="background: #060607; border-radius: 16px; border: 1px solid #2E3038; padding: 40px; max-width: 420px; margin: 20px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalSlideIn 0.3s ease-out;">
                    <div style="margin-bottom: 20px;">
                        <svg style="margin: 0 auto; height: 80px; width: 80px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 12px; font-family: -apple-system, BlinkMacSystemFont, system-ui;">Thank You!</h3>
                    <p style="color: #d1d5db; margin-bottom: 24px; font-size: 16px; line-height: 1.5;">We have received your subscription and sent a confirmation email.</p>
                    <button id="close-modal-custom" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 32px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px 0 rgba(116, 79, 168, 0.75);">Close</button>
                </div>
            `;
            
            // Add Styles
            if (!document.getElementById('modal-styles')) {
                var style = document.createElement('style');
                style.id = 'modal-styles';
                style.innerHTML = '@keyframes modalSlideIn { from { opacity: 0; transform: translateY(-50px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }';
                document.head.appendChild(style);
            }

            document.body.appendChild(modal);

            // Close logic
            function closeModal() {
                modal.style.transition = 'opacity 0.4s ease';
                modal.style.opacity = '0';
                setTimeout(() => modal.remove(), 400);
            }

            // Auto close
            setTimeout(closeModal, 5000);

            // Manual close
            document.getElementById('close-modal-custom').addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if(e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function(e) {
                if(e.key === 'Escape') closeModal();
            }, {once: true});
        }
    }
});
</script>
