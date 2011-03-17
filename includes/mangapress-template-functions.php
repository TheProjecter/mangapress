<?php
/**
 * 
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @since 0.1
 * 
 */

/**
 * This file contains all of the custom template tags for displaying comics and navigation properly.
 *
 * The code for the comic navigation is based on the MyComic Wordpress plugin found at http://borkweb.com/story/wordpress-plugin-mycomic-browser
 * Manga+Press follows the same philosophy but automatically adds the required meta-data for the plugin to work properly.
 */

/**
 * have_comics()
 * 
 * Like have_posts(), returns true if there's comics;
 * false if there isn't any...
 * 
 * @since 1.0 RC1
 * 
 * @global object $wpdb
 * @global array $mp_options
 * @return bool Returns true if there are comic-containing posts.
 */
function have_comics()
{
    global $wpdb, $mp_options;

    $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->mpcomics.";");
    
    return (bool)$wpdb->get_col($sql);
}

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
    global $wpdb, $mp_options, $post;
	
    if ($id == 0) $id = $post->ID;
    $sql = $wpdb->prepare("SELECT $id FROM " . $wpdb->mpcomics . " WHERE post_id=$id;");
    return (bool)$wpdb->get_col($sql);
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
global $mp_options, $wp_query;

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
global $mp_options, $wp_query;

	if ( is_page( $mp_options['comic_archive_page'] ) )
		return true;
	else
		return false;
}
/** 
* is_series_cat()
*
* Checks to see if category is a series category
*
* @since 1.0 RC1
* @deprecated since 2.6
*
*/
function is_series_cat() {}
/** 
* is_comic_cat()
*
* Checks to see if category is a comic category
*
* @since 1.0 RC1
*
* @global integer $cat Not used.
* @global array $mp_options
* @global object $wp_query
* @return bool
*/
function is_comic_cat() {
global $cat, $mp_options, $wp_query;

	if ($wp_query->is_category) {
		$cat_obj = $wp_query->get_queried_object();  
		return  (bool)($cat_obj->term_id == $mp_options['latestcomic_cat']);
	} else {
		return false;
	}
}
/** 
* get_comic_post()
*
* grabs a comic post by ID and returns it as an OBJECT.
* @since 1.0 RC1
*
* @global object $comic_page
* @global datetime $post_date
* @global string $post_content
* @global string $post_title
* @global string $post_excerpt
* @param int $id
* @return object
*/
function get_comic_post($id) {
global $comic_page, $post_date, $post_content, $post_title, $post_excerpt;

	$comic_page = get_post($id, OBJECT);
	return $comic_page;
}
/** 
* get_latest_comic_banner()
*
* Custom template tag
* Displays comic banner
*
* @since 2.1
*
* @global objecy $wpdb
* @global array $mp_options
* @global string $post_excerpt
* @param bool $nav. Whether or not to display comic navigation below banner.
*/
function get_latest_comic_banner($nav = false) {
	global $wpdb, $mp_options, $post_excerpt;

	$latest = wp_comic_last();

	if ((int)$latest) {
		$child = get_posts( array( 'post_parent'=>$latest, 'post_type'=>'attachment', 'post_mime_type'=>'image', 'numberposts'=>1 ) );
		$image = wp_get_attachment_image_src( $child[0]->ID, 'full' );
		get_comic_post ( $latest );
?>
<div class="comic-banner">
	<h2><a href="<?php echo get_permalink( $latest )?>" title="<?php echo get_the_title( $latest )?>" class="new" rel="latest-comic"><?php echo get_the_title( $latest )?></a></h2>
	<span class="comic-banner-wrap">
        <span class="comic-banner-overlay"></span>
        <img src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/mangapress/includes/mangapress-timthumb.php?src=<?=$image[0]?>&amp;w=<?=$mp_options['banner_width']?>&amp;h=<?=$mp_options['banner_height']?>&amp;zc=1" class="comic-banner-image" title="<?php echo get_the_title( $latest )?>" alt="<?php echo get_the_title( $latest )?>" />
	</span>
<?php if ($nav) { wp_comic_navigation( $latest ); } ?>
</div>
<?php
	}
}
/** 
 * wp_comic_first()
 *
 * Retrieves the first comic posted.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_first(){
global $wpdb, $mp_options;
	
	$mp_options['order_by'] = ($mp_options['order_by'])?$mp_options['order_by']:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " ORDER BY ".$mp_options['order_by']." ASC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}

}
/** 
 * wp_comic_last()
 *
 * Retrieves the last (most recent) comic posted.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_last(){
global $wpdb, $mp_options;
	
	$mp_options['order_by'] = ($mp_options['order_by'])?$mp_options['order_by']:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " ORDER BY ".$mp_options['order_by']." DESC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}

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
function wp_comic_navigation($query = null, $echo = true)
{
    if (is_null($query)) { 
        global $wp_query, $mp_options;
        
        $query = $wp_query;
        //$query = new WP_Query();
        //var_dump($query);
        //
        // because we use $wp_query here, we have to check for certain
        // parameters before continuing.
        $is_comic     = get_post_meta($wp_query->post->ID, 'comic', true);
        $is_comic_cat = ($query->query_vars['cat'] == $mp_options['latestcomic_cat']);

        if ($query->is_category && $is_comic_cat) {
            //global $paged;
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
/** 
 * wp_comic_next()
 *
 * Retrieves the comic after the current comic specified by $post_id
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @param int $post_id
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_next($post_id) {
global $wpdb, $mp_options;

	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " WHERE post_id>$post_id ORDER BY ".$mp_options['order_by']." ASC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}
}
/** 
 * wp_comic_previous()
 *
 * Retrieves previous comic before the current comic specified by $post_id
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @param int $post_id
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_previous($post_id) {
global $wpdb, $mp_options;

	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " WHERE post_id<$post_id ORDER BY ".$mp_options['order_by']." DESC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}
}
/** 
 * wp_comic_category_id()
 *
 * Returns the value of $mp_options[latestcomic_cat] for use in templates
 *
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Latest Comic catergory
 */
function wp_comic_category_id() {
global $mp_options;

	return $mp_options['latestcomic_cat'];
}
/** 
 * wp_comic_page_id()
 *
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
 * wp_comic_archive_page_id()
 *
 * Returns the value of $mp_options[comic_archive_page] for use in templates
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Comic Archives page
 */
function wp_comic_archive_page_id() {
global $mp_options;

	return $mp_options['comic_archive_page'];
}
/**
 * wp_sidebar_comic()
 *
 * Displays a recent comic thumbnail in the sidebar
 *
 * @since 2.0
 */
function wp_sidebar_comic() {
	
	$ID = wp_comic_last();
	if ($ID) {
		$images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $ID );
		foreach( $images as $imageID => $imagePost )
			$image = wp_get_attachment_metadata( $imageID );
			$imgurl = wp_get_attachment_thumb_url( $imagePost->ID );
			$res = getimagesize( $imgurl );
			echo '<div class="comic-sidebar"><a href="'.get_permalink( $ID ).'" title="'.__('Latest Comic','mangapress').'"><img src="'.$imgurl.'" '.$res[3].' style="border: none; " alt="" /></a></div>'."\n";
			echo '<div class="comic-sidebar-link"><a href="'.get_permalink( $ID ).'" title="'.__('Latest Comic','mangapress').'">'.__('Latest Comic','mangapress').'</a></div>'."\n";
	}
}
?>