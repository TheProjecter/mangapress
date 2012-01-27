<?php
/**
 * @subpackage Manga_Press_Posts
 *
 * handles registering the Comics custom post-type, any attached taxonomies
 */
class Manga_Press_Posts {
    
    private $_ajax_action_add_comic    = 'add-comic';
    private $_ajax_action_remove_comic = 'remove-comic';
    private $_nonce_insert_comic       = 'mangapress_comic-insert-comic';
    private $_ajax_url_add_comic;

    public function __construct()
    {
        
        //$this->_ajax_url_add_comic = admin_url() ;
        
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
            'mangapress_comic',
            array(
                'labels'              => $labels,
                'public'              => true,
                'exclude_from_search' => false,
                'singular_label'      => __('Comic', MP_DOMAIN),
                'menu_position'       => 5,
                'taxonomies'          => array(
                    'mangapress_series',
                    'mangapress_issue'
                ),
                'supports'            => array(
                    'thumbnail',
                    'author',
                    'title',
                    'editor',
                    'comments',
                ),
                'rewrite'             => array('slug' => 'comic'),
            )
        );

        // Add new taxonomy for Comic Posts
        register_taxonomy( 'mangapress_series', array('mangapress_comic'),
            array(
                'hierarchical' => true,
                'labels' => array(
                    'name'                => __('Series', MP_DOMAIN),
                    'singular_name'       => __('Series', MP_DOMAIN),
                    'search_items'        => __('Search ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'popular_items'       => __('Popular ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'all_items'           => __('All ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'parent_item'         => __('Parent ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'parent_item_colon'   => __('Parent ' . __('Series', MP_DOMAIN) .  ':: ', $plugin_dir),
                    'edit_item'           => __('Edit ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'update_item'         => __('Update ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'add_new_item'        => __('Add New ' . __('Series', MP_DOMAIN), $plugin_dir),
                    'new_item_name'       => __('New ' . __('Series', MP_DOMAIN) . ' name', $plugin_dir),
                    'add_or_remove_items' => __('Add or remove ' . __('Series', MP_DOMAIN), $plugin_dir),
                ),
                'query_var' => 'series',
                'rewrite' => array('slug' => 'series' )
            )
        );

        // Add new taxonomy for Comic Posts
        register_taxonomy( 'mangapress_issue', array('mangapress_comic'),
            array(
                'hierarchical' => true,
                'labels' => array(
                    'name'                => __('Issues', MP_DOMAIN),
                    'singular_name'       => __('Issue', MP_DOMAIN),
                    'search_items'        => __('Search ' . __('Issues', MP_DOMAIN), $plugin_dir),
                    'popular_items'       => __('Popular ' . __('Issues', MP_DOMAIN), $plugin_dir),
                    'all_items'           => __('All ' . __('Issues', MP_DOMAIN), $plugin_dir),
                    'parent_item'         => __('Parent ' . __('Issue', MP_DOMAIN), $plugin_dir),
                    'parent_item_colon'   => __('Parent ' . __('Issue', MP_DOMAIN) .  ':: ', $plugin_dir),
                    'edit_item'           => __('Edit ' . __('Issue', MP_DOMAIN), $plugin_dir),
                    'update_item'         => __('Update ' . __('Issue', MP_DOMAIN), $plugin_dir),
                    'add_new_item'        => __('Add New ' . __('Issue', MP_DOMAIN), $plugin_dir),
                    'new_item_name'       => __('New ' . __('Issue', MP_DOMAIN) . ' name', $plugin_dir),
                    'add_or_remove_items' => __('Add or remove ' . __('Issue', MP_DOMAIN), $plugin_dir),
                ),
                'query_var' => 'issue',
                'rewrite' => array('slug' => 'issue' )
            )
        );
        
        wp_register_script(MP_DOMAIN . '-media-script', MP_URLPATH . 'js/add-comic.js', array('jquery'), MP_VERSION);
        
        /*
         * 
         */
        //add_action('save_post', array( &$this, 'save_post' ));
        
        
        // Setup Manga+Press Post Options box
        add_action('add_meta_boxes', array( &$this, 'add_meta_boxes'));
        
        add_action("wp_ajax_{$this->_ajax_action_add_comic}", array(&$this, 'wp_ajax_comic_handler'));
        add_action("wp_ajax_{$this->_ajax_action_remove_comic}", array(&$this, 'wp_ajax_comic_handler'));
        
        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', array(&$this, 'comics_headers'));
        add_filter('manage_edit-mangapress_comic_columns', array(&$this, 'comics_columns'));
        
        add_filter('attachment_fields_to_edit', array(&$this, 'attachment_fields_to_edit'), null, 2);
        add_action('admin_head-media-upload-popup', array(&$this, 'media_upload_popup_scripts'));
        add_action('admin_print_scripts', array(&$this, 'enqueue_admin_scripts'));        
    }
    
    public function media_upload_popup_scripts()
    {
        wp_enqueue_script(MP_DOMAIN . '-media-script');
    }
    
    public function enqueue_admin_scripts()
    {
        global $pagenow, $typenow;

        if (($pagenow == 'post-new.php' || $pagenow == 'post.php') && $typenow == 'mangapress_comic') {
            wp_enqueue_script(MP_DOMAIN . '-media-script');            
        }                    
    }
    

    public function attachment_fields_to_edit($form_fields, $post)
    {

        if (strpos(get_post_mime_type($post->ID), 'image') === false)
            return $form_fields;

        if ($_GET['post_type'] !== 'mangapress_comic')
            return $form_fields;
        
        if (intval($_GET['post_id']) == 0)
            return $form_fields;
        
        $form_fields['mangapress_comic'] = array(
            'label' => __('Manga+Press', MP_DOMAIN),
            'input' => 'html',
        );
        
        $parent_post_id = intval($_GET['post_id']);
        $nonce = wp_create_nonce($this->_nonce_insert_comic);
        $fields = "<p>"
                . "<a href=\"#\" data-post-parent=\"{$parent_post_id}\" data-attachment-id=\"{$post->ID}\" data-nonce=\"{$nonce}\" class=\"manga-press-add-comic-link\">Use As Comic Image</a>"
                . "</p>";
        
        $form_fields['mangapress_comic']['html'] = $fields;
        
        return $form_fields;
    }
    
    public function wp_ajax_comic_handler() 
    {

        header('Content-type: application/json');
        
        $nonce_action = ($_POST['action'] == 'add-comic')
                            ? $this->_nonce_insert_comic
                            : "set_comic_thumbnail-{$_POST['post_parent']}";

        if (!wp_verify_nonce($_POST['nonce'], $nonce_action)) {
            // send a JSON response
            echo json_encode(array(
                'error' => 'invalid-nonce',
                'msg'   => 'Nonce has either expired or is invalid. '
                           . 'Please re-open Media Library modal and try again.'
            ));
            exit();
        }
        
        if ($_POST['attachment_id'] == '' || $_POST['attachment_id'] == 0) {
            
            echo json_encode(array(
                'error' => 'no-attachment-id',
                'msg'   => 'Attachment ID is blank.'
            ));
            exit();
        }
        
        if ($_POST['action'] == 'add-comic') {
            $html = $this->_admin_post_comic_html($_POST['attachment_id'], $_POST['post_parent']);
            $this->_set_post_comic_image($_POST['attachment_id'], $_POST['post_parent']);
        } else {
            $html = $this->_admin_post_comic_html(null, $_POST['post_parent']);
            $this->_delete_post_comic_image($_POST['post_parent']);
        }
        
        echo json_encode(array(
            'html'        => $html,
            'post_parent' => intval($_POST['post_parent']),
        ));
        
        die();
            
    }
    
    private function _admin_post_comic_html($thumbnail_id = '', $post_parent = '')
    {
        global $_wp_additional_image_sizes;
        
	$set_thumbnail_link = '<p class="hide-if-no-js"><a title="' 
                            . esc_attr__( 'Set Comic Image', MP_DOMAIN ) . '" href="' 
                            . esc_url( $this->_get_iframe_src_url($post_parent) ) 
                            . '" id="set-comic-image" class="thickbox">%s</a></p>';
        
	$content = sprintf($set_thumbnail_link, esc_html__(  'Set Comic Image', MP_DOMAIN ));

	if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
            
            if ( !isset( $_wp_additional_image_sizes['comic-page'] ) ) {
                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'medium');
            } else {
                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'comic-page' );
            }
            
            if ( !empty( $thumbnail_html ) ) {
                    $ajax_nonce = wp_create_nonce( "set_comic_thumbnail-{$post_parent}" );
                    $content = sprintf($set_thumbnail_link, $thumbnail_html);
                    $content .= '<p class="hide-if-no-js">'
                             . '<a href="#" data-nonce="' . $ajax_nonce . '" data-post-parent="' . $post_parent . '" id="remove-comic-thumbnail">'
                             . esc_html__( 'Remove Comic image', MP_DOMAIN ) . '</a></p>';
            }
                        
	}

	return apply_filters( 'mangapress_admin_post_thumbnail_html', $content );
        
    }
    
    private function _set_post_comic_image($thumbnail_id, $post_parent)
    {
        
        $attachment_post = get_post($thumbnail_id);
        $attachment_post->post_parent = $post_parent;
        
        wp_update_post($attachment_post);
        
        return set_post_thumbnail($post_parent, $thumbnail_id);
    }
    
    private function _delete_post_comic_image($post_parent)
    {
        $thumbnail_id = get_post_thumbnail_id($post_parent);
        
        $attachment_post = get_post($thumbnail_id);
        $attachment_post->post_parent = 0;
        
        wp_update_post($attachment_post);
        
        return delete_post_thumbnail($post_parent);
        
    }
    
    /**
     * mpp_custom_columns()
     *
     * @since 2.7
     *
     */
    public function comics_headers($column)
    {
        global $post;
        
        if ("cb" == $column) {
            echo "<input type=\"checkbox\" value=\"{$post->ID}\" name=\"post[]\" />";
        } elseif ("thumbnail" == $column) {

            $thumbnail_html = get_the_post_thumbnail($post->ID, 'comic-admin-thumb', array('class' => 'wp-caption'));

            if ($thumbnail_html) {
                echo $thumbnail_html;
            } else {
                echo "No image";
            }
        } elseif ("title" == $column) {
            echo $post->post_title;
        } elseif ("series" == $column) {
            $series = wp_get_object_terms( $post->ID, 'mangapress_series' );
            if (!empty($series)){
                $series_html = array();
                foreach ($series as $s)
                    array_push($series_html, '<a href="' . get_term_link($s->slug, 'mangapress_series') . '">'.$s->name."</a>");

                echo implode($series_html, ", ");
            }
        } elseif ("post_date" == $column) {
            echo date( "Y/m/d", strtotime($post->post_date) );

        } elseif ("description" == $column) {
            echo $post->post_excerpt;
        } elseif ("author" == $column) {
            echo $post->post_author;
        }
    }
    /**
     * mpp_comic_columns()
     *
     * @since 2.7
     *
     */
    public function comics_columns($columns)
    {

        $columns = array(
                "cb"          => "<input type=\"checkbox\" />",
                "thumbnail"   => "Thumbnail",
                "title"       => "Comic Title",
                "series"      => "Series",
                "description" => "Description",
                //"comments"    => "Comments",
                //"author"      => "Author",
                //"post_date"   => "Date",
        );

        return $columns;

    }
    
    public function add_meta_boxes() 
    {
        
        add_meta_box(
            'comic-image',
            __('Comic Image', MP_DOMAIN),
            array(&$this, 'comic_meta_box_cb'), 
            'mangapress_comic',
            'normal',
            'high'
        );
        
        /*
         * Because we don't need this...the comic image is the "Featured Image"
         * TODO add an option for users to override this "functionality"
         */
        remove_meta_box('postimagediv', 'mangapress_comic', 'side');
    }
    
    public function comic_meta_box_cb()
    {
        global $post_ID;
        
        $thumbnail_id = get_post_thumbnail_id($post_ID);
        
        if ($thumbnail_id == '') {
            $image_popup_url = $this->_get_iframe_src_url($post_ID);
        ?>
        <a href="<?php echo $image_popup_url; ?>" title="<?php esc_attr__( 'Set Comic Image', MP_DOMAIN ) ?>" id="set-comic-image" class="thickbox">Set Comic Image</a>        
        <?php
        
        } else {
            echo $this->_admin_post_comic_html($thumbnail_id, $post_ID);
        }

    }
    
    private function _get_iframe_src_url($post_ID)
    {
        $iframe_url = add_query_arg(array(
                        'post_id'   => $post_ID,
                        'tab'       => 'library',
                        'post_type' => 'mangapress_comic',
                        'TB_iframe' => 1,
                        'width'     => '640',
                        'height'    => '322'
                    ),
                    admin_url('media-upload.php')
                );
        
        return $iframe_url;
    }


    public function save_post($post_id)
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
    
}