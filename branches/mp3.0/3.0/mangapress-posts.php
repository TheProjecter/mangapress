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

    public function  __construct() {

        register_post_type(
             'comics',
             array('label' => __('Comics', 'mangapress'),
                             'public' => true,
                             'exclude_from_search'=>false,
                             'singular_label' => __('Comic', 'mangapress'),
                             'menu_position'=>5,
                             'supports' => array(
                                        'thumbnail',
                                        'custom-fields',
                                        'editor',
                                        'author',
                                        'title'
                                        )
                                    )
                          );

        register_taxonomy( 'series', array('characters', 'comics'),
                array(
                        'hierarchical' => true,
                        'label' => __('Series &amp; Chapters', 'mangapress'),
                        'query_var' => 'series',
                        'rewrite' => array('slug' => 'series' )
                    )
        );
        /*
         * This will do something, eventually...
        add_action('publish_post', array( &$this, 'add_comic' ));
        */

        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', array(&$this, 'comics_headers'));
        add_filter('manage_edit-comics_columns', array(&$this, 'comics_columns'));

        $this->get_boundary_comics(); // grab and cache...
        $this->get_all_comics(); // grab and cache...
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
                    "cb" => "<input type=\"checkbox\" />",
                    "thumbnail"=>"Thumbnail",
                    "title" => "Comic Title",
                    "series" => "Series &amp; Chapters",
                    "post_date" => "Date",
                    "description" => "Description",
                    "author" => "Author",
                    "comments" => 'Comments'
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
                                  $mp_options['order_by'] : 'post_id';

        $sql = $wpdb->prepare(
                    "SELECT ID FROM "
                    . $wpdb->posts
                    . " WHERE post_type='comics' AND "
                    . "post_status='publish' ORDER BY "
                    . $mp_options['order_by']." DESC LIMIT 1;"
                );

        $last = $wpdb->get_results($sql);
        $this->last_comic = $last[0]->ID;

        $sql = $wpdb->prepare(
                    "SELECT ID FROM "
                    . $wpdb->posts
                    . " WHERE post_type='comics' "
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
                $group = wp_get_object_terms( $post_id, 'series' );
                $series = $group[0]->slug;
        }

        $this->all_comics = get_posts(
                array(
                    'numberposts'=>-1,
                    'post_type'=>'comics',
                    'order'=>'ASC',
                    'orderby'=>$mp_options['order_by'],
                    'series'=>$series,
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
        $ret = array('post_id'=>$post_id, 'curcomic'=>$curcomic);

        array_walk( $posts, array(&$this, 'comic_nav_walker'), &$ret);

        extract( $ret ); // extract $curcomic and $post_id, which contain the new values from mpp_comicnav_walker()

        /*
         * now, using $curcomic, let's find the next comic and the previous comic.
         */
        ($curcomic != 0) ? $prev = $curcomic - 1 : $prev = 0;
        ($curcomic != $c) ? $nxt = $curcomic + 1 : $nxt = $c;

        /*
         * it's easier this way, trust me...
         */
        $next = $posts[$nxt]->ID;
        $last = $posts[$c]->ID;
        $first = $posts[0]->ID;
        $previous = $posts[$prev]->ID;

        unset( $posts ); // free some memory, we don't need $posts anymore.

        $first_post = ($first	== $post_id || !$first)?'<span class="comic-nav-span">'.__('First', 'mangapress').'</span>':'<a href="'.get_permalink($first).'">'.__('First', 'mangapress').'</a>';
        $last_post	= ($last 	== $post_id || !$last)?'<span class="comic-nav-span">'.__('Last', 'mangapress').'</span>':'<a href="'.get_permalink($last).'">'.__('Last', 'mangapress').'</a>';
        $next_post	= ($next 	== $post_id || !$next)?'<span class="comic-nav-span">'.__('Next', 'mangapress').'</span>':'<a href="'.get_permalink($next).'">'.__('Next', 'mangapress').'</a>';
        $prev_post	= ($previous    == $post_id || !$previous)?'<span class="comic-nav-span">'.__('Previous', 'mangapress').'</span>':'<a href="'.get_permalink($previous).'">'.__('Previous', 'mangapress').'</a>';

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
    private function comic_nav_walker($item, $key, &$ret) {

        extract( $ret );
        if ($item->ID == $post_id){
                $curcomic = $key;
                $ret = compact('post_id', 'curcomic' );
        }
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
        return $this;
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
