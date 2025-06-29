<?php

/**
 * custom template file for Rock Stars theme.
 *
 * Template Name: Page Template Custom
 *
 * @package Rock_Star
 */



get_header(); ?>


<!-- ====== Hero Section Start -->

<section id="home" class="relative overflow-hidden z-10 pt-[120px] pb-[110px] md:pt-[150px] md:pb-[120px] xl:pt-[180px] xl:pb-[160px] 2xl:pt-[210px] 2xl:pb-[200px]">

<?php
$title = carbon_get_post_meta(get_the_ID(), 'hero_title');
$description = carbon_get_post_meta(get_the_ID(), 'hero_description');

$btn1_text = carbon_get_post_meta(get_the_ID(), 'hero_button1_text');
$btn1_url = carbon_get_post_meta(get_the_ID(), 'hero_button1_url');

$btn2_text = carbon_get_post_meta(get_the_ID(), 'hero_button2_text');
$btn2_url = carbon_get_post_meta(get_the_ID(), 'hero_button2_url');

?>

<div class="container"> 
  <div class="flex flex-wrap mx-[-16px]">
    <div class="w-full px-4">
      <div class="mx-auto max-w-[570px] text-center wow fadeInUp" data-wow-delay=".2s">

        <?php if ($title): ?>
          <h1 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-5xl leading-tight sm:leading-tight md:leading-tight mb-5">
            <?= esc_html($title); ?>
          </h1>
        <?php endif; ?>

        <?php if ($description): ?>
          <p class="font-medium text-lg md:text-xl leading-relaxed md:leading-relaxed text-body-color dark:text-white dark:opacity-90 mb-12">
            <?= esc_html($description); ?>
          </p>
        <?php endif; ?>

        <div class="flex items-center justify-center">
          <?php if ($btn1_text && $btn1_url): ?>
            <a href="<?= esc_url($btn1_url); ?>"
              class="text-base font-semibold text-white bg-primary py-4 px-8 hover:bg-opacity-80 mx-2 rounded-md transition duration-300 ease-in-out">
              <?= esc_html($btn1_text); ?>
            </a>
          <?php endif; ?>

          <?php if ($btn2_text && $btn2_url): ?>
            <a href="<?= esc_url($btn2_url); ?>"
              class="text-base font-semibold text-black bg-black bg-opacity-10 dark:text-white dark:bg-white dark:bg-opacity-10 py-4 px-8 hover:bg-opacity-20 dark:hover:bg-opacity-20 mx-2 rounded-md transition duration-300 ease-in-out">
              <?= esc_html($btn2_text); ?>
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>


    <div class="absolute top-0 right-0 z-[-1]">
      <svg width="450" height="556" viewBox="0 0 450 556" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="277" cy="63" r="225" fill="url(#paint0_linear_25:217)" />
        <circle cx="17.9997" cy="182" r="18" fill="url(#paint1_radial_25:217)" />
        <circle cx="76.9997" cy="288" r="34" fill="url(#paint2_radial_25:217)" />
        <circle cx="325.486" cy="302.87" r="180" transform="rotate(-37.6852 325.486 302.87)"
          fill="url(#paint3_linear_25:217)" />
        <circle opacity="0.8" cx="184.521" cy="315.521" r="132.862" transform="rotate(114.874 184.521 315.521)"
          stroke="url(#paint4_linear_25:217)" />
        <circle opacity="0.8" cx="356" cy="290" r="179.5" transform="rotate(-30 356 290)"
          stroke="url(#paint5_linear_25:217)" />
        <circle opacity="0.8" cx="191.659" cy="302.659" r="133.362" transform="rotate(133.319 191.659 302.659)"
          fill="url(#paint6_linear_25:217)" />
        <defs>
          <linearGradient id="paint0_linear_25:217" x1="-54.5003" y1="-178" x2="222" y2="288"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
          <radialGradient id="paint1_radial_25:217" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
            gradientTransform="translate(17.9997 182) rotate(90) scale(18)">
            <stop offset="0.145833" stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0.08" />
          </radialGradient>
          <radialGradient id="paint2_radial_25:217" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
            gradientTransform="translate(76.9997 288) rotate(90) scale(34)">
            <stop offset="0.145833" stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0.08" />
          </radialGradient>
          <linearGradient id="paint3_linear_25:217" x1="226.775" y1="-66.1548" x2="292.157" y2="351.421"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
          <linearGradient id="paint4_linear_25:217" x1="184.521" y1="182.159" x2="184.521" y2="448.882"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="white" stop-opacity="0" />
          </linearGradient>
          <linearGradient id="paint5_linear_25:217" x1="356" y1="110" x2="356" y2="470" gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="white" stop-opacity="0" />
          </linearGradient>
          <linearGradient id="paint6_linear_25:217" x1="118.524" y1="29.2497" x2="166.965" y2="338.63"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
        </defs>
      </svg>
    </div>
    <div class="absolute bottom-0 left-0 z-[-1]">
      <svg width="364" height="201" viewBox="0 0 364 201" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M5.88928 72.3303C33.6599 66.4798 101.397 64.9086 150.178 105.427C211.155 156.076 229.59 162.093 264.333 166.607C299.076 171.12 337.718 183.657 362.889 212.24"
          stroke="url(#paint0_linear_25:218)" />
        <path
          d="M-22.1107 72.3303C5.65989 66.4798 73.3965 64.9086 122.178 105.427C183.155 156.076 201.59 162.093 236.333 166.607C271.076 171.12 309.718 183.657 334.889 212.24"
          stroke="url(#paint1_linear_25:218)" />
        <path
          d="M-53.1107 72.3303C-25.3401 66.4798 42.3965 64.9086 91.1783 105.427C152.155 156.076 170.59 162.093 205.333 166.607C240.076 171.12 278.718 183.657 303.889 212.24"
          stroke="url(#paint2_linear_25:218)" />
        <path
          d="M-98.1618 65.0889C-68.1416 60.0601 4.73364 60.4882 56.0734 102.431C120.248 154.86 139.905 161.419 177.137 166.956C214.37 172.493 255.575 186.165 281.856 215.481"
          stroke="url(#paint3_linear_25:218)" />
        <circle opacity="0.8" cx="214.505" cy="60.5054" r="49.7205" transform="rotate(-13.421 214.505 60.5054)"
          stroke="url(#paint4_linear_25:218)" />
        <circle cx="220" cy="63" r="43" fill="url(#paint5_radial_25:218)" />
        <defs>
          <linearGradient id="paint0_linear_25:218" x1="184.389" y1="69.2405" x2="184.389" y2="212.24"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" />
          </linearGradient>
          <linearGradient id="paint1_linear_25:218" x1="156.389" y1="69.2405" x2="156.389" y2="212.24"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" />
          </linearGradient>
          <linearGradient id="paint2_linear_25:218" x1="125.389" y1="69.2405" x2="125.389" y2="212.24"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" />
          </linearGradient>
          <linearGradient id="paint3_linear_25:218" x1="93.8507" y1="67.2674" x2="89.9278" y2="210.214"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" stop-opacity="0" />
            <stop offset="1" stop-color="#4A6CF7" />
          </linearGradient>
          <linearGradient id="paint4_linear_25:218" x1="214.505" y1="10.2849" x2="212.684" y2="99.5816"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
          <radialGradient id="paint5_radial_25:218" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
            gradientTransform="translate(220 63) rotate(90) scale(43)">
            <stop offset="0.145833" stop-color="white" stop-opacity="0" />
            <stop offset="1" stop-color="white" stop-opacity="0.08" />
          </radialGradient>
        </defs>
      </svg>
    </div>
</section>


<!-- ====== Hero Section End -->

<!-- ====== Features Section Start -->

<?php
$title = carbon_get_post_meta(get_the_ID(), 'features_section_title');
$desc = carbon_get_post_meta(get_the_ID(), 'features_section_description');
$features = carbon_get_post_meta(get_the_ID(), 'features_list');
?>

<section id="features" class="bg-primary bg-opacity-[3%] pt-[120px] pb-[50px]">
  <div class="container">

    <?php if ($title || $desc): ?>
      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full px-4">
          <div class="mx-auto max-w-[570px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
            <?php if ($title): ?>
              <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                <?= esc_html($title); ?>
              </h2>
            <?php endif; ?>
            <?php if ($desc): ?>
              <p class="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed">
                <?= esc_html($desc); ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($features): ?>
      <div class="flex flex-wrap mx-[-16px]">
        <?php foreach ($features as $i => $feature): ?>
          <div class="w-full md:w-1/2 lg:w-1/3 px-4">
            <div class="mb-[70px] wow fadeInUp" data-wow-delay=".<?= 10 + $i * 5 ?>s">
              <div class="w-[70px] h-[70px] flex items-center justify-center rounded-md bg-primary bg-opacity-10 mb-10 text-primary">
                <?= $feature['feature_icon_svg']; ?>
              </div>
              <h3 class="font-bold text-black dark:text-white text-xl sm:text-2xl lg:text-xl xl:text-2xl mb-5">
                <?= esc_html($feature['feature_title']); ?>
              </h3>
              <p class="text-body-color text-base leading-relaxed font-medium pr-[10px]">
                <?= esc_html($feature['feature_description']); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>


  <!-- ====== Features Section End -->

  <!-- ====== Video Section Start -->

  <section class="relative z-10 py-[120px]">
    <div class="container">
      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full px-4">
          <div class="mx-auto max-w-[570px] text-center mb-20 wow fadeInUp" data-wow-delay=".1s">
            <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">We are ready to
              help</h2>
            <p class="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed">
              There are many variations of passages of Lorem Ipsum available but the majority have suffered alteration
              in some form.
            </p>
          </div>
        </div>
      </div>
      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full px-4">
          <div class="mx-auto max-w-[770px] rounded-md overflow-hidden wow fadeInUp" data-wow-delay=".15s">
            <div class="relative items-center justify-center">
              <img src="http://localhost:8081/wp-content/uploads/2025/06/video.jpg" alt="video image" class="w-full h-full object-cover object-center" />
              <div class="absolute w-full h-full top-0 right-0 flex items-center justify-center">
                <a href="javascript:void(0)"
                  class="glightbox w-[70px] h-[70px] rounded-full flex items-center justify-center bg-white bg-opacity-75 text-primary hover:bg-opacity-100 transition">
                  <svg width="16" height="18" viewBox="0 0 16 18" class="fill-current">
                    <path
                      d="M15.5 8.13397C16.1667 8.51888 16.1667 9.48112 15.5 9.86602L2 17.6603C1.33333 18.0452 0.499999 17.564 0.499999 16.7942L0.5 1.20577C0.5 0.43597 1.33333 -0.0451549 2 0.339745L15.5 8.13397Z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 z-[-1]">
      <img src="http://localhost:8081/wp-content/uploads/2025/06/shape.svg" alt="shape" class="w-full" />
    </div>
  </section>

  <!-- ====== Video Section End -->

  <!-- ====== Brands Section Start -->

  <?php
$logos = carbon_get_post_meta(get_the_ID(), 'brand_logos_list');
?>

<?php if (!empty($logos)): ?>
<section class="pt-16">
  <div class="container">
    <div class="flex flex-wrap mx-[-16px]">
      <div class="w-full px-4">
        <div class="bg-dark dark:bg-primary dark:bg-opacity-5 rounded-md flex flex-wrap items-center justify-center py-8 px-8 sm:px-10 md:py-[40px] md:px-[50px] xl:p-[50px] 2xl:py-[60px] 2xl:px-[70px] wow fadeInUp" data-wow-delay=".1s">
          <?php foreach ($logos as $item): ?>
            <a href="<?= esc_url($item['brand_link']); ?>" target="_blank" rel="nofollow noreferrer"
              class="flex items-center justify-center lg:max-w-[130px] xl:max-w-[150px] 2xl:max-w-[160px] mx-3 sm:mx-4 xl:mx-6 2xl:mx-8 py-[15px] grayscale hover:grayscale-0 opacity-70 hover:opacity-100 dark:opacity-60 dark:hover:opacity-100 transition">
              <img src="<?= esc_url($item['brand_logo']); ?>" alt="<?= esc_attr($item['brand_alt']); ?>" />
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>


  <!-- ====== Brands Section End -->

  <!-- ====== Porfolio block --->

  
<div class="portfolio-name" style="text-align: center;">
  <h1 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">Portfolio</h1>
</div>


<!-- portfolio slider -->

<?php
$page_id = get_the_ID();
$slides = carbon_get_post_meta($page_id, 'portfolio_slides');

if ($slides && is_array($slides)) : ?>
    <div class="portfolio-slider-block"></div>

    <div class="slider-container" id="sliderContainer">
        <div id="sliderTrack" class="slider-track">
            <?php foreach ($slides as $slide) :
                $img_id = $slide['slide_image'];
                $url = esc_url($slide['slide_url'] ?: '#');
                $alt = esc_attr($slide['slide_alt'] ?: '');
                $img_html = wp_get_attachment_image($img_id, 'full', false, ['alt' => $alt]);
            ?>
                <a href="<?php echo $url; ?>" class="slide">
                    <?php echo $img_html; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="slider-buttons">
        <button id="prevBtn" aria-label="Предыдущий слайд"><</button>
        <button id="nextBtn" aria-label="Следующий слайд">></button>
    </div>
<?php endif; ?>


<!-- portfolio slider -->


  <!-- ====== Porfolio block End --->

  <!-- ====== About Section Start -->

  <?php
$title = carbon_get_post_meta(get_the_ID(), 'about_title');
$subtitle = carbon_get_post_meta(get_the_ID(), 'about_subtitle');
$image = carbon_get_post_meta(get_the_ID(), 'about_image');
$features = carbon_get_post_meta(get_the_ID(), 'about_features');
?>

<section id="about" class="pt-[120px]">
  <div class="container">
    <div class="pb-[100px] border-b border-white border-opacity-[.15]">
      <div class="flex flex-wrap items-center mx-[-16px]">
        <div class="w-full lg:w-1/2 px-4">
          <div class="mb-12 lg:mb-0 max-w-[570px] wow fadeInUp" data-wow-delay=".15s">
            <?php if ($title): ?>
              <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] lg:text-4xl xl:text-[45px] leading-tight mb-6">
                <?= esc_html($title); ?>
              </h2>
            <?php endif; ?>

            <?php if ($subtitle): ?>
              <p class="font-medium text-body-color text-base sm:text-lg leading-relaxed mb-11">
                <?= nl2br(esc_html($subtitle)); ?>
              </p>
            <?php endif; ?>

            <?php if (!empty($features)): ?>
              <div class="flex flex-wrap mx-[-12px]">
                <?php
                $columns = array_chunk($features, ceil(count($features) / 2));
                foreach ($columns as $col):
                ?>
                  <div class="w-full sm:w-1/2 lg:w-full xl:w-1/2 px-3">
                    <?php foreach ($col as $item): ?>
                      <p class="flex items-center text-body-color text-lg font-medium mb-5">
                        <span class="w-[30px] h-[30px] flex items-center justify-center rounded-md bg-primary bg-opacity-10 text-primary mr-4">
                          <svg width="16" height="13" viewBox="0 0 16 13" class="fill-current">
                            <path d="M5.8535 12.6631C5.65824 12.8584 5.34166 12.8584 5.1464 12.6631L0.678505 8.1952C0.483242 7.99994 0.483242 7.68336 0.678505 7.4881L2.32921 5.83739C2.52467 5.64193 2.84166 5.64216 3.03684 5.83791L5.14622 7.95354C5.34147 8.14936 5.65859 8.14952 5.85403 7.95388L13.3797 0.420561C13.575 0.22513 13.8917 0.225051 14.087 0.420383L15.7381 2.07143C15.9333 2.26669 15.9333 2.58327 15.7381 2.77854L5.8535 12.6631Z" />
                          </svg>
                        </span>
                        <?= esc_html($item['feature_text']); ?>
                      </p>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="w-full lg:w-1/2 px-4">
          <div class="text-center lg:text-right wow fadeInUp" data-wow-delay=".2s">
            <?php if ($image): ?>
              <img src="<?= esc_url($image); ?>" alt="about-image" class="max-w-full mx-auto lg:mr-0" />
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- ====== About Section End -->

  <!-- ====== About Section Start -->

  <?php
$image = carbon_get_post_meta(get_the_ID(), 'benefits_image');
$benefits = carbon_get_post_meta(get_the_ID(), 'benefits_list');
?>

<section class="pt-[100px] pb-[120px]">
  <div class="container">
    <div class="flex flex-wrap items-center mx-[-16px]">
      <div class="w-full lg:w-1/2 px-4">
        <div class="text-center lg:text-left mb-12 lg:mb-0 wow fadeInUp" data-wow-delay=".15s">
          <?php if ($image): ?>
            <img src="<?= esc_url($image); ?>" alt="about image" class="max-w-full mx-auto lg:ml-0" />
          <?php endif; ?>
        </div>
      </div>

      <div class="w-full lg:w-1/2 px-4">
        <div class="max-w-[470px] wow fadeInUp" data-wow-delay=".2s">
          <?php if (!empty($benefits)): ?>
            <?php foreach ($benefits as $benefit): ?>
              <div class="mb-9">
                <?php if (!empty($benefit['benefit_title'])): ?>
                  <h3 class="font-bold text-black dark:text-white text-xl sm:text-2xl lg:text-xl xl:text-2xl mb-4">
                    <?= esc_html($benefit['benefit_title']); ?>
                  </h3>
                <?php endif; ?>
                <?php if (!empty($benefit['benefit_description'])): ?>
                  <p class="text-body-color text-base sm:text-lg leading-relaxed font-medium">
                    <?= nl2br(esc_html($benefit['benefit_description'])); ?>
                  </p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- ====== About Section End -->

  <!-- ====== Testimonial Section Start -->

  <section class="relative z-10 pt-[120px] pb-20 bg-primary bg-opacity-[3%]">
  <div class="container">
    <div class="flex flex-wrap mx-[-16px]">
      <div class="w-full px-4">
        <div class="mx-auto max-w-[570px] text-center mb-[100px]">
          <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
            <?= carbon_get_theme_option('testimonial_title'); ?>
          </h2>
          <p class="text-body-color text-base md:text-lg leading-relaxed">
            <?= carbon_get_theme_option('testimonial_description'); ?>
          </p>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap mx-[-16px]">
      <?php
        $testimonials = carbon_get_theme_option('testimonial_list');
        if ($testimonials) :
          foreach ($testimonials as $index => $item) :
            $delay = 0.1 + $index * 0.05;
            $stars = intval($item['rating']);
      ?>
        <div class="w-full md:w-1/2 lg:w-1/3 px-4">
          <div class="shadow-one bg-white dark:bg-[#1D2144] rounded-md p-8 lg:px-5 xl:px-8 mb-10 wow fadeInUp"
               data-wow-delay="<?= $delay ?>s">
            <div class="flex items-center mb-5">
              <?php for ($i = 1; $i <= 5; $i++) : ?>
                <span class="text-yellow mr-1 block">
                  <svg width="18" height="16" viewBox="0 0 18 16" class="fill-current">
                    <path
                      d="M9.09815 0.361679L11.1054 6.06601H17.601L12.3459 9.59149L14.3532 15.2958L9.09815 11.7703L3.84309 15.2958L5.85035 9.59149L0.595291 6.06601H7.0909L9.09815 0.361679Z" />
                  </svg>
                </span>
              <?php endfor; ?>
            </div>

            <p class="text-base text-body-color dark:text-white leading-relaxed pb-8 border-b border-body-color dark:border-white border-opacity-10 mb-8">
              “<?= esc_html($item['text']); ?>”
            </p>

            <div class="flex items-center">
              <?php if ($item['photo']) : ?>
                <div class="rounded-full overflow-hidden max-w-[50px] w-full h-[50px] mr-4">
                  <img src="<?= wp_get_attachment_image_url($item['photo'], 'thumbnail'); ?>" alt="<?= esc_attr($item['name']); ?>" />
                </div>
              <?php endif; ?>
              <div class="w-full">
                <h5 class="text-lg lg:text-base xl:text-lg text-dark dark:text-white font-semibold mb-1">
                  <?= esc_html($item['name']); ?>
                </h5>
                <p class="text-sm text-body-color">
                  <?= esc_html($item['position']); ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- SVG декорации можно оставить как есть -->

</section>

  <!-- ====== Testimonial Section End -->

  <!-- ====== Pricing Section Start -->

  <section id="pricing" class="relative z-10 pt-[120px] pb-20">
    <div class="container">
      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full px-4">
          <div class="mx-auto max-w-[655px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
            <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">Simple and
              Affordable Pricing</h2>
            <p class="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed max-w-[570px] mx-auto">
              There are many variations of passages of Lorem Ipsum available but the majority have suffered alteration
              in some form.
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full px-4">
          <div class="flex justify-center mb-16 wow fadeInUp" data-wow-delay=".1s">
            <span class="text-dark dark:text-white text-base font-semibold mr-4 monthly cursor-pointer"> Monthly </span>
            <label for="togglePlan" class="flex items-center cursor-pointer">
              <div class="relative">
                <input id="togglePlan" type="checkbox" class="sr-only" />
                <div class="w-14 h-5 bg-[#1D2144] rounded-full shadow-inner"></div>
                <div
                  class="dot absolute w-7 h-7 bg-primary rounded-full shadow-switch-1 left-0 top-[-4px] transition flex items-center justify-center">
                  <span class="active w-4 h-4 rounded-full bg-white"></span>
                </div>
              </div>
            </label>
            <span class="text-dark dark:text-white text-base font-semibold ml-4 yearly cursor-pointer"> Yearly </span>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full md:w-1/2 lg:w-1/3 px-4">
          <div class="relative z-10 bg-white dark:bg-[#1D2144] shadow-signUp px-8 py-10 rounded-md mb-10 wow fadeInUp"
            data-wow-delay=".1s">
            <div class="flex justify-between items-center">
              <h3 class="font-bold text-black dark:text-white text-3xl mb-2 price">
                $<span class="amount">40</span>
                <span class="text-dark dark:text-body-color time">/mo</span>
              </h3>
              <h4 class="text-white font-bold text-xl mb-2">Lite</h4>
            </div>
            <p class="text-base text-body-color mb-7">Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.</p>
            <div
              class="border-b border-body-color dark:border-white border-opacity-10 dark:border-opacity-10 pb-8 mb-8">
              <a href="javascript:void(0)"
                class="font-semibold text-base text-white bg-primary w-full flex items-center justify-center rounded-md p-3 hover:shadow-signUp hover:bg-opacity-80 transition duration-300 ease-in-out">
                Start Free Trial
              </a>
            </div>
            <div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">All UI Components</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Use with Unlimited Projects</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Commercial Use</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Email Support</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="8" viewBox="0 0 8 8" class="fill-current stroke-current">
                    <path
                      d="M1.40102 0.95486C1.27421 0.828319 1.07219 0.828354 0.945421 0.954965C0.818519 1.08171 0.818519 1.28389 0.945421 1.41063L0.945612 1.41083L3.54915 4.00184L0.955169 6.60202C0.955106 6.60209 0.95504 6.60215 0.954978 6.60222C0.828263 6.72897 0.82833 6.93101 0.955169 7.05769C1.01288 7.11533 1.09989 7.15024 1.17815 7.15024C1.25641 7.15024 1.34342 7.11533 1.40113 7.05769L1.29513 6.95156L1.40113 7.05769L4.00493 4.45706L6.59917 7.0575L6.59936 7.05769C6.65707 7.11533 6.74408 7.15024 6.82234 7.15024C6.9006 7.15024 6.98761 7.11533 7.04532 7.05769C7.17215 6.93102 7.17222 6.729 7.04553 6.60224C7.04546 6.60217 7.04539 6.6021 7.04532 6.60202L4.46051 4.00165L7.05507 1.4009C7.05511 1.40085 7.05516 1.4008 7.05521 1.40076L7.05526 1.40071L6.94907 1.29477L1.40102 0.95486ZM1.40102 0.95486C1.40106 0.954895 1.40109 0.95493 1.40113 0.954965L1.40102 0.95486Z"
                      stroke-width="0.3" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Lifetime Access</p>
              </div>
              <div class="flex items-center">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="8" viewBox="0 0 8 8" class="fill-current stroke-current">
                    <path
                      d="M1.40102 0.95486C1.27421 0.828319 1.07219 0.828354 0.945421 0.954965C0.818519 1.08171 0.818519 1.28389 0.945421 1.41063L0.945612 1.41083L3.54915 4.00184L0.955169 6.60202C0.955106 6.60209 0.95504 6.60215 0.954978 6.60222C0.828263 6.72897 0.82833 6.93101 0.955169 7.05769C1.01288 7.11533 1.09989 7.15024 1.17815 7.15024C1.25641 7.15024 1.34342 7.11533 1.40113 7.05769L1.29513 6.95156L1.40113 7.05769L4.00493 4.45706L6.59917 7.0575L6.59936 7.05769C6.65707 7.11533 6.74408 7.15024 6.82234 7.15024C6.9006 7.15024 6.98761 7.11533 7.04532 7.05769C7.17215 6.93102 7.17222 6.729 7.04553 6.60224C7.04546 6.60217 7.04539 6.6021 7.04532 6.60202L4.46051 4.00165L7.05507 1.4009C7.05511 1.40085 7.05516 1.4008 7.05521 1.40076L7.05526 1.40071L6.94907 1.29477L1.40102 0.95486ZM1.40102 0.95486C1.40106 0.954895 1.40109 0.95493 1.40113 0.954965L1.40102 0.95486Z"
                      stroke-width="0.3" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Free Lifetime Updates</p>
              </div>
            </div>
            <div class="absolute bottom-0 right-0 z-[-1]">
              <svg width="179" height="158" viewBox="0 0 179 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.5"
                  d="M75.0002 63.256C115.229 82.3657 136.011 137.496 141.374 162.673C150.063 203.47 207.217 197.755 202.419 167.738C195.393 123.781 137.273 90.3579 75.0002 63.256Z"
                  fill="url(#paint0_linear_70:153)" />
                <path opacity="0.3"
                  d="M178.255 0.150879C129.388 56.5969 134.648 155.224 143.387 197.482C157.547 265.958 65.9705 295.709 53.1024 246.401C34.2588 174.197 100.939 83.7223 178.255 0.150879Z"
                  fill="url(#paint1_linear_70:153)" />
                <defs>
                  <linearGradient id="paint0_linear_70:153" x1="69.6694" y1="29.9033" x2="196.108" y2="83.2919"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint1_linear_70:153" x1="165.348" y1="-75.4466" x2="-3.75136" y2="103.645"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3 px-4">
          <div class="relative z-10 bg-white dark:bg-[#1D2144] shadow-signUp px-8 py-10 rounded-md mb-10 wow fadeInUp"
            data-wow-delay=".15s">
            <div class="flex justify-between items-center">
              <h3 class="font-bold text-black dark:text-white text-3xl mb-2 price">
                $<span class="amount">399</span>
                <span class="text-dark dark:text-body-color time">/mo</span>
              </h3>
              <h4 class="text-white font-bold text-xl mb-2">Basic</h4>
            </div>
            <p class="text-base text-body-color mb-7">Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.</p>
            <div
              class="border-b border-body-color dark:border-white border-opacity-10 dark:border-opacity-10 pb-8 mb-8">
              <a href="javascript:void(0)"
                class="font-semibold text-base text-white bg-primary w-full flex items-center justify-center rounded-md p-3 hover:shadow-signUp hover:bg-opacity-80 transition duration-300 ease-in-out">
                Start Free Trial
              </a>
            </div>
            <div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">All UI Components</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Use with Unlimited Projects</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Commercial Use</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Email Support</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Lifetime Access</p>
              </div>
              <div class="flex items-center">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="8" viewBox="0 0 8 8" class="fill-current stroke-current">
                    <path
                      d="M1.40102 0.95486C1.27421 0.828319 1.07219 0.828354 0.945421 0.954965C0.818519 1.08171 0.818519 1.28389 0.945421 1.41063L0.945612 1.41083L3.54915 4.00184L0.955169 6.60202C0.955106 6.60209 0.95504 6.60215 0.954978 6.60222C0.828263 6.72897 0.82833 6.93101 0.955169 7.05769C1.01288 7.11533 1.09989 7.15024 1.17815 7.15024C1.25641 7.15024 1.34342 7.11533 1.40113 7.05769L1.29513 6.95156L1.40113 7.05769L4.00493 4.45706L6.59917 7.0575L6.59936 7.05769C6.65707 7.11533 6.74408 7.15024 6.82234 7.15024C6.9006 7.15024 6.98761 7.11533 7.04532 7.05769C7.17215 6.93102 7.17222 6.729 7.04553 6.60224C7.04546 6.60217 7.04539 6.6021 7.04532 6.60202L4.46051 4.00165L7.05507 1.4009C7.05511 1.40085 7.05516 1.4008 7.05521 1.40076L7.05526 1.40071L6.94907 1.29477L1.40102 0.95486ZM1.40102 0.95486C1.40106 0.954895 1.40109 0.95493 1.40113 0.954965L1.40102 0.95486Z"
                      stroke-width="0.3" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Free Lifetime Updates</p>
              </div>
            </div>
            <div class="absolute bottom-0 right-0 z-[-1]">
              <svg width="179" height="158" viewBox="0 0 179 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.5"
                  d="M75.0002 63.256C115.229 82.3657 136.011 137.496 141.374 162.673C150.063 203.47 207.217 197.755 202.419 167.738C195.393 123.781 137.273 90.3579 75.0002 63.256Z"
                  fill="url(#paint0_linear_70:153)" />
                <path opacity="0.3"
                  d="M178.255 0.150879C129.388 56.5969 134.648 155.224 143.387 197.482C157.547 265.958 65.9705 295.709 53.1024 246.401C34.2588 174.197 100.939 83.7223 178.255 0.150879Z"
                  fill="url(#paint1_linear_70:153)" />
                <defs>
                  <linearGradient id="paint0_linear_70:153" x1="69.6694" y1="29.9033" x2="196.108" y2="83.2919"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint1_linear_70:153" x1="165.348" y1="-75.4466" x2="-3.75136" y2="103.645"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3 px-4">
          <div class="relative z-10 bg-white dark:bg-[#1D2144] shadow-signUp px-8 py-10 rounded-md mb-10 wow fadeInUp"
            data-wow-delay=".2s">
            <div class="flex justify-between items-center">
              <h3 class="font-bold text-black dark:text-white text-3xl mb-2 price">
                $<span class="amount">589</span>
                <span class="text-dark dark:text-body-color time">/mo</span>
              </h3>
              <h4 class="text-white font-bold text-xl mb-2">Plus</h4>
            </div>
            <p class="text-base text-body-color mb-7">Lorem ipsum dolor sit amet adiscing elit Mauris egestas enim.</p>
            <div
              class="border-b border-body-color dark:border-white border-opacity-10 dark:border-opacity-10 pb-8 mb-8">
              <a href="javascript:void(0)"
                class="font-semibold text-base text-white bg-primary w-full flex items-center justify-center rounded-md p-3 hover:shadow-signUp hover:bg-opacity-80 transition duration-300 ease-in-out">
                Start Free Trial
              </a>
            </div>
            <div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">All UI Components</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Use with Unlimited Projects</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Commercial Use</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Email Support</p>
              </div>
              <div class="flex items-center mb-3">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Lifetime Access</p>
              </div>
              <div class="flex items-center">
                <span
                  class="bg-primary bg-opacity-10 text-primary max-w-[18px] w-full h-[18px] mr-3 flex items-center justify-center rounded-full">
                  <svg width="8" height="6" viewBox="0 0 8 6" class="fill-current">
                    <path
                      d="M2.90567 6.00024C2.68031 6.00024 2.48715 5.92812 2.294 5.74764L0.169254 3.43784C-0.0560926 3.18523 -0.0560926 2.78827 0.169254 2.53566C0.39461 2.28298 0.74873 2.28298 0.974086 2.53566L2.90567 4.66497L7.02642 0.189715C7.25175 -0.062913 7.60585 -0.062913 7.83118 0.189715C8.0566 0.442354 8.0566 0.839355 7.83118 1.09198L3.54957 5.78375C3.32415 5.92812 3.09882 6.00024 2.90567 6.00024Z" />
                  </svg>
                </span>
                <p class="text-base font-medium text-body-color m-0">Free Lifetime Updates</p>
              </div>
            </div>
            <div class="absolute bottom-0 right-0 z-[-1]">
              <svg width="179" height="158" viewBox="0 0 179 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.5"
                  d="M75.0002 63.256C115.229 82.3657 136.011 137.496 141.374 162.673C150.063 203.47 207.217 197.755 202.419 167.738C195.393 123.781 137.273 90.3579 75.0002 63.256Z"
                  fill="url(#paint0_linear_70:153)" />
                <path opacity="0.3"
                  d="M178.255 0.150879C129.388 56.5969 134.648 155.224 143.387 197.482C157.547 265.958 65.9705 295.709 53.1024 246.401C34.2588 174.197 100.939 83.7223 178.255 0.150879Z"
                  fill="url(#paint1_linear_70:153)" />
                <defs>
                  <linearGradient id="paint0_linear_70:153" x1="69.6694" y1="29.9033" x2="196.108" y2="83.2919"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint1_linear_70:153" x1="165.348" y1="-75.4466" x2="-3.75136" y2="103.645"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4A6CF7" stop-opacity="0.62" />
                    <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="absolute left-0 bottom-0 z-[-1]">
      <svg width="239" height="601" viewBox="0 0 239 601" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect opacity="0.3" x="-184.451" y="600.973" width="196" height="541.607" rx="2"
          transform="rotate(-128.7 -184.451 600.973)" fill="url(#paint0_linear_93:235)" />
        <rect opacity="0.3" x="-188.201" y="385.272" width="59.7544" height="541.607" rx="2"
          transform="rotate(-128.7 -188.201 385.272)" fill="url(#paint1_linear_93:235)" />
        <defs>
          <linearGradient id="paint0_linear_93:235" x1="-90.1184" y1="420.414" x2="-90.1184" y2="1131.65"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
          <linearGradient id="paint1_linear_93:235" x1="-159.441" y1="204.714" x2="-159.441" y2="915.952"
            gradientUnits="userSpaceOnUse">
            <stop stop-color="#4A6CF7" />
            <stop offset="1" stop-color="#4A6CF7" stop-opacity="0" />
          </linearGradient>
        </defs>
      </svg>
    </div>
  </section>

  <!-- ====== Pricing Section End -->

  <!-- ====== Blog Section Start -->

  <?php
// Сначала пытаемся получить посты из категории 'front-page-three-last-posts'
$args_category = array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post_status' => 'publish',
    'category_name' => 'lasts-posts',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false
);

$category_posts = new WP_Query($args_category);

// Если в категории нет постов, получаем последние 3 поста
if (!$category_posts->have_posts()) {
    wp_reset_postdata(); // Сбрасываем данные предыдущего запроса
    
    $args_latest = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    );
    $posts_query = new WP_Query($args_latest);
} else {
    $posts_query = $category_posts;
}

if ($posts_query->have_posts()) : ?>

<section id="blog" class="bg-primary bg-opacity-5 pt-[120px] pb-20">
    <div class="container">
        <div class="flex flex-wrap mx-[-16px]">
            <div class="w-full px-4">
                <div class="mx-auto max-w-[570px] text-center mb-[100px] wow fadeInUp" data-wow-delay=".1s">
                    <h2 class="text-black dark:text-white font-bold text-3xl sm:text-4xl md:text-[45px] mb-4">
                        <?php _e('Our latest blogs', 'textdomain'); ?>
                    </h2>
                    <p class="text-body-color text-base md:text-lg leading-relaxed md:leading-relaxed">
                        <?php _e('Check out our latest articles and useful tips', 'textdomain'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap mx-[-16px] justify-center">
            <?php 
            $delay = 0.1;
            $post_count = 0; // Счетчик постов
            while ($posts_query->have_posts() && $post_count < 3) : 
                $posts_query->the_post();
                $post_count++; // Увеличиваем счетчик
                $categories = get_the_category();
                $first_category = !empty($categories) ? $categories[0]->name : 'Блог';
            ?>
            <div class="w-full md:w-2/3 lg:w-1/2 xl:w-1/3 px-4">
                <div class="relative bg-white dark:bg-dark shadow-one rounded-md overflow-hidden mb-10 wow fadeInUp"
                    data-wow-delay="<?php echo $delay; ?>s">
                    <a href="<?php the_permalink(); ?>" class="w-full block relative">
                        <span class="absolute top-6 right-6 bg-primary rounded-full inline-flex items-center justify-center py-2 px-4 font-semibold text-sm text-white">
                            <?php echo esc_html($first_category); ?>
                        </span>
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('class' => 'w-full', 'alt' => get_the_title())); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-blog.jpg" alt="<?php the_title(); ?>" class="w-full" />
                        <?php endif; ?>
                    </a>
                    <div class="p-6 sm:p-8 md:py-8 md:px-6 lg:p-8 xl:py-8 xl:px-5 2xl:p-8">
                        <h3>
                            <a href="<?php the_permalink(); ?>"
                                class="font-bold text-black dark:text-white text-xl sm:text-2xl block mb-4 hover:text-primary dark:hover:text-primary">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <p class="text-base text-body-color font-medium pb-6 mb-6 border-b border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                        </p>
                        <div class="flex items-center">
                            <div class="flex items-center pr-5 mr-5 xl:pr-3 2xl:pr-5 xl:mr-3 2xl:mr-5 border-r border-body-color border-opacity-10 dark:border-white dark:border-opacity-10">
                                <div class="max-w-[40px] w-full h-[40px] rounded-full overflow-hidden mr-4">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 40, '', get_the_author(), array('class' => 'w-full')); ?>
                                </div>
                                <div class="w-full">
                                    <h4 class="text-sm font-medium text-dark dark:text-white mb-1">
                                        <?php _e('Автор:', 'textdomain'); ?>
                                        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                            class="text-dark dark:text-white hover:text-primary dark:hover:text-primary">
                                            <?php the_author(); ?>
                                        </a>
                                    </h4>
                                    <p class="text-xs text-body-color">
                                        <?php 
                                        $author_description = get_the_author_meta('description');
                                        echo !empty($author_description) ? esc_html($author_description) : __('Автор блогу', 'textdomain');
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="inline-block">
                                <h4 class="text-sm font-medium text-dark dark:text-white mb-1"><?php _e('Дата', 'textdomain'); ?></h4>
                                <p class="text-xs text-body-color"><?php echo get_the_date('j M, Y'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            $delay += 0.05;
            endwhile; 
            ?>
        </div>
    </div>
</section>

<?php 
endif;
wp_reset_postdata();
?>

  <!-- ====== Blog Section End -->

  <!-- ====== Contact Section Start -->

  <section id="contact" class="pt-[120px] pb-20 overflow-hidden">
    <div class="container">
      <div class="flex flex-wrap mx-[-16px]">
        <div class="w-full lg:w-8/12 px-4">
          <div
            class="bg-primary bg-opacity-[3%] dark:bg-dark rounded-md p-11 mb-12 lg:mb-5 sm:p-[55px] lg:p-11 xl:p-[55px] wow fadeInUp"
            data-wow-delay=".15s
              ">
            <h2 class="font-bold text-black dark:text-white text-2xl sm:text-3xl lg:text-2xl xl:text-3xl mb-3">Need
              Help? Open a Ticket</h2>
            <p class="text-body-color text-base font-medium mb-12">Our support team will get back to you ASAP via email.
            </p>

            <!-- form ticket admin  -->

            <form id="wp-custom-contact-form" class="wp-custom-form">
  <div class="flex flex-wrap mx-[-16px]">
    <div class="w-full md:w-1/2 px-4">
      <div class="mb-8">
        <label for="wp-custom-name" class="block text-sm font-medium text-dark dark:text-white mb-3"> Your Name
        </label>
        <input type="text" id="wp-custom-name" name="wp-custom-name" placeholder="Enter your name"
          class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary" />
      </div>
    </div>
    <div class="w-full md:w-1/2 px-4">
      <div class="mb-8">
        <label for="wp-custom-email" class="block text-sm font-medium text-dark dark:text-white mb-3"> Your Email
        </label>
        <input type="email" id="wp-custom-email" name="wp-custom-email" placeholder="Enter your email"
          class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary" />
      </div>
    </div>
    <div class="w-full px-4">
      <div class="mb-8">
        <label for="wp-custom-message" class="block text-sm font-medium text-dark dark:text-white mb-3"> Your Message
        </label>
        <textarea id="wp-custom-message" name="wp-custom-message" rows="5" placeholder="Enter your Message"
          class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary resize-none"></textarea>
      </div>
    </div>
    <div class="w-full px-4">
      <button type="submit" id="wp-custom-submit" name="wp-custom-submit"
        class="text-base font-medium text-white bg-primary py-4 px-9 hover:bg-opacity-80 hover:shadow-signUp rounded-md transition duration-300 ease-in-out">
        Submit Ticket
      </button>
    </div>
  </div>
</form>

          </div>
        </div>
        <div class="w-full lg:w-4/12 px-4">
          <div
            class="relative z-10 rounded-md bg-primary bg-opacity-[3%] dark:bg-opacity-10 p-8 sm:p-11 lg:p-8 xl:p-11 mb-5 wow fadeInUp"
            data-wow-delay=".2s
              ">
            <h3 class="text-black dark:text-white font-bold text-2xl leading-tight mb-4">Subscribe to receive future
              updates</h3>
            <p
              class="font-medium text-base text-body-color leading-relaxed pb-11 mb-11 border-b border-body-color border-opacity-25 dark:border-white dark:border-opacity-25">
              Lorem ipsum dolor sited Sed ullam corper consectur adipiscing Mae ornare massa quis lectus.
            </p>
            <form>
              <input type="text" name="name" placeholder="Enter your name"
                class="w-full border border-body-color border-opacity-10 dark:border-white dark:border-opacity-10 dark:bg-[#242B51] rounded-md py-3 px-6 font-medium text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary focus:border-opacity-100 mb-4" />
              <input type="email" name="email" placeholder="Enter your email"
                class="w-full border border-body-color border-opacity-10 dark:border-white dark:border-opacity-10 dark:bg-[#242B51] rounded-md py-3 px-6 font-medium text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary focus:border-opacity-100 mb-4" />
              <input type="submit" value="Subscribe"
                class="w-full border border-primary bg-primary rounded-md py-3 px-6 font-medium text-white text-base text-center outline-none cursor-pointer focus-visible:shadow-none hover:shadow-signUp hover:bg-opacity-80 transition duration-80 ease-in-out mb-4" />
              <p class="text-base text-body-color text-center font-medium leading-relaxed">No spam guaranteed, So please
                don’t send any spam mail.</p>
            </form>
            <div class="absolute top-0 left-0 z-[-1]">
              <svg width="370" height="596" viewBox="0 0 370 596" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_88:141" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="370"
                  height="596">
                  <rect width="370" height="596" rx="2" fill="#1D2144" />
                </mask>
                <g mask="url(#mask0_88:141)">
                  <path opacity="0.15" d="M15.4076 50.9571L54.1541 99.0711L71.4489 35.1605L15.4076 50.9571Z"
                    fill="url(#paint0_linear_88:141)" />
                  <path opacity="0.15" d="M20.7137 501.422L44.6431 474.241L6 470.624L20.7137 501.422Z"
                    fill="url(#paint1_linear_88:141)" />
                  <path opacity="0.1"
                    d="M331.676 198.309C344.398 204.636 359.168 194.704 358.107 180.536C357.12 167.363 342.941 159.531 331.265 165.71C318.077 172.69 318.317 191.664 331.676 198.309Z"
                    fill="url(#paint2_linear_88:141)" />
                  <g opacity="0.3">
                    <path
                      d="M209 89.9999C216 77.3332 235.7 50.7999 258.5 45.9999C287 39.9999 303 41.9999 314 30.4999C325 18.9999 334 -3.50014 357 -3.50014C380 -3.50014 395 4.99986 408.5 -8.50014C422 -22.0001 418.5 -46.0001 452 -37.5001C478.8 -30.7001 515.167 -45 530 -53"
                      stroke="url(#paint3_linear_88:141)" />
                    <path
                      d="M251 64.9999C258 52.3332 277.7 25.7999 300.5 20.9999C329 14.9999 345 16.9999 356 5.49986C367 -6.00014 376 -28.5001 399 -28.5001C422 -28.5001 437 -20.0001 450.5 -33.5001C464 -47.0001 460.5 -71.0001 494 -62.5001C520.8 -55.7001 557.167 -70 572 -78"
                      stroke="url(#paint4_linear_88:141)" />
                    <path
                      d="M212 73.9999C219 61.3332 238.7 34.7999 261.5 29.9999C290 23.9999 306 25.9999 317 14.4999C328 2.99986 337 -19.5001 360 -19.5001C383 -19.5001 398 -11.0001 411.5 -24.5001C425 -38.0001 421.5 -62.0001 455 -53.5001C481.8 -46.7001 518.167 -61 533 -69"
                      stroke="url(#paint5_linear_88:141)" />
                    <path
                      d="M249 40.9999C256 28.3332 275.7 1.79986 298.5 -3.00014C327 -9.00014 343 -7.00014 354 -18.5001C365 -30.0001 374 -52.5001 397 -52.5001C420 -52.5001 435 -44.0001 448.5 -57.5001C462 -71.0001 458.5 -95.0001 492 -86.5001C518.8 -79.7001 555.167 -94 570 -102"
                      stroke="url(#paint6_linear_88:141)" />
                  </g>
                </g>
                <defs>
                  <linearGradient id="paint0_linear_88:141" x1="13.4497" y1="63.5059" x2="81.144" y2="41.5072"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint1_linear_88:141" x1="28.1579" y1="501.301" x2="8.69936" y2="464.391"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint2_linear_88:141" x1="338" y1="167" x2="349.488" y2="200.004"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint3_linear_88:141" x1="369.5" y1="-53" x2="369.5" y2="89.9999"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint4_linear_88:141" x1="411.5" y1="-78" x2="411.5" y2="64.9999"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint5_linear_88:141" x1="372.5" y1="-69" x2="372.5" y2="73.9999"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="paint6_linear_88:141" x1="409.5" y1="-102" x2="409.5" y2="40.9999"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="white" />
                    <stop offset="1" stop-color="white" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== Contact Section End -->

  <!-- ====== All Scripts -->

  <script>
    // ==== pricing plan toggler
    let togglePlan = document.querySelector('#togglePlan');

    document.querySelector('.monthly').addEventListener('click', () => {
      togglePlan.checked = false;
    });
    document.querySelector('.yearly').addEventListener('click', () => {
      togglePlan.checked = true;
    });

    // ==== for menu scroll
    const pageLink = document.querySelectorAll('.menu-scroll');

    pageLink.forEach((elem) => {
      elem.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector(elem.getAttribute('href')).scrollIntoView({
          behavior: 'smooth',
          offsetTop: 1 - 60,
        });
      });
    });

    // section menu active

    function onScroll(event) {
      const sections = document.querySelectorAll('.menu-scroll');
      const scrollPos = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

      for (let i = 0; i < sections.length; i++) {
        const currLink = sections[i];
        const val = currLink.getAttribute('href');
        const refElement = document.querySelector(val);
        const scrollTopMinus = scrollPos + 73;
        if (refElement.offsetTop <= scrollTopMinus && refElement.offsetTop + refElement.offsetHeight > scrollTopMinus) {
          document.querySelector('.menu-scroll').classList.remove('active');
          currLink.classList.add('active');
        } else {
          currLink.classList.remove('active');
        }
      }
    }

    window.document.addEventListener('scroll', onScroll);
  </script>



<?php get_footer(); ?>

             
