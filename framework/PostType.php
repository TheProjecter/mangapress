<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_PostType
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

/**
 * MangaPress_PostType
 *
 * @package MangaPress_PostType
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
abstract class MangaPress_PostType extends MangaPress_FrameWork_Helper
{
    /**
     * Object name
     *
     * @var string
     */
    protected $_name;

    /**
     * Object singular (human-readable) label
     *
     * @var string
     */
    protected $_label_single;

    /**
     * Object plural (human-readable) label
     *
     * @var string
     */
    protected $_label_plural;

    /**
     * PostType Capabilities
     * @var array
     */
    protected $_capabilities = array(
        'edit_post',
        'read_post',
        'delete_post',
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'read_private_posts',
    );

    /**
     * Taxonomies attached to PostType
     *
     * @var array
     */
    protected $_taxonomies   = array();

    /**
     * Object arguments
     *
     * @var array
     */
    protected $_args         = array(
        'labels'               => '',
        'description'          => '',
        'public'               => true,
        'publicly_queryable'   => true,
        'exclude_from_search'  => false,
        'show_ui'              => true,
        'show_in_menu'         => true,
        'menu_position'        => 5,
        'menu_icon'            => '',
        'capability_type'      => 'post',
        //'capabilities'         => '',
        //'map_meta_cap'         => false,
        'hierarchical'         => false,
        'supports'             => '',
        'register_meta_box_cb' => '',
        'taxonomies'           => array(),
        'permalink_epmask'     => EP_PERMALINK,
        'has_archive'          => false,
        'rewrite'              => true,
        'can_export'           => true,
        'show_in_nav_menus'    => true,
    );

    /**
     * PostType supports
     * @var array
     */
    protected $_supports     = array('title');

    /**
     * @var View
     */
    protected $_view;

    /**
     * Object init
     *
     * @return void
     */
    public function init()
    {

        register_post_type($this->_name, $this->_args);

        add_action('generate_rewrite_rules', array($this, 'rewrite'));
        add_action('template_include', array($this, 'template_include'));

    }

    /**
     * Sets MangaPress_View object for Post Type screens
     *
     * @param MangaPress_View $view
     * @return MangaPress_PostType
     */
    public function set_view($view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Set object arguments
     *
     * @param array $args
     * @return MangaPress_FrameWork_Helper
     */
    public function set_arguments($args)
    {
        global $plugin_dir;

        $args = array_merge($this->_args, $args);
        extract($args);

        $labels
            = array(
                'name'               => $this->_label_plural,
                'singular_name'      => $this->_label_single,
                'add_new'            => __('Add New ' . $this->_label_single, $plugin_dir),
                'add_new_item'       => __('Add New ' . $this->_label_single, $plugin_dir),
                'edit_item'          => __('Edit ' . $this->_label_single, $plugin_dir),
                'view_item'          => __('View ' . $this->_label_single, $plugin_dir),
                'search_items'       => __('Search ' . $this->_label_single, $plugin_dir),
                'not_found'          => __($this->_label_single . ' not found', $plugin_dir),
                'not_found_in_trash' => __($this->_label_single . ' not found in Trash', $plugin_dir),
                'parent_item_colon'  => __($this->_label_single . ': ', $plugin_dir),
            );


        $args =
            array(
                'labels'               => $labels,
                'description'          => $description,
                'public'               => $public,
                'publicly_queryable'   => $publicly_queryable,
                'exclude_from_search'  => $exclude_from_search,
                'show_ui'              => $show_ui,
                'show_in_menu'         => $show_in_menu,
                'menu_position'        => $menu_position,
                'menu_icon'            => $menu_icon,
                'capability_type'      => $capability_type,
                //'capabilities'         => $this->_capabilities,
                //'map_meta_cap'         => $map_meta_cap,
                'hierarchical'         => $hierarchical,
                'supports'             => $supports,
                'register_meta_box_cb' => array($this, 'meta_box_cb'),
                'taxonomies'           => $taxonomies,
                'permalink_epmask'     => EP_PERMALINK,
                'has_archive'          => $has_archive,
                'rewrite'              => $rewrite,
                'can_export'           => $can_export,
                'show_in_nav_menus'    => $show_in_nav_menus,
            );

        $this->_args = $args;

        return $this;
    }

    /**
     * Set object taxonomies
     *
     * @param array $taxonomies
     * @return MangaPress_PostType
     */
    public function set_taxonomies($taxonomies)
    {
        $this->_taxonomies = $taxonomies;

        return $this;
    }

    /**
     * Set object supports
     * Must be called before set_arguments()
     *
     * @param array $supports
     * @return MangaPress_PostType
     */
    public function set_support($supports)
    {
        $this->_supports = $supports;

        return $this;
    }

    /**
     * Meta-box callback
     *
     * @return void
     */
    abstract public function meta_box_cb();

}
