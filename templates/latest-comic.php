<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Templates
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press_Templates
 * @subpackage Latest_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
global $mp_options;
get_header(); ?>

<div id="container">
    <div id="content" role="main">
        <h2>Latest Comic</h2>
        <?php
        
            $args = array(
                'category__in'   => array($mp_options['latestcomic_cat']),
                'posts_per_page' => '1',
                'paged'          => get_query_var('paged'),
            );
            
            $comic_query = new WP_Query($args);

        ?>       

        <?php if ($comic_query->have_posts()) : while($comic_query->have_posts()) : ?>
        <?php $comic_query->the_post(); ?>

        <?php $cats = wp_get_post_categories(get_the_ID()); ?>
        <?php wp_comic_navigation($comic_query); ?>
            
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                <div class="entry_content">
                    <?php the_content(); ?>
                </div>

            <?php endwhile; ?>
            <?php comments_template( '', true ); ?>
        <?php endif;?>
        
    </div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>