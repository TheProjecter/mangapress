<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Templates
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press_Templates
 * @subpackage Comic_Archive
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

global $mp_options;
get_header(); ?>

<div id="container">
    <div id="content" role="main">

        <h2>Comic Archives</h2>
        <!-- need archives to be organized by series, then by issue, then by date -->
        <ul>
        <?php
            $series_tax = get_terms('series');

            if (!empty($series_tax)):
                foreach ($series_tax as $series) :
        ?>

            <li>
                <h3><a href="<?php echo get_category_link($series) ?>"><?php echo $series->name ?></a></h3>
                <ul>
                <?php
                    $parent_issue = get_term_by('slug', $series->slug, 'issue');
                    if ($parent_issue) :
                        $issues_tax = get_terms('issue', array('child_of' => $parent_issue->term_id));
                        foreach($issues_tax as $issue) :
                ?>
                    <li>
                        <h4><a href="<?php echo get_category_link($issue); ?>"><?php echo $issue->name ?></a></h4>
                        <?php

                            $args = array(
                                'posts_per_page' => -1,
                                'tax_query'      => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => 'category',
                                        'field' => 'id',
                                        'terms' => array($mp_options['latestcomic_cat']),
                                    ),
                                    array(
                                        'taxonomy'   => 'series',
                                        'field'      => 'slug',
                                        'terms'      => $series->slug,
                                    ),
                                    array(
                                        'taxonomy'   => 'issue',
                                        'field'      => 'slug',
                                        'terms'      => $issue->slug,
                                    ),
                                )
                            );
                            $archive_query = new WP_Query($args);
                            if ($archive_query->have_posts()) :
                        ?>
                        <ul>
                        <?php while($archive_query->have_posts()) : $archive_query->the_post(); ?>
                            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
                        <?php endwhile; ?>
                        </ul>
                        <?php endif;?>
                    </li>
                <?php endforeach;  ?>
                <?php else : ?>
                    <?php

                        $args = array(
                            'posts_per_page' => -1,
                            'tax_query'      => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'category',
                                    'field' => 'id',
                                    'terms' => array($mp_options['latestcomic_cat']),
                                ),
                                array(
                                    'taxonomy'   => 'series',
                                    'field'      => 'slug',
                                    'terms'      => $series->slug,
                                ),
                            )
                        );
                        $archive_query = new WP_Query($args);
                        if ($archive_query->have_posts()) :
                    ?>
                    <?php while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
                    <?php endwhile; ?>
                    <?php endif;?>
                <?php endif;?>
                </ul>
            </li>

        <?php
            endforeach;
        else: // then pull from Comics
            $args = array(
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'id',
                        'terms' => array($mp_options['latestcomic_cat']),
                    ),
                )
            );
            $archive_query = new WP_Query($args);

            if ($archive_query->have_posts()) : while ($archive_query->have_posts()) : $archive_query->the_post();
            ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
            <?php
            endwhile; endif;
        endif;
        ?>
        </ul>

    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>