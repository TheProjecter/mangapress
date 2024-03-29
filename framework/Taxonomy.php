<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

/**
 * MangaPress_Taxonomy
 *
 * @package MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Taxonomy extends MangaPress_FrameWork_Helper
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
     * Objects that support this taxonomy
     * @var array
     */
    protected $_object_types  = array('post');

    /**
     * Object arguments
     *
     * @var array
     */
    protected $_args = array(
        'labels'                => '',
        'public'                => true,
        'can_export'            => true,
        'show_in_nav_menus'     => true,
        'show_ui'               => true,
        'show_tagcloud'         => false,
        'hierarchical'          => false,
        'update_count_callback' => '',
        'rewrite'               => true,
        'query_var'             => true,
        'capabilities'          => array(),
    );

    /**
     * Init object
     * @return void
     */
    public function init()
    {
        register_taxonomy($this->_name, $this->_object_types, $this->_args);
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

        $args
            = array(
                'labels' => array(
                    'name'                       => $this->_label_plural,
                    'singular_name'              => $this->_label_single,
                    'search_items'               => __('Search '                . $this->_label_plural, $plugin_dir),
                    'popular_items'              => __('Popular '               . $this->_label_plural, $plugin_dir),
                    'all_items'                  => __('All '                   . $this->_label_plural, $plugin_dir),
                    'parent_item'                => __('Parent '                . $this->_label_single, $plugin_dir),
                    'parent_item_colon'          => __('Parent '                . $this->_label_single .  ':: ', $plugin_dir),
                    'edit_item'                  => __('Edit '                  . $this->_label_single, $plugin_dir),
                    'update_item'                => __('Update '                . $this->_label_single, $plugin_dir),
                    'add_new_item'               => __('Add New '               . $this->_label_single, $plugin_dir),
                    'new_item_name'              => __('New '                   . $this->_label_single . ' name', $plugin_dir),
                    'separate_items_with_commas' => __('Separate '              . $this->_label_plural . ' with commas', $plugin_dir),
                    'add_or_remove_items'        => __('Add or remove '         . $this->_label_plural, $plugin_dir),
                    'choose_from_most_used'      => __('Choose from most used ' . $this->_label_plural, $plugin_dir),
                ),
                'public'                => $public,
                'can_export'            => $can_export,
                'show_in_nav_menus'     => $show_in_nav_menus,
                'show_ui'               => $show_ui,
                'show_tagcloud'         => $show_tagcloud,
                'hierarchical'          => $hierarchical,
                'update_count_callback' => '',
                'rewrite'               => $rewrite,
                'query_var'             => $query_var,
                'capabilities'          => $capabilities,
            );

        $this->_args = $args;

        return $this;
    }

    /**
     * Set taxonomy objects
     *
     * @param array $object_types
     * @return MangaPress_Taxonomy
     */
    public function set_objects($object_types)
    {
        $this->_object_types  = $object_types;

        return $this;
    }
}
