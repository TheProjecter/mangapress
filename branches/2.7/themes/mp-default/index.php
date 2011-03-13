<?php
/**
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
?>
<?php get_header(); ?>

        <div id="content" class="hfeed">
        <?php if (have_posts ()) : while(have_posts()) : the_post(); ?>
            <div id="post_<?php the_ID() ?>" <?php post_class() ?>>
                <h2 class="entry-title"><?php the_title(); ?></h2>
                <div class="entry-content">
                    <?php the_content();?>
                </div>
            </div>
               
        <?php endwhile; endif; ?>

        </div>
        <?php get_sidebar(); ?>

<?php get_footer(); ?>
