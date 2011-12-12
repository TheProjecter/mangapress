<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.silent-shadow.net/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.silent-shadow.net/">Manga+Press</a> for more info.
 Version: 2.7-beta
 Author: Jessica Green
 Author URI: http://www.dumpster-fairy.com
*/
/*
 * (c) 2008 - 2011 Jessica C Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

global $mp;

include_once(ABSPATH . "/wp-includes/pluggable.php");
include_once("mangapress-bootstrap.php");
include_once("mangapress-posts.php");
include_once("mangapress-setup.php");
include_once("mangapress-options.php");
include_once("includes/mangapress-constants.php");
include_once("includes/mangapress-functions.php");
include_once("includes/mangapress-template-functions.php");
include_once("includes/mangapress-pages.php");

/**
 * @subpackage Manga_Press
 */
class Manga_Press {
    
    /**
     * Plugin directory
     *
     * @var string
     */
    public $plugin_dir;

    /**
     * Location of language files
     *
     * @var string
     */
    public $lang_dir;

    /**
     * Comic posts
     *
     * @var mixed
     */
    public $comics;
    
    public $options;

    /**
     * Variable for Manga Press setup class.
     *
     * @var Manga_Press_Setup
     * @access private
     */
    private $_install;

    /**
     * Constructor function.
     *
     * @global array $mp_options
     *
     * @return void
     */
    public function  __construct()
    {
        global $mp_options;
       
        $this->plugin_dir = basename(dirname(__FILE__));
        
        /*
         * Load our text domain for international support
         */
        $this->lang_dir =  $this->plugin_dir . '/lang';
        
        load_plugin_textdomain(MP_DOMAIN, false, $this->lang_dir);
        
        /*
         * Initialize the Setup class. We use this to check if Manga+Press needs
         * to be upgraded, if it is a fresh install, or just do nothing.
         */
        $this->_install = new Manga_Press_Setup();
         
        $this->options  = new Manga_Press_Options();
        
        /*
         * General plugin administration hooks
         */
        register_activation_hook( __FILE__, array( &$this->_install, 'activate' ));
        register_deactivation_hook( __FILE__, array( &$this->_install, 'deactivate' ));
                        
        $mp_options = maybe_unserialize( get_option('mangapress_options') );
        
        add_action('admin_menu', array($this, 'admin_init'));

        /*
         * Initialize the Comic Posts class
         */
        $this->comics = new Manga_Press_Posts();

        // enable Manga+Press theme
        //add_action('setup_theme', 'mangapress_load_theme_dir');
        
        add_action('template_include', 'mpp_series_template');

        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css')
            add_action('wp_print_styles', 'mpp_add_nav_css');
        
        /*
         * Comic Navigation
         */
        if ($mp_options['nav']['insert_nav'])
            add_action('template_include', 'mpp_comic_insert_navigation');

        /*
         * Lastest Comic Page
         */
        if ((bool)$mp_options['basic']['latestcomic_page'])
            add_filter('template_include', 'mpp_filter_latest_comic');

        /*
         * Comic Archive Page setup
         */
        if ((bool)$mp_options['basic']['comicarchive_page'])
            add_filter('template_include', 'mpp_filter_comic_archivepage');

        /*
         * Comic Thumbnail Banner
         */
        add_image_size ('comic-banner', $mp_options['comic_page']['banner_width'], $mp_options['comic_page']['banner_height'], true);

        /*
         * Comic Page size
         */
        if ($mp_options['comic_page']['generate_comic_page'])
            add_image_size ('comic-page', $mp_options['comic_page']['comic_page_width'], $mp_options['comic_page']['comic_page_height'], false);
        
        add_image_size('comic-admin-thumb', 60, 80, true);
        
        /*
         * Navigation style
         */
        wp_register_style('mangapress-nav', MP_URLPATH . 'css/nav.css', null, MP_VERSION, 'screen');

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
        
    }
    

    /**
     * admin_init()
     *
     * @since 2.7
     *
     * Loads Manga+Press Options Pages
     *
     */
    public function admin_init()
    {
        global $mp_options;

        $options = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            'mangapress-options-page',
            array($this->options, 'page_options')
        );


        if (get_option('mangapress_upgrade') == 'yes'){
            $upgrade =  add_submenu_page(
                "plugins.php",
                __("Manga+Press Options", MP_DOMAIN),
                __("Manga+Press Upgrade", MP_DOMAIN),
                'administrator',
                'upgrade',
                'upgrade_mangapress'
            );
        }

        add_action("admin_print_scripts-$options", array($this->options, 'options_print_scripts'));
        add_action("admin_print_styles-$options", array($this->options, 'options_print_styles'));
        
        add_action('admin_init', array($this->options, 'options_init'));
    }
}
