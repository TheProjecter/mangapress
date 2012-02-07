<?php

require_once 'pages/options.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mangapress-options
 *
 * @author Jessica
 */
class MangaPress_Options extends Options
{
    /**
     * Options page View object
     *
     * @var \View_OptionsPage
     */
    protected $_view;


    public function __construct()
    {
        parent::__construct(
            array(
                'name'             => 'mangapress',
                'optiongroup_name' => 'mangapress_options',
                'options_field'    => $this->options_fields(),
                'sections'         => $this->options_sections(),
                'option_page'      => 'mangapress-options-page',
            )
        );

        // Syntax highlighter
        wp_register_script(
            'syntax-highlighter',
            MP_URLPATH . 'pages/js/syntaxhighlighter/scripts/shCore.js'
        );
        // the brush we need...
        wp_register_script(
            'syntax-highlighter-cssbrush',
            MP_URLPATH . 'pages/js/syntaxhighlighter/scripts/shBrushCss.js',
            array('syntax-highlighter')
        );
        // the style
        wp_register_style(
            'syntax-highlighter-css',
            MP_URLPATH . 'pages/js/syntaxhighlighter/styles/shCoreDefault.css'
        );

        add_action('admin_menu', array($this, 'admin_init'), 10);
    }

    public function admin_init()
    {
        $options = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            'mangapress-options-page',
            array(&$this->_view, 'page')
        );

        $this->set_view(
            new View_OptionsPage(
                array(
                    'path'       => MP_URLPATH, // plugin path
                    'post_type'  => null,
                    'hook'       => $options,
                    'js_scripts' => array(
                        'syntax-highlighter',
                        'syntax-highlighter-cssbrush'
                    ),
                    'css_styles' => array(
                        'syntax-highlighter-css',
                    ),
                    'ver'        => MP_VERSION,
                )
            )
        );

    }

    public function set_view(View_OptionsPage $view)
    {
        $this->_view = $view;

        return $this;
    }

    public function get_view()
    {
        if (!($this->_view instanceof View)) {
            return new WP_Error('not_view', '$this->_view is not an instance of View');
        }

        return $this->_view;
    }

    public function options_fields($options = array())
    {
        /*
         * Section
         *      |_ Option
         *              |_ Option Setting
         */
        $options = array(
            'basic' => array(
                'order_by' => array(
                    'id'    => 'order-by',
                    'title' => __('Order By', MP_DOMAIN),
                    'type'  => 'select',
                    'valid' => array(
                        'post_date' => __('Date', MP_DOMAIN),
                        'post_id'   => __('Post ID', MP_DOMAIN),
                    ),
                    'default' => 'post_date',
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'group_comics'      => array( // New options in 3.0
                    'id'    => 'group-comics',
                    'type'  => 'checkbox',
                    'title' => __('Group Comics', MP_DOMAIN),
                    'valid' => 'boolean',
                    'default' => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'latestcomic_page'  => array(
                    'id'    => 'latest-comic-page',
                    'type'  => 'select',
                    'title' => __('Latest Comic Page', MP_DOMAIN),
                    'valid'    => array(),
                    'default'  => 0,
                    'callback' => array(&$this, 'ft_basic_page_dropdowns_cb'),
                ),
                'comicarchive_page' => array(
                    'id'    => 'archive-page',
                    'type'  => 'select',
                    'title' => __('Comic Archive Page', MP_DOMAIN),
                    'valid' => array(),
                    'default' => 0,
                    'callback' => array(&$this, 'ft_basic_page_dropdowns_cb'),
                ),
            ),
            'comic_page' => array(
                'banner_width'        => array(
                    'id'    => 'banner-width',
                    'type'  => 'text',
                    'title' => __('Banner Width', MP_DOMAIN),
                    'valid' => '/[0-9]/',
                    'default' => 450,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'banner_height'       =>  array(
                    'id'    => 'banner-height',
                    'type'  => 'text',
                    'title'   => __('Banner Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 100,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'comic_post_count'    =>  array( // New option in 3.0
                    'id'    => 'number-posts',
                    'type'  => 'text',
                    'title' => __('Comic Posts to Display', MP_DOMAIN),
                    'description' => 'Overrides ',
                    'valid' => '/[0-9]/',
                    'default' => 10,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'generate_comic_page' => array(  // New option in 3.0
                    'id'    => 'generate-page',
                    'type'  => 'checkbox',
                    'title'       => __('Generate Comic Page', MP_DOMAIN),
                    'description' => 'Generate a comic page based on values below.',
                    'valid'       => 'boolean',
                    'default'     => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'comic_page_width'    => array( // New option in 3.0
                    'id'    => 'page-width',
                    'type'  => 'text',
                    'title'   => __('Comic Page Width', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 600,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'comic_page_height'   => array( // New option in 3.0
                    'id'    => 'page-height',
                    'type'  => 'text',
                    'title'   => __('Comic Page Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 1000,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
            ),
            'nav' => array(
                'insert_nav' => array(
                    'id'      => 'insert',
                    'title'   => __('Insert Navigation', MP_DOMAIN),
                    'description' => __('Automatically insert comic navigation code into comic posts.', MP_DOMAIN),
                    'type'    => 'checkbox',
                    'valid'   => 'boolean',
                    'default' => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'nav_css'    => array(
                    'id'     => 'navigation-css',
                    'title'  => __('Navigation CSS', MP_DOMAIN),
                    'description' => __('Turn this off. You know you want to!', MP_DOMAIN),
                    'type'   => 'select',
                    'valid'  => array(
                        'default_css' => __('Default CSS', MP_DOMAIN),
                        'custom_css' => __('Custom CSS', MP_DOMAIN),
                    ),
                    'default' => 'default_css',
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'display_css' => array(
                    'id'       => 'display',
                    'callback' => array(&$this, 'ft_navigation_css_display_cb'),
                )
            ),
        );

        return $options;

    }

    public function options_sections($sections = array())
    {
        $sections = array(
            'basic'      => array(
                'title'       => __('Basic Options', MP_DOMAIN),
                'description' => __(
                    'This section sets the &ldquo;Latest-&rdquo; and '
                    . '&ldquo;Comic Archive&rdquo; pages, number of comics '
                    . 'per page, and grouping comics together by category.',
                    MP_DOMAIN
                ),
            ),

            'comic_page' => array(
                'title'       => __('Comic Page Options', MP_DOMAIN),
                'description' => __(
                    'Handles image sizing options for comic pages. '
                    . 'Thumbnail support may need to be enabled for some '
                    . 'features to work properly.',
                    MP_DOMAIN
                ),
            ),

            'nav'        => array(
                'title'       => __('Navigation Options', MP_DOMAIN),
                'description' => __(
                    'Options for comic navigation. Whether to have navigation'
                    . ' automatically inserted on comic pages, or to enable/disable'
                    . ' default comic navigation CSS.',
                    MP_DOMAIN
                ),
            ),
        );

        return $sections;

    }

    public function output_settings_fields()
    {

        $field_sections = $this->options_fields();
        $current_tab    = $this->get_view()->get_current_tab();
        $fields         = $field_sections[$current_tab];

        foreach ($fields as $field_name => $field) {

            add_settings_field(
                "{$current_tab}-options-{$field['id']}",
                (isset($field['title']) ? $field['title'] : " "),
                array(&$this, 'settings_field_cb'), //$field['callback'],
                "mangapress_options-{$current_tab}",
                "mangapress_options-{$current_tab}",
                array_merge(array('name' => $field_name), $field)
            );

        }

    }

    public function settings_field_cb($option)
    {
        //place holder
        echo '<p>' . $option['name'] . '</p>';
    }

    /**
     * settings_section_cb()
     * Outputs Settings Sections
     *
     * @param string $section Name of section
     * @return void
     */
    public function settings_section_cb($section)
    {
        $options = $this->options_sections();

        $current = (substr($section['id'], strpos($section['id'], '-') + 1));

        echo "<p>{$options[$current]['description']}</p>";
    }

    public function sanitize_options($options)
    {
        return $options;
    }

}
