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

include_once(ABSPATH . "/wp-includes/pluggable.php");
include_once("includes/mangapress-constants.php");
include_once("includes/mangapress-functions.php");
include_once("includes/mangapress-template-functions.php");
include_once("includes/mangapress-pages.php");

/**
 * @global array $mp_options. Manga+Press options array.
 */ 
global $mp_options;

$mp_options = unserialize( get_option('mangapress_options') );

add_action('init', 'mangapress_init');
add_action('admin_init', 'mangapress_options_init');
add_action('admin_menu', 'mangapress_admin_init');
add_action('delete_post', 'mpp_delete_comic_post');
add_action('save_post', 'mpp_add_comic_post');

add_action('wp_head',	'mpp_add_header_info');
// enable Manga+Press theme
add_action('setup_theme', 'mangapress_load_theme_dir');

// Setup Manga+Press Post Options box
add_action('add_meta_boxes', 'mangapress_add_comic_panel');

add_action('template_include', 'mpp_series_template');

if ($mp_options['nav_css'] == 'default_css')
    add_action('wp_print_styles', 'mpp_add_nav_css');

if ((bool)$mp_options['comic_front_page'])
    add_action('pre_get_posts', 'mpp_filter_posts_frontpage');

if ((bool)$mp_options['latestcomic_page'])
    add_filter('template_include', 'mpp_filter_latest_comic');

if ((bool)$mp_options['comic_archive_page'])
    add_filter('template_include', 'mpp_filter_comic_archivepage');

if ($mp_options['insert_nav'])
    add_action('template_include', 'mpp_comic_insert_navigation');

if ($mp_options['make_thumb'])
    add_image_size ('comic-banner', $mp_options['banner_width'], $mp_options['banner_height'], true);

if ($mp_options['generate_comic_page'])
    add_image_size ('comic-page', $mp_options['comic_width'], $mp_options['comic_height'], false);

add_image_size('comic-sidebar-image', 150, 150, true);

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

/**
 * mangapress_init()
 * 
 * @since 2.6.2
 *
 * Handles any functions needed during plugin init, like loading text domains.
 */
function mangapress_init()
{
    $plugin_dir = basename(dirname(__FILE__)). '/lang';
    load_plugin_textdomain( 'mangapress', false, $plugin_dir);   

    $src = MP_URLPATH . 'css/nav.css';
    wp_register_style('mangapress-nav', $src, null, MP_VERSION, 'screen');

    // Add new taxonomy for Comic Posts
    register_taxonomy( 'series', array('post'),
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
    register_taxonomy( 'issue', array('post'),
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
    
}

/**
 * Registers the theme directory and clears out the transient theme roots cache.
 *
 * @return void
 */
function mangapress_load_theme_dir()
{

    register_theme_directory('plugins/' . MP_FOLDER . '/themes');
}

/**
 * Handles adding additional metaboxes to Posts panel.
 * 
 * @return void
 */
function mangapress_add_comic_panel()
{
    add_meta_box(
        'comicoptions',
        'Comic Options',
        'mangapress_comic_panel_cb',
        'post',
        'normal',
        'high'
    );
}

/**
 * Output metabox. Callback for add_meta_box();
 * 
 * @global object $post WordPress post object
 * @return void
 */
function mangapress_comic_panel_cb()
{
    global $post;
    // Use nonce for verification
    wp_nonce_field(MP_FOLDER, 'mangapress_nonce');

    $comic_meta = (int)get_post_meta($post->ID, 'comic', true);
?>
    <fieldset>
        <label for="is_comic">
            <input type="checkbox" name="is_comic" id="is_comic" value="1" <?php checked($comic_meta, 1) ?> /> Is this post a comic?
        </label>
    </fieldset>

<?php
}

/**
 * mangapress_admin_init()
 *
 * @since 2.6
 *
 * Loads Manga+Press Options Pages
 *
 */
function mangapress_admin_init()
{
    global $mp_options;

    $options = add_options_page(
        __("Manga+Press Options", 'mangapress'),
        __("Manga+Press Options", 'mangapress'),
        'manage_options',
        'mangapress-options',
        'mangapress_options_page'
    );


    if (get_option('mangapress_upgrade') == 'yes'){
        $upgrade =  add_submenu_page(
            "plugins.php",
            __("Manga+Press Options", 'mangapress'),
            __("Manga+Press Upgrade", 'mangapress'),
            'administrator',
            'upgrade',
            'upgrade_mangapress'
        );
    }

    add_action("admin_print_scripts-$options", 'mangapress_options_print_scripts');
    add_action("admin_print_styles-$options", 'mangapress_options_print_styles');
}

function mangapress_options_print_scripts()
{
    wp_enqueue_script('syntax-highlighter-cssbrush');
}

function mangapress_options_print_styles()
{
    wp_enqueue_style('syntax-highlighter-css');
}

/**
 * mangapress_options_init()
 *
 * @since 2.6b
 *
 * Registers Manga+Press settings
 *
 * @return void
 */
function mangapress_options_init()
{
    // Adding new options...
    register_setting(
        'mangapress-options',
        'mangapress_options',
        'update_mangapress_options'
    );
}

/**
 * mangapress_activate()
 *
 * @since 0.1b
 *
 * Manga+Press activation hook. Was originally webcomicplugin_activate()
 *
 * @return void
 */
function mangapress_activate()
{
    global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Check for capability
    if ( !current_user_can('activate_plugins') ){
        wp_die(
            __(
                'Sorry, you do not have suffient permissions '
                . 'to activate this plugin.',
                'mangapress'
            )
        );
    }
    
    // Get the capabilities for the administrator
    $role = get_role('administrator');
	
    // Must have admin privileges in order to activate.
    if (empty($role)){
        wp_die(
            __(
                'Sorry, you must be an Administrator in '
                . 'order to use Manga+Press',
                'mangapress'
            )
        );
    }
    
    /*
     * Pull the current Manga+Press options from the database.
     * If it's empty, either this is a first-time install or is an
     * upgrade from Manga+Press 2.5 where the options were stored
     * in seperate rows in the database...
     */
    $options = get_option('mangapress_options');

    /*
     * Set default options, if new. Or add options, if upgrading.
     * This function handles both.
     */
    mangapress_set_options();
    
    $wp_rewrite->flush_rules();
	
}

/**
 * mangapress_set_options()
 *
 * @since 2.6b
 *
 * Sets default options if activation wasn't an upgrade or
 * copies old options over to new options if it is an upgrade
 *
 */
function mangapress_set_options()
{

    // no checks for version 2.5 or older.
    $installed_ver = substr(strval(get_option('mangapress_ver')), 0, 3);

    // This should be taken out. Updgrade doesn't run here.
    if (version_compare($installed_ver, '2.6', '==')){
        global $mp_options;

        // add new options here
        $mp_options['generate_comic_page'] = false; // New option in 2.7
        $mp_options['comic_width']         = '';    // New option in 2.7
        $mp_options['comic_height']        = '';    // New option in 2.7

        //  Manga+Press checks for this to display the upgrade page
        add_option('mangapress_upgrade', 'yes', '', 'no');

    } else {
        //
        // if $installed_ver returns false, then this is a new install
        // Before setting options, look for or create a Comics category.
        $comic_cat = get_category_by_slug('comics');
        if (!$comic_cat) {
            $comic_cat_ID = wp_create_category('Comics');
        } else {
            $comic_cat_ID = $comic_cat->term_id;
        }

        // add comic options to database
        $mp_options['nav_css']            = 'default_css';
        $mp_options['order_by']           = 'post_date';
        $mp_options['insert_nav']         = false;
        $mp_options['group_comics']       = false;
        $mp_options['latestcomic_cat']    = $comic_cat_ID;
        $mp_options['latestcomic_page']   = 0;
        $mp_options['comic_archive_page'] = 0;
        $mp_options['make_thumb']         = false;
        $mp_options['banner_width']       = 0;
        $mp_options['banner_height']      = 0;
        $mp_options['generate_comic_page'] = false; // New option in 2.7
        $mp_options['comic_width']         = ''; // New option in 2.7
        $mp_options['comic_height']        = ''; // New option in 2.7

        add_option('mangapress_ver', MP_VERSION, '', 'no');
    }

    add_option('mangapress_options', serialize($mp_options), '', 'no');
}

/**
 * mangapress_upgrade()
 *
 * @since 2.0 beta
 *
 * Handles the process of upgrading from previous versions by
 * copying over old options to new options and deleting old
 * options. Also handles any changes to database schema.
 *
 * @todo Set defaults for new options in this function.
 */
function mangapress_upgrade()
{
    global $mp_options, $wpdb;

    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
    check_admin_referer('mangapress-upgrade-form');
	
    $msg = "Manga+Press version " . MP_VERSION . "<br />";
    if (get_option('mangapress_upgrade') == 'yes') {

        $wpdb->mpcomicseries = $wpdb->prefix . 'comics_series';
        $wpdb->mpcomics      = $wpdb->prefix . 'comics';
        
        $msg .= __("Upgrading Manga+Press...<br />", 'mangapress');

        add_option('mangapress_ver', MP_VERSION, '', 'no');
        add_option('mangapress_db_ver', MP_DB_VERSION, '', 'no');

        $msg .= __('Deleting old options....<br/>', 'mangapress');
        //
        // Remove options from previous version, which would be versions 1.0 to 2.5
        delete_option('comic_latest_default_category');
        delete_option('comic_latest_page');
        delete_option('comic_archive_page');
        delete_option('comic_plugin_ver');
        delete_option('comic_order_by');
        delete_option('banner_width');
        delete_option('banner_height');
        delete_option('comic_make_thmb');
        delete_option('comic_use_default_css');
        delete_option('comic_db_ver');
        delete_option('comic_front_page');
        delete_option('insert_nav');
        delete_option('insert_banner');
        delete_option('twc_code_insert');
        delete_option('oc_code_insert');
        delete_option('oc_comic_id');
        delete_option('insert_banner');
        delete_option('mangapress_db_ver');

        delete_option('mangapress_upgrade');
        $msg .= __('Old options have been deleted from the database.', 'mangapress')
             .'<br/>';
        //
        // Make changes to databases...
        if(($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->mpcomics}'") != $wpdb->mpcomics)) {
            // this version, we don't need the mpcomics table anymore. Drop it.
            $sql = $wpdb->prepare("DROP TABLE {$wpdb->mpcomics};");
            $wpdb->query($sql);

            $msg .=  __("{$wpdb->mpcomics} has been removed.", 'mangapress')
                 ."<br />";
        }

        if( $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->mpcomicseries}'") ) {
            $sql = $wpdb->prepare("DROP TABLE {$wpdb->mpcomicseries};" );
            $wpdb->query($sql);

            $msg .=  __("$wpdb->mpcomicseries has been removed.", 'mangapress')
                 ."<br />";
        }
        $msg .= __("Manga+Press has been upgraded to ", 'mangapress')
             . MP_VERSION
             . "<br />";
    }

    return $msg;
}

/**
 * mangapress_deactivate()
 *
 * @since 2.6
 *
 * Manga+Press deactivation hook. Does the clean-up after
 * uninstall has run.
 *
 */
function mangapress_deactivate()
{
    global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $wp_rewrite->flush_rules();

}

register_activation_hook( __FILE__, 'mangapress_activate' );
register_deactivation_hook( __FILE__, 'mangapress_deactivate' );
