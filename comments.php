<?php
/**
 * The template for displaying comments
 *
 * @package Rock_Star
 */

if ( post_password_required() ) {
	return;
}
?>
<style>
    /* Smooth transition for hidden sections */
    .smooth-reveal {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-out, opacity 0.5s ease-out;
    }
    .smooth-reveal.open {
        max-height: 2000px; /* Arbitrary large height */
        opacity: 1;
    }
    
    /* Blinking dots animation (Standardized) */
    .loading-dots:after { 
        content: "."; 
        animation: dots 1.5s steps(5, end) infinite; 
    } 
    @keyframes dots { 
        0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 
        40% { color: white; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0);} 
        60% { text-shadow: .25em 0 0 white, .5em 0 0 rgba(0,0,0,0);} 
        80%, 100% { text-shadow: .25em 0 0 white, .5em 0 0 white;}
    }
    
    /* Validation Borders - Force override */
    .border-red-500 { border-color: #ef4444 !important; }
    .border-blue-500 { border-color: #3b82f6 !important; }
</style>

<div id="comments" class="comments-area pt-[50px]">

	<?php
    // Logic to determine button text
    $comment_count = get_comments_number();
    $button_text = 'Leave a Comment';
    if ( $comment_count > 0 ) {
        $button_text = sprintf( 'View Comments (%s)', number_format_i18n( $comment_count ) );
    }
    ?>

    <!-- Toggle Button (Always visible if comments are allowed or there are comments) -->
    <?php if ( comments_open() || have_comments() ) : ?>
        <div class="flex flex-wrap gap-4 mb-8">
            <button id="toggle-view-comments" class="text-base font-medium text-white bg-primary py-4 px-9 hover:bg-opacity-80 hover:shadow-signUp rounded-md transition duration-300 ease-in-out">
                <?php echo esc_html( $button_text ); ?>
            </button>
        </div>
    <?php endif; ?>

    <!-- Toggle Section (Hidden by default, contains List + Form) -->
    <div id="comments-toggle-section" class="smooth-reveal relative" style="display: none;">
        
        <!-- Close Button -->
        <button id="close-comments-section" type="button" class="absolute top-4 right-0 z-50 flex items-center space-x-1 text-xs font-bold uppercase tracking-wider bg-gray-200 dark:bg-gray-700 text-body-color hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-full transition-all shadow-sm" aria-label="Hide Comments">
            <span>Hide</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <?php if ( have_comments() ) : ?>
            <h2 class="comments-title font-bold text-black dark:text-white text-2xl mb-8">
                <?php
                if ( '1' === $comment_count ) {
                    printf( esc_html__( 'One comment', 'rock-star' ), '<span>' . get_the_title() . '</span>' );
                } else {
                    printf( 
                        esc_html( _nx( '%1$s comment', '%1$s comments', $comment_count, 'comments title', 'rock-star' ) ),
                        number_format_i18n( $comment_count ),
                        '<span>' . get_the_title() . '</span>'
                    );
                }
                ?>
            </h2>
        <?php endif; ?>

        <div class="comment-list-wrapper space-y-8 mb-12">
            <?php
            if ( have_comments() ) {
                wp_list_comments(
                    array(
                        'style'       => 'div',
                        'short_ping'  => true,
                        'avatar_size' => 50,
                        'callback'    => 'rock_stars_comment_callback'
                    )
                );
            }
            ?>
        </div>
        
        <?php if ( ! comments_open() && have_comments() ) : ?>
            <p class="no-comments text-body-color"><?php esc_html_e( 'Comments are closed.', 'rock-star' ); ?></p>
        <?php endif; ?>

        <!-- Comment Form Section -->
        <?php if ( comments_open() ) : ?>
            <div class="comment-form-container pl-[16px] pr-[16px]">
                <!-- Manual Custom Form -->
                <div id="respond" class="mt-8 bg-primary bg-opacity-[3%] dark:bg-dark rounded-md p-8 sm:p-11">
                    
                    <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" id="commentform" class="comment-form" novalidate>
                        <div class="flex flex-wrap mx-[-16px]">
                            <?php if ( is_user_logged_in() ) : ?>
                                <div class="w-full px-4 mb-8">
                                    <p class="text-base text-body-color">
                                        Parsed as <a href="<?php echo get_edit_user_link(); ?>"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a>
                                    </p>
                                </div>
                            <?php else : ?>
                                <!-- Name Field -->
                                <div class="w-full md:w-1/2 px-4">
                                    <div class="mb-8">
                                        <label for="author" class="block text-sm font-medium text-dark dark:text-white mb-3">Your Name</label>
                                        <input id="author" name="author" type="text" placeholder="Enter your name" class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary" />
                                    </div>
                                </div>
                                
                                <!-- Email Field -->
                                <div class="w-full md:w-1/2 px-4">
                                    <div class="mb-8">
                                        <label for="email" class="block text-sm font-medium text-dark dark:text-white mb-3">Your Email</label>
                                        <input id="email" name="email" type="email" placeholder="Enter your email" class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary" />
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Custom Checkbox (Wrapping Label + Unique ID) -->
                             <?php if ( ! is_user_logged_in() ) : ?>
                             <div class="w-full px-4 mb-8">
                                <style>
                                    #custom-cookies-consent:checked ~ div svg { opacity: 1 !important; }
                                </style>
                                <label for="custom-cookies-consent" class="flex items-start cursor-pointer group select-none relative z-10">
                                    <!-- Changed ID to avoid conflict with standard WP scripts -->
                                    <input id="custom-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked="checked" class="sr-only peer">
                                    
                                    <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center bg-white dark:bg-[#242B51] border border-gray-300 dark:border-transparent rounded peer-checked:border-primary transition-all duration-200 mt-1 mr-3 group-hover:border-primary">
                                        <svg class="w-3.5 h-3.5 text-primary dark:text-white opacity-0 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    
                                    <span class="text-sm font-medium text-body-color dark:text-gray-300 leading-relaxed">
                                        Save my name and email in this browser for the next time I comment
                                    </span>
                                </label>
                            </div>
                            <?php endif; ?>

                            <!-- Message Field -->
                            <div class="w-full px-4">
                                <div class="mb-8">
                                    <label for="comment" class="block text-sm font-medium text-dark dark:text-white mb-3">Your Message</label>
                                    <textarea id="comment" name="comment" rows="5" placeholder="Enter your Message" class="w-full border border-transparent dark:bg-[#242B51] rounded-md shadow-one dark:shadow-signUp py-3 px-6 text-body-color text-base placeholder-body-color outline-none focus-visible:shadow-none focus:border-primary resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                             <div id="js-comment-error" class="w-full px-4 mb-4 text-red-500 text-sm hidden"></div>
                             <div class="w-full px-4 pb-4">
                                <?php comment_id_fields(); ?>
                                <button name="submit" type="submit" id="submit" class="inline-block rounded-md bg-primary py-4 px-9 text-center text-base font-medium text-white hover:bg-opacity-90 hover:shadow-signUp transition duration-300 ease-in-out cursor-pointer border-0 shadow-submit">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const viewBtn = document.getElementById('toggle-view-comments');
                    const toggleSection = document.getElementById('comments-toggle-section');
                    const closeBtn = document.getElementById('close-comments-section');

                    // Open logic
                    if (viewBtn && toggleSection) {
                        viewBtn.addEventListener('click', function() {
                            viewBtn.style.display = 'none';
                            toggleSection.style.display = 'block';
                            setTimeout(function() {
                                toggleSection.classList.add('open');
                            }, 10);
                        });
                    }

                    // Close logic
                    if (closeBtn && toggleSection && viewBtn) {
                        closeBtn.addEventListener('click', function() {
                            toggleSection.classList.remove('open');
                            setTimeout(function() {
                                toggleSection.style.display = 'none';
                                viewBtn.style.display = 'inline-block'; // Or 'block', depending on original css. Inline-block is likely safer for buttons.
                            }, 500); // 500ms match transition
                        });
                    }
                });
            </script>
        <?php endif; ?>

    </div><!-- #comments-toggle-section -->

	<?php if ( ! comments_open() && !is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<!-- Redundant closed check, but handled inside as well. -->
	<?php endif; ?>

</div><!-- #comments -->
