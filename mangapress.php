<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.jes.gs/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.jes.gs/">Manga+Press</a> for more info.
 Version: 2.7-beta
 Author: Jessica Green
 Author URI: http://www.jes.gs
*/
/*
 * (c) 2008 - 2012 Jessica C Green
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

$plugin_folder = plugin_basename(dirname(__FILE__));

if (!defined('MP_VERSION'))
    define('MP_VERSION', '2.7-beta');

if (!defined('MP_DB_VERSION'))
    define('MP_DB_VERSION', '1.0');

if (!defined('MP_DB_VERSION'))
    define('MP_DB_VERSION', '1.0');

if (!defined('MP_FOLDER'))
    define('MP_FOLDER', $plugin_folder);

if (!defined('MP_ABSPATH'))
    define('MP_ABSPATH', WP_CONTENT_DIR . '/plugins/' . $plugin_folder . '/');

if (!defined('MP_URLPATH'))
    define('MP_URLPATH', WP_CONTENT_URL . '/plugins/' . $plugin_folder . '/');

if (!defined('MP_LANG'))
    define('MP_LANG', $plugin_folder . '/lang');

if (!defined('MP_DOMAIN'))
    define('MP_DOMAIN', $plugin_folder);

include_once('framework/FrameworkHelper.php');
include_once('framework/PostType.php');
include_once('framework/Taxonomy.php');
include_once('framework/View.php');
include_once('framework/Options.php');

include_once('comic-post-type.php');
include_once('mangapress-install.php');
include_once('mangapress-posts.php');
include_once('mangapress-options.php');

register_activation_hook(__FILE__, array('MangaPress_Install', 'do_activate'));
register_deactivation_hook( __FILE__, array('MangaPress_Install', 'do_deactivate'));

add_action('init', array('MangaPress_Bootstrap', 'init'));


class MangaPress_Bootstrap
{
    
    protected static $_options;

    protected $_posts;
    /**
     * Static function used to initialize Bootstrap
     * 
     * @return void 
     */
    public static function init()
    {
        global $mp, $options_page;
        
        register_theme_directory('plugins/' . basename(dirname(__FILE__)) . '/themes');
        self::set_options();
        
        //load_plugin_textdomain(MP_DOMAIN, false, $this->lang_dir);
        
        $mp         = new MangaPress_Bootstrap();
        $mp->_posts = new MangaPress_Posts();
        $options_page = new MangaPress_Options();
    }
    
    /**
     * 
     * @return void 
     */
    public function __construct()
    {
        
        $mp_options = $this->get_options();
        
        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css')
            //add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
        
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
        if ($mp_options['comic_page']['generate_comic_page']){
            add_image_size ('comic-page', $mp_options['comic_page']['comic_page_width'], $mp_options['comic_page']['comic_page_height'], false);
        }
        
        add_image_size('comic-admin-thumb', 60, 80, true);
        
    }
    
    /**
     * Set MangaPress options. This method should run every time
     * MangaPress options are updated.
     * 
     * @return void
     */
    public static function set_options()
    {
        self::$_options = maybe_unserialize(get_option('mangapress_options'));
        
    }
        
    /**
     * Get MangaPress options 
     * 
     * @return array
     */
    public function get_options()
    {
        return self::$_options;
    }
}