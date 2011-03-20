<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage Single_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

global $mp_options;
get_header(); ?>

<div id="content" class="hfeed">
<?php if (have_posts()) : while(have_posts()) : the_post(); ?>
    
    <?php wp_comic_navigation(); ?>
    <div id="post_<?php the_ID() ?>" <?php post_class('comic') ?>>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

        <?php if (has_post_thumbnail()): ?>
        <div class="comic">
            <?php the_post_thumbnail('comic-page'); ?>
        </div>
        <?php endif; ?>

        <div class="entry-content">
            <?php the_content(); ?>
        </div>
        
    </div>
    <?php endwhile; ?>

    <?php comments_template( '', true ); ?>
    
<?php endif;?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>