<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage Latest_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
global $mp_options;
get_header(); ?>

<div id="content" class="hfeed latest-comic">
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
    <div id="post_<?php the_ID() ?>" <?php post_class('comic') ?>>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

        <?php if (has_post_thumbnail()): ?>
        <div class="comic">
            <?php the_post_thumbnail('comic-page'); ?>
        </div>
        <?php else : ?>
        <div class="comic">
            <?php the_comic_thumbnail(); ?>
        </div>
        <?php endif; ?>

        <div class="entry-content">
            <?php
                $allowed_tags = "<em><strong><u><strikethrough><ol><ul><li><p><blockquote><q>";
                $stripped_html = strip_tags($post->post_content, $allowed_tags);

                echo apply_filters('the_content', $stripped_html);
            ?>
        </div>

        <?php endwhile; ?>
        <?php comments_template( '', true ); ?>
        </div>
    <?php endif; ?>
    
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>