<?php

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
     * @var \View
     */
    protected $_view;


    public function __construct()
    {

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

        add_action('admin_menu', array($this, 'admin_init'));        
    }
    
    public function admin_init()
    {
        $options = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            'mangapress-options-page',
            array(&$this, 'page_options')
        );

        $this->_view = new View(
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
        );
        
    }
    
    public function page_options()
    {
        include_once 'pages/options.php';
    }

    public function options_fields($options)
    {
        ;
    }
    
    public function settings_field_cb($option)
    {
        ;
    }
    
    public function sanitize_options($options)
    {
        ;
    }
    
}
