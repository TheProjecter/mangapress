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
<div id="primary">
        <div id="content" role="main">
        <?php if (have_posts()) : while(have_posts()) :  the_post(); ?>
        

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <nav id="nav-comic-single">
                    <?php mangapress_comic_navigation(); ?>
                </nav>
                
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>
                
                <div class="entry-content">
                    <?php if (has_post_thumbnail()): ?>
                    <div class="comic">
                        <?php the_post_thumbnail('comic-page'); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php the_content(); ?>
                </div>
                
            </article>
            
            <?php comments_template( '', true ); ?>
            
            <?php endwhile; ?>
        <?php endif;?>
        
    </div>

</div>
<?php get_footer(); ?>