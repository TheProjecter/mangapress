<?php
/**
 * @package Manga_Press
 * @subpackage Includes
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Includes
 * @subpackage Manga_Press_Template_Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * is_comic()
 *
 * Used to detect if post contains a comic.
 * @since 0.1
 *
 * @global object $wpdb
 * @global array $mp_options
 * @global object $post
 * @return bool Returns true if post contains a comic, false if not.
 */
function is_comic($id = 0)
{
    if (!$id) {
        global $post;
        $id = $post->ID;
    }

    $is_comic = get_post_meta($id, 'comic', true);

    return (bool)$is_comic;
}

/**
 * @since 1.0 RC1
 * 
 * @global array $mp_options
 * @return bool 
 */
function is_comic_page()
{
    global $mp_options, $wp_query;    
        
    return ($wp_query->is_page && ($wp_query->queried_object_id == $mp_options['latestcomic_page']));
    
}

/**
 * 
 * @since 1.0 RC1
 *
 * @global array $mp_options
 * @return bool
 */
function is_comic_archive_page()
{
    global $mp_options, $wp_query;

    $is_comic_archive_page
        = ($wp_query->is_page && ($wp_query->queried_object_id
                                    == $mp_options['comic_archive_page']));
    
    return $is_comic_archive_page;
    
}

/**
 *
 * @global array $mp_options
 * @return bool
 */
function is_comic_cat()
{
    global $mp_options, $wp_query;    

    $is_comic_cat
        = ($wp_query->is_category && ($wp_query->queried_object_id
                                        == $mp_options['latestcomic_cat']));
    
    return $is_comic_cat;
}

/** 
 * wp_comic_navigation()
 *
 * Displays navigation for post specified by $post_id.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @param int $post_id ID of the comic post.
 * @param bool $banner_nav Not used.
 * @param bool $echo Specifies whether to echo comic navigation or return it as a string
 * @return string Returns navigation string if $echo is set to false.
 */
function wp_comic_navigation(WP_Query $query = null, $echo = true)
{
    global $mp_options;

    if (is_null($query)) { 
        global $wp_query;
        
        $query = $wp_query;
        //
        // because we use $wp_query here, we have to check for certain
        // parameters before continuing.
        $is_comic     = get_post_meta($wp_query->post->ID, 'comic', true);
        $is_comic_cat = ($query->query_vars['cat'] == $mp_options['latestcomic_cat']);

        if ($query->is_category && $is_comic_cat) {
            $query->set('posts_per_page', '1');
            //$query->set('paged', get_query_var('paged'));
            ;
        } elseif ($query->is_single && $is_comic) {
            global $post;

            $comic_cat_ID = $mp_options['latestcomic_cat'];

            if ($mp_options['group_comics']) {
                $next_post  = mpp_get_adjacent_comic(true, 'series', null, false);
                $prev_post  = mpp_get_adjacent_comic(true, 'series', null, true);
                $last_post  = mpp_get_boundary_comic(true, 'series', null, false);
                $first_post = mpp_get_boundary_comic(true, 'series', null, true);
            } else {
                $next_post  = mpp_get_adjacent_comic(true, 'category', null, false);
                $prev_post  = mpp_get_adjacent_comic(true, 'category', null, true);
                $last_post  = mpp_get_boundary_comic(true, 'category', null, false);
                $first_post = mpp_get_boundary_comic(true, 'category', null, true);
            }


            $current_page = $post->ID; // use post ID this time.
            
            $next_page = is_null($next_post->ID)
                       ? $current_page : $next_post->ID;
            
            $prev_page = is_null($prev_post->ID)
                       ? $current_page : $prev_post->ID;
            
            $last      = is_null($last_post[0]->ID)
                       ? $current_page : $last_post[0]->ID;
            
            $first     = is_null($first_post[0]->ID)
                       ? $current_page : $first_post[0]->ID;
                    
            $first_url = get_permalink($first);
            $last_url  = get_permalink($last);
            $next_url  = get_permalink($next_page);
            $prev_url  = get_permalink($prev_page);

        } else {
            return false;
        }
    } else {
        
        if ($mp_options['group_comics']) {
            $term = wp_get_object_terms($query->post->ID, 'series');
            $query->set(
                'tax_query',
                array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'category',
                        'field' => 'id',
                        'terms' => array($mp_options['latestcomic_cat']),
                    ),
                    array(
                        'taxonomy'   => 'series',
                        'field'      => 'slug',
                        'terms'      => $term[0]->slug,
                    ),
                )
            );
            $query->get_posts();
        }
        // we'll use WordPress's paging system to generate the required navigation
        $first     = $query->max_num_pages; // last is most recent
        $last      = (float)1;
        $num_pages = $query->max_num_pages;

        //
        // Current page will help us determine the previous and next pages
        $paged        = $query->get('paged');
        $current_page = ($paged == 0) ? $last : $paged;
        $next_page    = ($current_page == $last) ? $last : $current_page - 1;
        $prev_page    = ($current_page == $first) ? $first : $current_page + 1;

        $first_url = get_pagenum_link($first);
        $last_url = get_pagenum_link($last);
        $next_url = get_pagenum_link($next_page);
        $prev_url = get_pagenum_link($prev_page);
    }
    
    //
    // Here, we start processing the urls.
    // Let's do first page first.
    $first = ($first == $current_page)
           ? '<span class="comic-nav-span">' . __('First', 'mangapress') . '</span>'
           : '<a href="' . $first_url . '">' . __('First', 'mangapress') . '</a>';


    $last = ($last == $current_page)
           ? '<span class="comic-nav-span">' . __('Last', 'mangapress') . '</span>'
           : '<a href="' . $last_url . '">'. __('Last', 'mangapress') . '</a>';


    $next = ($next_page == $current_page)
           ? '<span class="comic-nav-span">' . __('Next', 'mangapress') . '</span>'
           : '<a href="' . $next_url . '">'. __('Next', 'mangapress') . '</a>';

    $prev = ($prev_page == $current_page)
           ? '<span class="comic-nav-span">' . __('Prev', 'mangapress') . '</span>'
           : '<a href="' . $prev_url . '">'. __('Prev', 'mangapress') . '</a>';
    
    $navigation='
        <div class="comic-navigation">
            <ul class="comic-nav">
                <li class="comic-nav-first">'.$first.'</li>
                <li class="comic-nav-prev">'.$prev.'</li>
                <li class="comic-nav-next">'.$next.'</li>
                <li class="comic-nav-last">'.$last.'</li>
            </ul>
        </div>
    ';

    if ($echo){
        echo $navigation;
    } else {
        return $navigation;
    }
		
}
