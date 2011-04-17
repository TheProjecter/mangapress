<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Templates
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press_Templates
 * @subpackage Single_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

global $mp_options;
get_header(); ?>

<div id="container">
    <div id="content" role="main">
        <?php if (have_posts()) : while(have_posts()) : ?>
        <?php the_post(); ?>
        <?php wp_comic_navigation(); ?>

        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                <div class="entry_content">
                    <?php if (has_post_thumbnail()): ?>
                    <div class="comic">
                        <?php the_post_thumbnail('comic-page'); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php the_content(); ?>
                </div>

            <?php endwhile; ?>
        <?php endif;?>
        <?php comments_template( '', true ); ?>
    </div>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>