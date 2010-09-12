<?php
/**
 * @subpackage Manga_Press_Posts
 *
 * handles registering the Comics custom post-type, any attached taxonomies
 */
class Manga_Press_Posts {
    /**
     *
     * @var <type>
     */
    protected $_last_comic;
    /**
     *
     * @var <type>
     */
    protected $_first_comic;
    /**
     *
     * @var <type>
     */
    protected $_all_comics;

    /**
     *
     * @var <type> 
     */
    public $last_comic;

    /**
     *
     * @var <type> 
     */
    public $all_comics;

    public function  __construct() {
        $labels = array(
            'name'               => __('Comics', MP_DOMAIN), //- general name for the post type, usually plural. The same as, and overridden by $post_type_object->label
            'singular_name'      => __('Comic', MP_DOMAIN), //- name for one object of this post type. Defaults to value of name
            'add_new'            => __('Add New Comic', MP_DOMAIN), //- the add new text. The default is Add New for both hierarchical and non-hierarchical types. When internationalizing this string, please use a gettext context matching your post type. Example: _x('Add New', 'product');
            'add_new_item'       => __('Add New Comic', MP_DOMAIN), //- the add new item text. Default is Add New Post/Add New Page
            'edit_item'          => __('Edit Comic', MP_DOMAIN), //- the edit item text. Default is Edit Post/Edit Page
            'new_item'           => __('New Comic', MP_DOMAIN), //- the new item text. Default is New Post/New Page
            'view_item'          => __('View Comic', MP_DOMAIN), // - the view item text. Default is View Post/View Page
            'search_items'       => __('Search Comics', MP_DOMAIN), // - the search items text. Default is Search Posts/Search Pages
            'not_found'          => __('Comic not found', MP_DOMAIN), // - the not found text. Default is No posts found/No pages found
            'not_found_in_trash' => __('Comic not found in trash', MP_DOMAIN), // - the not found in trash text. Default is No posts found in Trash/No pages found in Trash
        );

        register_post_type(
             'comic',
             array(  'labels'              => $labels,
                     'public'              => true,
                     'exclude_from_search' => false,
                     'singular_label'      => __('Comic', MP_DOMAIN),
                     'menu_position'       => 5,
                     'taxonomies'          => array(
												'category',
												'series',
                            ),
                     'capability'          => 'comic',
                     'capabilities'        => array(
                                                'edit_comic',
                                                'edit_other_comics',
                                                'publish_comics',
                                                'read_comics',
                                                'read_private_comics',
                                                'delete_comics',
                            ),
                     'supports'            => array(
                                                'thumbnail',
                                                'custom-fields',
                                                'editor',
                                                'author',
                                                'title',
                                                'comments',
                            ),
                     'rewrite'             => array('slug' => 'comic'),
                     )

                  );

        register_taxonomy( 'series', array('characters', 'comic'),
            array(
                    'hierarchical' => true,
                    'label' => __('Series &amp; Chapters', MP_DOMAIN),
                    'query_var' => 'series',
                    'rewrite' => array('slug' => 'series' )
                )
        );
        /*
         * 
         */
        add_action('save_post', array( &$this, 'add_comic_thumbnail' ));


        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', array(&$this, 'comics_headers'));
        add_filter('manage_edit-comic_columns', array(&$this, 'comics_columns'));

        $this->get_boundary_comics(); // grab and cache...
        $this->get_all_comics(); // grab and cache...
    }

    /**
     * Sets the attachment as the post thumbnail.
     *
     * @param integer $post_id Post Id
     * @return integer
     */
    function add_comic_thumbnail($post_id) {

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;


        if (get_post_type($post_id) == 'comic'
                && !(did_action('trash_to_publish') || did_action('publish_to_trash'))
                && current_theme_supports('post-thumbnails')) {

            if (!has_post_thumbnail($post_id)) {
                $ancestors
                    = get_posts(
                        array(
                            'post_type'      => 'attachment',
                            'post_mime_type' => 'image',
                            'post_parent'    => $post_id,
                            'numberposts'    => 1,
                        )
                    );

                if (isset($ancestors))
                    update_post_meta($post_id, '_thumbnail_id', $ancestors[0]->ID);

            }
        }

        return $post_id;
    }

    public function update_thumbnails() {
        global $_wp_additional_image_sizes;
        check_admin_referer('mangapress-thumbnails-update');

        // get all comic posts
        $comic_posts = get_posts(array('post_type' => 'comic', 'numberposts' => -1));
        $thumbnails_updated = false;

        // get all attachments of comic posts
        foreach($comic_posts as $comic_post) {

            // check for _thumbnail_id meta
            // if there is no thumbnail, then we set it...
            $thumbnail_exists = intval(has_post_thumbnail($comic_post->ID));

            // if there is no thumbnail, then we set it...
            if ($thumbnail_exists) {

                $message .= "&#149; Thumbnails already exist for comic post " . $comic_post->ID . ". <br />";

            } else {


                // but first, we have to see if attachments exist...
                $attachment
                    = get_posts(
                        array(
                            'post_type'      => 'attachment',
                            'post_mime_type' => 'image',
                            'post_parent'    => $comic_post->ID,
                            'numberposts'    => 1,
                        )
                    );

                // if attachments do exist, then we want to process them
                if (isset($attachment[0])) {

                    $wp_upload_dir = wp_upload_dir( date('Y-m', strtotime($attachment[0]->post_date))  );

                    $img_path = $wp_upload_dir['basedir'] . '/' .
                                get_post_meta( $attachment[0]->ID, '_wp_attached_file', true );

                    $meta = wp_generate_attachment_metadata( $attachment[0]->ID, $img_path );

                    if ((bool)$meta) {
                        wp_update_attachment_metadata( $attachment[0]->ID, $meta );
                        $message .= "&#149; Thumbnail generation for Attachment "
                                 . $attachment[0]->ID . " succeeded.<br />";

                        $thumbnails_updated = true;

                    } else {
                        $message .= "&#149; Thumbnail generation for Attachment "
                                 . $attachment[0]->ID . " failed. Please check your permissions.<br />";
                    }

                }
				// because this all started with needing a post thumbnail, we set it here
				if ($thumbnails_updated)
					update_post_meta($comic_post->ID, '_thumbnail_id', $attachment[0]->ID);

            }
        }

        $status['message'] = $message;
        $status['updated'] = $thumbnails_updated;

        return $status;
    }
    /**
     * mpp_custom_columns()
     *
     * @since 2.7
     *
     */
    function comics_headers($column) {
    global $post;

            if ("ID" == $column) {
                    echo $post->ID;
            } elseif ("author" == $column) {
                    echo $post->post_author;
            } elseif ("post_date" == $column) {
                    echo date( "Y/m/d", strtotime($post->post_date) );
            } elseif ("series" == $column) {
                    $series = wp_get_object_terms( $post->ID, 'series' );
                    $series_html = array();
                    foreach ($series as $s)
                        array_push($series_html, '<a href="'.get_term_link($s->slug, 'series').'">'.$s->name."</a>");

                    echo implode($series_html, ", ");

            } elseif ("thumbnail" == $column) {
                    $images = get_children( array('post_mime_type'=>'image', 'post_parent'=>$post->ID));
                    $upload_dir = wp_upload_dir();

                    if ($images) {
                            foreach( $images as $imageID => $imagePost ) {

                                    $image = wp_get_attachment_metadata( $imageID );
                                    $file = $upload_dir['baseurl'].'/'.$image['file'];

                                    echo '<img src="'.$file.'" '.$image['hwstring_small'].' style="border: none; " alt="" />'."\n";
                            }
                    } else {
                            echo "No image";
                    }

            } elseif ("description" == $column) {
                    echo $post->post_excerpt;
            }
    }
    /**
     * mpp_comic_columns()
     *
     * @since 2.7
     *
     */
    function comics_columns($columns) {

            $columns = array(
                    "cb"          => "<input type=\"checkbox\" />",
                    "thumbnail"   => "Thumbnail",
                    "title"       => "Comic Title",
                    "series"      => "Series &amp; Chapters",
                    "post_date"   => "Date",
                    "description" => "Description",
                    "author"      => "Author",
                    "comments"    => "Comments",
            );
            return $columns;

    }
    /**
     *
     * @global <type> $wpdb
     * @global array $mp_options
     */
    private function get_boundary_comics() {
        global $wpdb, $mp_options;
        
        $mp_options['order_by'] = ($mp_options['order_by']) ?
                                  $mp_options['order_by'] : 'ID';

        $sql = $wpdb->prepare(
                    "SELECT ID FROM "
                    . $wpdb->posts
                    . " WHERE post_type='comic' AND "
                    . "post_status='publish' ORDER BY "
                    . $mp_options['order_by'] . " DESC LIMIT 1;"
                );

        $last = $wpdb->get_results($sql);
        $this->last_comic = $last[0]->ID;

        $sql = $wpdb->prepare(
                    "SELECT ID FROM "
                    . $wpdb->posts
                    . " WHERE post_type='comic' "
                    . "AND post_status='publish' ORDER BY "
                    . $mp_options['order_by']
                    . " ASC LIMIT 1;"
                );

        $first = $wpdb->get_results($sql);
        $this->first_comic = $first[0]->ID;

    }
    /**
     *
     * @global <type> $mp_options
     * @param <type> $post_id
     * @param <type> $series
     * @return <type>
     */
    function get_all_comics($post_id = 0, $series = ''){
        global $mp_options;
        /*
         * if group by series is enabled AND $series is empty...
         */
        if ($mp_options['group_comics']) {
                $group  = wp_get_object_terms( $post_id, 'series' );
                
                if (!empty($group))
                    $series = $group[0]->slug;
        }

        $this->all_comics = get_posts(
                array(
                    'numberposts' => -1,
                    'post_type'   => 'comic',
                    'order'       => 'ASC',
                    'orderby'     => $mp_options['order_by'],
                    'series'      => $series,
                    )
                );

        return $this->all_comics;
    }
    /**
     *
     * @param <type> $post_id
     * @param <type> $posts
     * @return string
     */
    function comic_navigation($post_id, $posts) {

        $c = count( $posts ) - 1;

        /*
         * $curcomic is blank before being packed into an array and passed to array_walk
         * values are returned via reference using $ret
         */
        $current_comic = array('post_id'=>$post_id, 'curcomic'=>'');

        $ret = $this->comic_nav_walker($posts, $current_comic);

        extract( $ret ); // extract $curcomic and $post_id, which contain the new values from mpp_comicnav_walker()

        /*
         * now, using $curcomic, let's find the next comic and the previous comic.
         */
        ($curcomic != 0) ? $prev = $curcomic - 1 : $prev = 0;
        ($curcomic != $c) ? $nxt = $curcomic + 1 : $nxt = $c;

        /*
         * it's easier this way, trust me...
         */
        $next     = $posts[$nxt]->ID;
        $last     = $posts[$c]->ID;
        $first    = $posts[0]->ID;
        $previous = $posts[$prev]->ID;

        unset( $posts ); // free some memory, we don't need $posts anymore.

        $first_post = ($first	== $post_id || !$first)?'<span class="comic-nav-span">'.__('First', MP_DOMAIN).'</span>':'<a href="'.get_permalink($first).'">'.__('First', MP_DOMAIN).'</a>';
        $last_post	= ($last 	== $post_id || !$last)?'<span class="comic-nav-span">'.__('Last', MP_DOMAIN).'</span>':'<a href="'.get_permalink($last).'">'.__('Last', MP_DOMAIN).'</a>';
        $next_post	= ($next 	== $post_id || !$next)?'<span class="comic-nav-span">'.__('Next', MP_DOMAIN).'</span>':'<a href="'.get_permalink($next).'">'.__('Next', MP_DOMAIN).'</a>';
        $prev_post	= ($previous    == $post_id || !$previous)?'<span class="comic-nav-span">'.__('Previous', MP_DOMAIN).'</span>':'<a href="'.get_permalink($previous).'">'.__('Previous', MP_DOMAIN).'</a>';

        $navigation =   '<div class="comic-navigation">
                            <ul class="comic-nav">
                                <li class="comic-nav-first">'.$first_post.'</li>
                                <li class="comic-nav-prev">'.$prev_post.'</li>
                                <li class="comic-nav-next">'.$next_post.'</li>
                                <li class="comic-nav-last">'.$last_post.'</li>
                            </ul>
                        </div>';

        return $navigation;
    }
    /**
     * Looks for the currently viewed comic by comparing $post_id extracted from $ret to $item->ID. If true, then
     * that is the current comic, which is then compacted back into $ret and returned by reference.
     *
     * @param object $item Post array object being passed.
     * @param int $key Array key.
     * @param array $ret Value that will be returned by reference.
     */
    private function comic_nav_walker($items, $current) {

        extract( $current );
        foreach($items as $item => $value) {
            
            if ($value->ID == $post_id){
                $curcomic = $item;
                $ret = compact('post_id', 'curcomic' );
            }
        }

        return $ret;
    }
    /**
     *
     * @return Manga_Press_Posts
     */
    public function get_last_comic() {
        if (!$this->_last_comic) {
            $this->_last_comic =
                    new WP_Error(
                        'no comics', 'Last comic not defined!'
                    );
        }
        return $this->_last_comic;
    }
    /**
     *
     * @param <type> $last_comic
     * @return <type>
     */
    public function set_last_comic($last_comic) {
        $this->_last_comic = $last_comic;

        return $this->_last_comic;
    }

}
?>