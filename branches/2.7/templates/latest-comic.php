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

<div id="primary">
    <div id="content" role="main">
        <h2>Latest Comic</h2>
        <?php
        
            $args = array(
                'post_type'      => 'mangapress_comic',
                'posts_per_page' => '1',
                'paged'          => get_query_var('paged'),
            );
            
            $comic_query = new WP_Query($args);
        ?>       

        <?php if ($comic_query->have_posts()) : while($comic_query->have_posts()) : ?>
        <?php $comic_query->the_post(); ?>
        

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <nav id="nav-comic-single">
                <?php wp_comic_navigation(); ?>
            </nav>
            
            <header class="entry-header">
                <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
            </header>
            
            <div class="entry-content">
                <?php if (has_post_thumbnail()): ?>
                <div class="comic">
                    <?php the_post_thumbnail('comic-page'); ?>
                </div>
                <?php endif; ?>
                <?php the_content(); ?>
            </div>
            
            <?php comments_template( '', true ); ?>    
        </article>
        <?php endwhile; endif;?>
        
    </div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>