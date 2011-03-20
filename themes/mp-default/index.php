<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage Default
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
?>
<?php get_header(); ?>

    <div id="content" class="hfeed">

    <?php if (have_posts ()) : while(have_posts()) : the_post(); ?>
        <div id="post_<?php the_ID() ?>" <?php post_class() ?>>
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <span class="meta"><?php the_time('F jS') ?> | Posted In: <?php the_category(', ') ?></span>
            <div class="entry-content">
                <?php the_content();?>

                <span class="meta"><?php comments_popup_link() ?></span>
            </div>
        </div>

    <?php endwhile; ?>
    <?php else: ?>
        404
    <?php endif; ?>

    </div>
    <?php get_sidebar(); ?>

<?php get_footer(); ?>
