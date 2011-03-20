<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press_Templates
 * @subpackage Series
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

global $mp_options;
get_header(); ?>

<div id="content" class="section archive">
    <h2>Comic Archives: <?php single_term_title(); ?></h2>
    <!-- need archives to be organized by series, then by issue, then by date -->
    <ul>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <li><?php the_time('m/d/Y'); ?> - <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

    <?php endwhile; endif; ?>
    </ul>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>