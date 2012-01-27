<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Templates
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press_Templates
 * @subpackage Archives
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

global $mp_options;
get_header(); ?>


<div id="primary">
    <div id="content" role="main">

        <h2>Comic Archives: <?php single_term_title(); ?></h2>
        <!-- need archives to be organized by series, then by issue, then by date -->
        <ul>
            <li>
                <h3><a href="<?php echo get_category_link($series) ?>"><?php echo $series->name ?></a></h3>
                <ul>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
                        
                    <?php endwhile; endif; ?>
                </ul>
            </li>
        </ul>

    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>