<?php

global $mp_options;
get_header(); ?>

<div id="container">
    <div id="content" role="main">
        <?php if (have_posts()) : while(have_posts()) : ?>
        <?php the_post(); ?>
        <?php wp_comic_navigation(); ?>

        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                <div class="entry_content">
                    <?php the_content(); ?>
                </div>

            <?php endwhile; ?>
        <?php endif;?>

    </div>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>