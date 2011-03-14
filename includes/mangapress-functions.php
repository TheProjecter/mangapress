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
    $mp_options['insert_nav']         = intval( $options['insert_nav'] );
    $mp_options['latestcomic_cat']    = intval( $options['latestcomic_cat'] );
    $mp_options['comic_front_page']   = intval( $options['comic_front_page'] );
    $mp_options['latestcomic_page']   = intval( $options['latestcomic_page'] );
    $mp_options['comic_archive_page'] = intval( $options['comic_archive_page'] );
    $mp_options['make_thumb']         = intval( $options['make_thumb'] );
    $mp_options['insert_banner']      =	intval( $options['insert_banner'] );
    $mp_options['banner_width']       =	intval( $options['banner_width'] );
    $mp_options['banner_height']      = intval( $options['banner_height'] );
    $mp_options['twc_code_insert']    = intval( $options['twc_code_insert'] );
    $mp_options['oc_code_insert']     = intval( $options['oc_code_insert'] );
    $mp_options['oc_comic_id']        = intval( $options['oc_comic_id'] );

    return serialize( $mp_options );

}

/**
 * Manga+Press Hook Functions
 */

/**
 * add_navigation_css()
 * is used to add CSS for comic navigation to <head> section
 * when the custom code option hasn't been specified. Called by: wp_head()
 *
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 * @since	0.5b
 * @todo Change this to external file and use wp_enqueue_style()
 * 
 */
function mpp_add_nav_css()
{
    wp_enqueue_style('mangapress-nav');
}
/**
 * add_header_info(). Called by:	wp_head()
 * 
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 * @since	0.5b
 *
 */
function mpp_add_header_info()
{
    echo "<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
}

/**
 * add_meta_info(). Called by:	wp_meta()
 * 
 * @since 1.0 RC1
 * 
 * @global bool $suppress_meta Optional @see $suppress_footer
 */
function mpp_add_meta_info(){
	global $suppress_meta;
	
	if (!$suppress_meta)
		echo "<li><a href=\"http://manga-press.silent-shadow.net\" title=\"".__('Powered by', 'mangepress')." Manga+Press ".MP_VERSION.", ".__('a revolutionary new web comic management system for Wordpress', 'mangapress')."\">Manga+Press</a></li>";
}

/**
 * mpp_add_comic_post(). Called by publish_post()
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference publish_post
 * @since 2.5
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
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

    $is_comic = intval($_POST);
    if (!add_post_meta($post_id, 'comic', $is_comic, true)) {
        update_post_meta($post_id, 'comic', $is_comic);
    }
//    var_dump(intval($_POST['is_comic'])); die();
    return $is_comic;
}
/**
 * delete_comic_post()
 * is used to delete comic from the comics DB table
 * when comic is deleted via Manage Posts or Edit Post
 *
 * @since	0.1b
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
 * edit_comic_post(). Called by edit_post()
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference edit_post
 * @since 2.6
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
 */
function mpp_edit_comic_post($id)
{
    global $mp_options, $wpdb;

    $cats = wp_get_post_categories($id);
    $value = (int)get_post_meta($id, 'comic', true);
    //
    // post has been edited, comic removed from comic categories...
    if ( !in_array($mp_options['latestcomic_cat'], $cats) && $value ) {
        $sql = $wpdb->prepare(
            "DELETE FROM {$wpdb->mpcomics} WHERE post_id='';",
            $id
        );
        $wpdb->query($sql);
        delete_post_meta($id, 'comic');

        return;

    } elseif (in_array($mp_options['latestcomic_cat'], $cats) && $value ) {
        if ( !is_comic($id) ) { // has meta value but if its not in the database, then add it
            $post = get_post($id);
            $sql = $wpdb->prepare(
                "INSERT INTO {$wpdb->mpcomics} (post_id, post_date)"
                . " VALUES ('%d', '%s') ;",
                $id, $post->post_date
            );

            $wpdb->query($sql);

            return;
        }
    }
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
 * filter_latest_comicpage()
 *
 * Makes changes to the_content() for Latest Comic Page. Hooked to the_content().
 * 
 * @since 2.5
 * 
 * @global object $wp Global WordPress query object.
 * @global array $mp_options Array containing Manga+Press options.
 * @todo Replace with a locate_template() routine.
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
 * filter_comic_archivepage()
 *
 * Makes changes to the_content() for Comic Archive Page. Hooked to the_content().
 * 
 * @since 2.6
 * 
 * @global object $wp Global WordPress query object.
 * @global array $mp_options Array containing Manga+Press options.
 * @todo Replace with a locate_template() routine
 */
function mpp_filter_comic_archivepage($content)
{
	global $mp_options, $wp;
	
//	$page = get_page( $mp_options['comic_archive_page'] );
//	if ( @$wp->query_vars['pagename'] === $page->post_name ) {
//		$parchives = '';
//		if ($mp_options['twc_code']) {
//			$recent_post = get_post( wp_comic_last() );
//			setuppost_date( $recent_post );
//
//			$parchives = "\n<!--Last Update: ".date('d/m/Y', strtotime($recent_post->post_date))."-->\n";
//		}
//		//
//		// Grab all available comic posts...
//		// Yes, this is sort of a "mini Loop"
//		$args = array( 'showposts'=>'10', 'cat'=>wp_comic_category_id(), 'orderby'=>'post_date' );
//		$posts = get_posts( $args );
//		if ( have_comics() ) :
//
//			$parchives .= "<ul class=\"comic-archive-list\">\n";
//
//			$c = 0;
//			foreach( $posts as $post) :	setup_postdata( $post );
//
//				$c++;
//				$parchives .= "\t<li class=\"list-item-$c\">".date('m-d-Y', strtotime( $post->post_date ) )." <a href=\"".get_permalink( $post->ID )."\">$post->post_title</a></li>\n";
//
//			endforeach;
//
//			$parchives .= "</ul>\n";
//
//		else:
//
//			$parchives = __("No comics found", 'mangapress');
//
//		endif;
//		$content = $parchives;
//	}
//
//	return $content;
	
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
 */
function mpp_comic_insert_navigation($template)
{
    global $mp_options, $wp_query;

    // new code here
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
 * comic_insert_banner()
 *
 * Inserts comic banner at the start of The Loop on the home page.
 * Hooked to loop_start.
 *
 * @since 2.5
 */
function mpp_comic_insert_banner()
{
    if ( is_home() || is_front_page() ){
        get_latest_comic_banner(true);
    }
}

/**
 * comic_insert_twc_update_code()
 *
 * Inserts a Last Update html comment at the start of The Loop on the either
 * the home page, the main comic page or the archive page. Hooked to loop_start.
 *
 * @since 2.5
 * @version 1.0
 */
function mpp_comic_insert_twc_update_code()
{
    if ( is_home() || is_comic_archive_page() ){
        $latest = wp_comic_last();
        $post_latest = get_post($latest);
        echo "\n<!--Last Update: ".date('d/m/Y', strtotime($post_latest->post_date))."-->\n";
    }
}
/**
 * mpp_comic_version()
 *
 * @since 2.0 beta
 *
 * echoes the current version of Manga+Press.
 */
function mpp_comic_version()
{	
    echo MP_VERSION;
}
?>