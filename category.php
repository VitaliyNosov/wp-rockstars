<?php
/**
 * Category template (category.php)
 * Шаблон для вывода записей по категориям с использованием Bootstrap 3.3.5
 */

get_header(); // Подключаем header.php
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <header class="category-header page-header">
                <h1 class="category-title"><?php echo esc_html( get_single_cat_title( '', false ) ); ?></h1>
                <?php 
                $rock_stars_cat_description = category_description();
                if ( ! empty( $rock_stars_cat_description ) ) : 
                ?>
                    <div class="category-description well">
                        <?php echo wp_kses_post( $rock_stars_cat_description ); ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <?php if (have_posts()) : ?>
                <div class="row posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="col-md-4 col-sm-6">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('panel panel-default'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('class' => 'img-responsive')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="panel-heading">
                                    <h2 class="entry-title panel-title">
                                        <a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
                                    </h2>
                                </div>
                                
                                <div class="panel-body">
                                    <div class="entry-meta text-muted">
                                        <span class="date"><i class="glyphicon glyphicon-calendar"></i> <?php echo esc_html( get_the_date() ); ?></span>
                                        <span class="author"><i class="glyphicon glyphicon-user"></i> <?php echo esc_html( get_the_author() ); ?></span>
                                    </div>
                                    
                                    <div class="entry-summary">
                                        <?php echo esc_html( get_the_excerpt() ); ?>
                                    </div>
                                    
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-primary btn-sm"><?php echo esc_html__( 'Read more', 'rock-stars' ); ?> &raquo;</a>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <nav class="navigation pagination-container text-center">
                    <ul class="pagination">
                        <li><?php previous_posts_link( '&laquo; ' . esc_html__( 'Previous', 'rock-stars' ) ); ?></li>
                        <li><?php next_posts_link( esc_html__( 'Next', 'rock-stars' ) . ' &raquo;' ); ?></li>
                    </ul>
                </nav>
                
            <?php else : ?>
                <div class="alert alert-info">
                    <p><?php esc_html_e( 'No posts found in this category.', 'rock-stars' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); // Подключаем footer.php ?>