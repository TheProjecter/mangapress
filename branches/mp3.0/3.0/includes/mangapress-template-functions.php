<?
/**
 * 
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @since 0.1
 * 
 * This file contains all of the custom template tags for displaying comics and navigation properly.
 *
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
function is_comic($id = 0){
global $wpdb, $post;

	
	if ($id == 0)
	    $post_type = get_query_var('post_type');
	else
        $post_type = get_post_type($id);
		
    // short had for if / else;
    return $post_type == 'comic' ? true : false;


}
/** 
* is_comic_page()
*
* Checks to see if page is the latest comic page specified in Manga+Press Options.
*
* @since 1.0 RC1
*
* @global array $mp_options
* @global object $wp_query
* @return bool Returns true on success, false if page doesn't match.
*/
function is_comic_page(){
global $mp_options;

	if ( is_page( $mp_options['latestcomic_page'] ) )
        return true;
	else
        return false;
}
/** 
* is_comic_archive_page()
*
* Checks to see if page is the comic archive page specified in Manga+Press Options.
*
* @since 1.0 RC1
*
* @global array $mp_options
* @global object $wp_query
* @return bool Returns true on success, false if page doesn't match.
*/
function is_comic_archive_page(){
global $mp_options;

	if ( is_page( $mp_options['comic_archive_page'] ) )
        return true;
	else
        return false;
}

/**
 *
 * get_latest_comic_banner()
 *
 * Custom template tag
 * Displays comic banner
 *
 * @since 2.1
 *
 * @global array $mp_options
 * @param bool $nav. Whether or not to display comic navigation below banner.
 *
 * @return void
 */
function get_latest_comic_banner($nav = false) {
    global $mp_options, $mp;

        $latest = $mp->comics->last_comic;
        if ((int)$latest) {

            echo '<div class="comic-banner">
                        <h2><a href="'.get_permalink( $latest ).'" title="'.get_the_title( $latest ).'" class="new" rel="latest-comic">'.get_the_title( $latest ).'</a></h2>
                        <span class="comic-banner-wrap">
                            <span class="comic-banner-overlay">&nbsp;</span>' .
                        get_the_post_thumbnail($latest, 'comic-banner')
                        . '</span>';

            if ($nav) { wp_comic_navigation( $latest ); }

            echo "\n</div>";

        }
}

/**
 * Displays navigation for post specified by $post_id. The original method used four separate db queries to build the navigation.
 * This version only uses one query but is a little more complicated as a result.
 *
 * @since 0.1b
 *
 * @global array $mp_options Manga+Press Options array
 * @param int $post_id ID of the comic post that navigation is being generated for.
 * @param string $series String name of series taxonomy. Used if group_by_series is enabled.
 * @param bool $echo Whether or not to echo or return navigation.
 * @return string
 */
function wp_comic_navigation($post_id = 0, $series = '', $echo = true) {
global $mp;

    $posts = $mp->comics->get_all_comics($post_id, $series);

    $c = count( $posts ) - 1;

    $nav = $mp->comics->comic_navigation($post_id, $posts);

    if (!$echo)
        return $nav;
    else
        echo $nav;
		
}
/** 
 * Returns the value of $mp_options[latestcomic_page] for use in templates
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Latest Comic page
 */
function wp_comic_page_id() {
global $mp_options;

	return $mp_options['latestcomic_page'];
}
/** 
 * Returns the value of $mp_options[comic_archive_page] for use in templates
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return integer
 */
function wp_comic_archive_page_id() {
global $mp_options;

	return $mp_options['comic_archive_page'];
}
/**
 * Displays a recent comic thumbnail in the sidebar
 *
 * @since 2.0
 *
 * @return void
 *
 */
function wp_sidebar_comic() {
    global $mp;

    $latest = $mp->comics->last_comic;
    
    if ($latest) {
        echo   '<div class="comic-sidebar">'
             . '<a href="'.get_permalink( $ID )
             . '" title="'.__('Latest Comic','mangapress') . '">'
             . get_the_post_thumbnail($latest, 'comic-sidebar-image')
             . '</a></div>'."\n";

        echo   '<div class="comic-sidebar-link">'
             . '<a href="' . get_permalink( $ID )
             . '" title="' . __('Latest Comic','mangapress') . '">'
             . __('Latest Comic','mangapress').'</a></div>'."\n";
    }
}
?>