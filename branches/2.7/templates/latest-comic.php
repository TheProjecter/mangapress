<?php

global $mp_options;
get_header(); ?>

<div id="container">
    <div id="content" role="main">
        <h2>Latest Comic</h2>
        <?php
        
            $args = array(
                'category__in'   => array($mp_options['latestcomic_cat']),
                'posts_per_page' => '1',
                'paged'          => get_query_var('paged'),
            );

            if ($mp_options['group_comics']) {
                $tax_query = array(
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category',
                            'field' => 'id',
                            'terms' => array($mp_options['latestcomic_cat']),
                        ),
                        array(
                            'taxonomy'   => 'series',
                            'field'      => 'slug',
                            'terms'      => array('silent-shadow'),
                        ),
                    )
                );
            } else {
                $tax_query = array(
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category',
                            'field' => 'id',
                            'terms' => array($mp_options['latestcomic_cat']),
                        ),
                        array(
                            'taxonomy'   => 'series',
                        ),
                    )
                );

            }
            
            $comic_query = new WP_Query(array_merge($args, $tax_query));

        ?>       

        <?php if ($comic_query->have_posts()) : while($comic_query->have_posts()) : ?>
        <?php $comic_query->the_post(); ?>

        <?php $cats = wp_get_post_categories(get_the_ID()); ?>
        <?php wp_comic_navigation($comic_query); ?>
            
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