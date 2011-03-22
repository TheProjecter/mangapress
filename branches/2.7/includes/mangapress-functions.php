<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Manga_Press
 * @subpackage Core_Functions
 * @since 0.1b
 * 
 * Manga+Press plugin Functions
 * This is where the actual work gets done...
 * 
 */

/**
 * Updates multiple options from page-comic-options.php
 * 
 * @since 2.6
 *
 * Originally update_options. Was modified and renamed in Manga+Press 2.6
 *
 * @global array $mp_options
 * @param array $options
 * @return string
 */
function update_mangapress_options($options)
{

    global $mp_options;
	
    // validate string options...
    $nav_css_values  = array('default_css', 'custom_css');
    $order_by_values = array('post_date', 'post_id');

    //
    // if the value of the option doesn't match the correct values in the array, then
    // the value of the option is set to its default.
    if (in_array($mp_options['nav_css'], $nav_css_values)){
        $mp_options['nav_css'] = strval($options['nav_css']);
    } else {
        $mp_options['nav_css'] = 'default_css';
    }

    if (in_array($mp_options['order_by'], $order_by_values)) {
        $mp_options['order_by'] = strval($options['order_by']);
    } else {
        $mp_options['order_by'] = 'post_date';
    }
    
    //
    // Converting the values to their correct data-types should be enough for now...
    $mp_options['insert_nav']          = intval( $options['insert_nav'] );
    $mp_options['group_comics']        = intval( $options['group_comics'] );
    $mp_options['latestcomic_page']    = intval( $options['latestcomic_page'] );
    $mp_options['comic_archive_page']  = intval( $options['comic_archive_page'] );
    $mp_options['make_thumb']          = intval( $options['make_thumb'] );
    $mp_options['banner_width']        = intval( $options['banner_width'] );
    $mp_options['banner_height']       = intval( $options['banner_height'] );
    $mp_options['generate_comic_page'] = intval( $options['generate_comic_page']);
    $mp_options['comic_width']         = intval( $options['comic_width']);
    $mp_options['comic_height']        = intval( $options['comic_height']);

    return serialize( $mp_options );

}

/**
 * Manga+Press Hook Functions
 */

/**
 * mpp_add_nav_css()
 * 
 * Is used to add CSS for comic navigation to <head> section
 * when the custom code option hasn't been specified. Called by: wp_head()
 *
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 *
 * @since 0.5b
 * @return void
 */
function mpp_add_nav_css()
{
    wp_enqueue_style('mangapress-nav');
}
/**
 * add_header_info(). Called by:	wp_head()
 * 
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 * @since 0.5b
 * @return void
 */
function mpp_add_header_info()
{
    echo "<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
}

/**
 * mpp_add_comic_post(). Called by save_post()
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference publish_post
 * @since 2.5
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
 *
 * @return int|array
 */
function mpp_add_comic_post($post_id)
{
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (!wp_verify_nonce( $_POST['mangapress_nonce'], MP_FOLDER)) {
        return $post_id;
    }

    // verify if this is an auto save routine. If it is our form has not been
    // submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check permissions
    if ( 'post' == $_POST['post_type'] ) {
        if (!current_user_can('edit_post', $post_id )) {
            return $post_id;
        }
    }

    $is_comic = intval($_POST['is_comic']);    
    if (!add_post_meta($post_id, 'comic', $is_comic, true)) {
        update_post_meta($post_id, 'comic', $is_comic);
    }

    return $is_comic;
}

/**
 * delete_comic_post()
 * is used to delete comic from the comics DB table
 * when comic is deleted via Manage Posts or Edit Post
 *
 * @since 0.1b
 * @global object $wpdb Wordpress database object.
 * @param int $post_id Integer of post to be added to comics database.
 * @see	delete_post()
 * 
 */
function mpp_delete_comic_post($post_id)
{
    return delete_post_meta($post_id, 'comic');
}

/**
 * Filters comic posts from main loop.
 * 
 * @since 2.5
 * @global array $mp_options
 * @param object $query WordPress query object
 * @return object Modified version of WordPress query object
 */
function mpp_filter_posts_frontpage($query)
{
    global $mp_options;

    if ($query->is_front_page || $query->is_home) {
        $query->set(
            'category__not_in',
            array($mp_options['latestcomic_cat'])
        );
    }

    return $query;

}

/**
 * Handles display for the latest comic page.
 * 
 * @param string $template
 *
 * @global array $mp_options
 * @global object $wp_query
 *
 * @since 2.5
 * 
 * @return string|void
 */
function mpp_filter_latest_comic($template)
{
    global $mp_options, $wp_query;
    
    // new code here
    $object = $wp_query->get_queried_object();

    if ($object->ID == $mp_options['latestcomic_page']) {
        
        if ('' == locate_template(array('comics/latest-comic.php'), true)) {
            load_template(MP_ABSPATH . 'templates/latest-comic.php');
        }
        
    } else {

        return $template;

    }
}

/**
 * Turns taxonomies associated with comics into comic archives.
 *
 * @global object $wp_query
 * @param string $template
 *
 * @return void|string
 */
function mpp_series_template($template)
{
    global $wp_query;

    if ($wp_query->is_tax) {

        $object = $wp_query->get_queried_object();
        
        if ($object->taxonomy == 'series' || $object->taxonomy == 'issue'){

            if ('' == locate_template(array('comics/archives.php'), true)) {
                load_template(MP_ABSPATH . 'templates/archives.php');
            }

        } else {
            return $template;
        }
    } else {
       return $template; 
    }   
}

/**
 * filter_comic_archivepage()
 *
 * @param string $template
 *
 * @global object $wp Global WordPress query object.
 * @global array $mp_options Array containing Manga+Press options.
 *
 * @since 2.6
 * 
 * @return string|void
 */
function mpp_filter_comic_archivepage($template)
{
    global $mp_options, $wp_query;

    // new code here
    $object = $wp_query->get_queried_object();

    if ($object->ID == $mp_options['comic_archive_page']) {

        if ('' == locate_template(array('comics/comic-archive.php'), true)) {
            load_template(MP_ABSPATH . 'templates/comic-archive.php');
        }

    } else {

        return $template;

    }
	
}

/**
 * comic_insert_navigation()
 *
 * Inserts comic navigation at the beginning of The Loop. Hooked to loop_start
 * 
 * @since 2.5
 * 
 * @global object $post Wordpress post object.
 * @global int $id Post ID. Not used.
 * @global int $cat Category ID. Not used.
 * @global array $mp_options Array containing Manga+Press options.
 *
 * @return string|void
 */
function mpp_comic_insert_navigation($template)
{
    global $mp_options, $wp_query;

    $object = $wp_query->get_queried_object();
    $is_comic = get_post_meta($object->ID, 'comic', true);

    if ($is_comic) {

        if ('' == locate_template(array('comics/single-comic.php'), true)) {
            load_template(MP_ABSPATH . 'templates/single-comic.php');
        }

    } else {

        return $template;

    }

}

/**
 * Clone of WordPress function get_adjacent_post()
 * Handles looking for previos and next comics. Needed because get_adjacent_post()
 * will only handle category, and not other taxonomies.
 *
 * @since 2.7
 * 
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param string $previous Optional. Whether to retrieve next or previous post.
 *
 * @global object $post
 * @global object $wpdb
 * 
 * @return string
 */
function mpp_get_adjacent_comic($in_same_cat = false, $taxonomy = 'category', $excluded_categories = '', $previous = true)
{
    global $post, $wpdb;

    if ( empty( $post ) )
            return null;

    $current_post_date = $post->post_date;

    $join = '';
    $posts_in_ex_cats_sql = '';
    if ( $in_same_cat || !empty($excluded_categories) ) {
        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

        if ( $in_same_cat ) {
            $cat_array = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
            $join .= " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
        }

        $posts_in_ex_cats_sql = "AND tt.taxonomy = '{$taxonomy}'";
        if ( !empty($excluded_categories) ) {
            $excluded_categories = array_map('intval', explode(' and ', $excluded_categories));
            if ( !empty($cat_array) ) {
                    $excluded_categories = array_diff($excluded_categories, $cat_array);
                    $posts_in_ex_cats_sql = '';
            }

            if ( !empty($excluded_categories) ) {
                    $posts_in_ex_cats_sql = " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
            }
        }
    }

    $adjacent = $previous ? 'previous' : 'next';
    $op       = $previous ? '<' : '>';
    $order    = $previous ? 'DESC' : 'ASC';

    $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
    $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' AND p.post_parent = '$parent' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
    $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

    $query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
    $query_key = 'adjacent_post_' . md5($query);
    $result = wp_cache_get($query_key, 'counts');
    if ( false !== $result )
            return $result;

    $result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
    if ( null === $result )
            $result = '';

    wp_cache_set($query_key, $result, 'counts');

    return $result;
}

/**
 * Clone of WordPress function get_boundary_post(). Retrieves first and last
 * comic posts. Needed because get_boundary_post() will only handle category,
 * and not other taxonomies.
 * 
 * @since 2.7
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $start Optional. Whether to retrieve first or last post.
 *
 * @return object
 */
function mpp_get_boundary_comic($in_same_cat = false, $taxonomy = 'category', $excluded_categories = '', $start = true)
{
    global $post;

    if ( empty($post) || !is_single() || is_attachment() )
        return null;

    $cat_array = array();
    $excluded_categories = array();
    if ( !empty($in_same_cat) || !empty($excluded_categories) ) {
        if ( !empty($in_same_cat) ) {
            $cat_array = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
        }

        if ( !empty($excluded_categories) ) {
            $excluded_categories = array_map('intval', explode(',', $excluded_categories));

            if ( !empty($cat_array) )
                    $excluded_categories = array_diff($excluded_categories, $cat_array);

            $inverse_cats = array();
            foreach ( $excluded_categories as $excluded_category)
                    $inverse_cats[] = $excluded_category * -1;
            $excluded_categories = $inverse_cats;
        }
    }

    $categories = implode(',', array_merge($cat_array, $excluded_categories) );

    $order = $start ? 'ASC' : 'DESC';
    $post_query = array(
        'numberposts' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $categories,
                'operator' => 'IN'
            )
        ),
        'order' => $order,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    );
    
    return get_posts($post_query);
}

/**
 * mpp_comic_version()
 * echoes the current version of Manga+Press.
 * @since 2.0 beta
 * @return void
 */
function mpp_comic_version()
{	
    echo MP_VERSION;
}