<?php
/**
 * @package Manga_Press
 * @version 3.0-beta
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.silent-shadow.net/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.silent-shadow.net/">Manga+Press</a> for more info.
 Version: 3.0
 Author: Jessica Green
 Author URI: http://www.dumpster-fairy.com
 License: GPL3
*/
/**
 * @todo Add routine to check if CharacterWiki is installed
 * @todo Update options routines to add new parameters.
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
    die('You are not allowed to call this page directly.');
}
//ini_set('error_reporting', E_ALL);
include_once("includes/mangapress-constants.php");
include_once("includes/mangapress-template-functions.php");
include_once("mangapress-posts.php");
include_once("mangapress-setup.php");
/**
 * @global object $wp_rewrite. WP_Rewrite object. @link http://codex.wordpress.org/Function_Reference/WP_Rewrite
 * @global object $wpdb. WPDB (Wordpress Database) Class object. @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 * @global string $wp_version. Wordpres version declaration.
 * @global array $mp_options. Manga+Press options array.
 */ 
global $wp_rewrite, $wpdb, $wp_version, $wp_roles, $mp_options, $mp, $comics;

add_action('setup_theme', 'mpp_load_theme_dir');
add_action('init', 'mp_init');

if (!function_exists('mpp_load_theme_dir')) {
    
    /**
     * Because Manga+Press comes with it's own bundled theme, we have to register
     * it's directory. Simple enough, but we do run into the problem of the dreaded
     * blank page. This is caused by the values stored in _site_transient_theme_roots
     * expiring. A way around this is to filter pre_transient_theme_roots to add
     * our value to the database. For more information, see
     * {@link http://core.trac.wordpress.org/ticket/11956 Ticket #11956} on
     * WordPress Trac
     *
     * @return void
     */
    function mpp_load_theme_dir() {
        
        add_filter( 'pre_transient_theme_roots', create_function( '', 'return get_site_transient("theme_roots");' ) );
        register_theme_directory('plugins/' . basename(dirname(__FILE__)) . '/themes');

    }

}

/**
 * Initilizes the Manga+Press plugin. Called by init();
 *
 * @global object $mp Manga_Press Class variable
 */
function mp_init() {
    global $mp;

    $mp = new Manga_Press();
    
}
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

    /**
     * Variable for Manga Press setup class.
     *
     * @var Manga_Press_Setup
     * @access private
     */
    private $install;

    /**
     * Constructor function.
     *
     * @global array $mp_options
     *
     * @return void
     */
    public function  __construct() {
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
        $this->install = new Manga_Press_Setup();
        
        /*
         * General plugin administration hooks
         */
        register_activation_hook( __FILE__, array( &$mp, 'activate' ));
        register_deactivation_hook( __FILE__, array( &$mp, 'deactivate' ));
        
        /*
         * Grab Options
         */
        $mp_options = maybe_unserialize( get_option('mangapress_options') );
        
        /*
         * Add header information
         */
        add_action('wp_head', array(&$this, 'add_header_info'));

        /*
         * If the Use Default CSS option is set...
         */
        if ($mp_options['nav_css'] == 'default_css') {
            if (!is_admin())
                wp_enqueue_style('mpnav', MP_URLPATH.'css/nav.css', null, MP_VERSION, 'screen');
        }
        
        /*
         * Initialize our options settings and menus
         */
        add_action('admin_init', array(&$this, 'options_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));

        /*
         * Initialize the Comic Posts class
         */
        $this->comics = new Manga_Press_Posts();

        /*
         * Comic Page filtering
         *
         * Replaces Latest Comic page content with content from recent comic post.
         */
        if ( (bool)$mp_options['latestcomic_page'] )
            add_action('the_content', array(&$this, 'filter_latest_comicpage'));

        /*
         * Same as Latest Comic Page filter but creates a list for the Archive Page.
         */
        if ( (bool)$mp_options['comic_archive_page'] )
            add_action('the_content', array(&$this, 'filter_comic_archivepage'));

        /*
         * Inserts the update code for TheWebComicList.com at the beginning of
         * The Loop on the Latest Comic Page.
         */
        if ($mp_options['twc_code_insert'])
           add_action('loop_start', array(&$this, 'insert_twc_update_code'));

        /*
         * Inserts a banner for the most recent comic at the beginning of The Loop
         * on the home page.
         */
        if ($mp_options['insert_banner'])
            add_action('loop_start', array(&$this, 'insert_banner'));

        /*
         * Inserts comic navigation at the beginning of The Loop for a comic post
         */
        if ($mp_options['insert_nav'])
            add_action('loop_start', array(&$this, 'insert_comic_navigation'));

        /*
         * For generating banners for comic pages. Theme must have post thumbnails enabled.
         */
        if ($mp_options['make_thumb'])
            add_image_size ('comic-banner', $mp_options['banner_width'], $mp_options['banner_height'], true);

        /*
         * For creating new comic page sizes for use with the_post_thumbnail().
         * Theme must have post thumbnails enabled.
         */
        if ($mp_options['generate_comic_page'])
            add_image_size ('comic-page', $mp_options['comic_width'], $mp_options['comic_height'], false);

        /*
         * For side-bar image.
         */
        add_image_size('comic-sidebar-image', 150, 150, true);

    }
    /**
     * Wrapper method for Manga_Press_Setup::activate()
     *
     * @return void
     */
    function activate(){
        //
        // First, let's check if we're upgrading or installing a new version
        $this->install->activate();

        if (current_theme_supports('post-thumbnails'))
            add_option('mangapress_thumbnails_updated', 'no', '', 'no');

    }
    /**
     * Wrapper method for Manga_Press_Setup::deactivate()
     *
     * @return void
     */
    function deactivate() {
        $this->install->deactivate();
    }
    /**
     * admin_menu()
     *
     * Handles the loading of needed custom admin pages.
     *
     * @since 2.6
     *
     * @return void
     */
    public function admin_menu() {
        global $mp_options;

        add_options_page(
            __("Manga+Press",'mangapress'),
            __("Manga+Press",'mangapress'),
            'manage_options',
            'mangapress-options',
            array(&$this, 'page_options')
        );

        if (get_option('mangapress_partial_upgrade') == 'yes') {
            add_plugins_page(
                    __("Manga+Press", 'mangapress'),
                    __("Manga+Press", 'mangapress'),
                    'manage_options',
                    'mangapress-upgrade-help',
                    array(&$this, 'upgrade_help')
            );
        }

        if (current_theme_supports('post-thumbnails')
                && get_option('mangapress_thumbnails_updated') == 'no') {
            
            add_submenu_page(
                    'edit.php?post_type=comic',
                    'Update Comic Thumbnails',
                    'Comic Thumbnails',
                    'manage_options',
                    'comic-thumbnails',
                    array(&$this, 'update_comic_thumbnails')
            );
        }

    }

    public function update_comic_thumbnails(){

        include_once('includes/mangapress-update-thumbnails.php');
    }


    /**
     *
     */
    public function page_options() {
        global $mp_options;

        if ( ! current_user_can('manage_options') )
            wp_die(
                __('You do not have sufficient permissions to manage options for this blog.',
                'mangapress')
            );
        
        include_once('includes/mangapress-options.php');

    }

    /**
     *
     */
    public function upgrade_help()
    {
        
    }
    /**
     * options_init()
     *
     * Registers Manga+Press settings
     *
     * @since 2.6b
     * 
     * @return void
     */
    public function options_init(){
        // Adding new options...
        register_setting(
            'mangapress-options',
            'mangapress_options',
            array(&$this, 'update_options')
        );
    }
    /**
     * Updates multiple options from page-comic-options.php
     *
     * @since 2.6
     * @deprecated possibly deprecated in 2.7 since WordPress 3.0 doesn't seem to use the callback for register_setting()
     *
     * Originally update_options. Was modified and renamed in Manga+Press 2.6
     *
     * @global array $mp_options
     * @param array $options
     * @return string
     */
    function update_options($options){
    global $mp_options;

            unset( $mp_options );

            // validate string options...
            $nav_css_values = array( 'default_css', 'custom_css');
            $order_by_values = array( 'post_date', 'ID' );
            //
            // if the value of the option doesn't match the correct values in the array, then
            // the value of the option is set to its default.
            in_array( $options['nav_css'], $nav_css_values ) ?
                    $mp_options['nav_css'] = strval( $options['nav_css'] )
                    : $mp_options['nav_css'] = 'default_css';

            in_array( $options['order_by'], $order_by_values ) ? 
                    $mp_options['order_by'] = strval( $options['order_by'] )
                    : $mp_options['order_by'] = 'post_date';
            //
            // Converting the values to their correct data-types should be enough for now...
            $mp_options['insert_nav']           =	intval( $options['insert_nav'] );
            $mp_options['group_comics']         =	intval( $options['group_comics'] );
            $mp_options['latestcomic_page']		=	intval( $options['latestcomic_page'] );
            $mp_options['comic_archive_page']	=	intval( $options['comic_archive_page'] );
            $mp_options['make_thumb']           =	intval( $options['make_thumb'] );
            $mp_options['insert_banner']		=	intval( $options['insert_banner'] );
            $mp_options['banner_width']         =	intval( $options['banner_width'] );
            $mp_options['banner_height']		=	intval( $options['banner_height'] );
            $mp_options['twc_code_insert']		=	intval( $options['twc_code_insert'] );
            $mp_options['oc_code_insert']		=	intval( $options['oc_code_insert'] );
            $mp_options['oc_comic_id']          =	intval( $options['oc_comic_id'] );
            $mp_options['comic_post_count']     =   intval( $options['comic_post_count']);
            $mp_options['generate_comic_page']  =   intval( $options['generate_comic_page']);
            $mp_options['comic_width']          =   intval( $options['comic_width']);
            $mp_options['comic_height']         =   intval( $options['comic_height']);
            
            return $mp_options;

    }

    /**
     * add_header_info(). Called by:	wp_head()
     *
     * @link http://codex.wordpress.org/Hook_Reference/wp_head
     * @since	0.5b
     *
     */
    function add_header_info() {
            echo "<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
    }
    /**
     * Filters the Latest Comic Page set in Manga+Press options.
     *
     * @since 2.5
     *
     * @global array $mp_options
     * @global object $wp
     * @global object $wpdb
     * @global object $wp_rewrite
     * @param string $content
     * @return string
     */
    function filter_latest_comicpage($content) {
        global $mp_options, $wp, $wpdb, $wp_rewrite, $id;
        
        /*
         * Grab the current Latest Comic Page.
         */
        $page = get_page( $mp_options['latestcomic_page'] );

        /*
         * This section handles support for home pages.
         */
        if ( get_option('show_on_front') == 'page' && is_front_page() ) {
            $front_page_id = get_option('page_on_front');
            $front_page = get_page( $front_page_id );
            $comic_page = $front_page->post_name;
        } else {
            $comic_page = @$wp->query_vars['pagename'];
        }

        /*
         * Now, let's start adding our content to the page.
         */
        if ( $comic_page === $page->post_name ) {
            $start = '';
            $end = '';
            $nav = '';
            $ptitle = '';
            $twc_code = '';

            $latest = $this->comics->last_comic;
            $posts = $this->comics->all_comics;

            $c = count( $posts ) - 1;
            $post = $posts[$c];
            
            $nav = $this->comics->comic_navigation($latest, $posts);

            setup_postdata( $post );
            $ptitle = '<h2 class="comic-title"><a href="'.get_permalink( $post->ID ).'" rel="bookmark" title="permalink to '.$post->post_title.'">'.$post->post_title.'</a></h2>';
            /*
             * If OnlineComics PageScan code is enabled...
             */
            if ($mp_options['oc_code_insert']) {
                $start = "\n<!-- OnlineComics.net ".$mp_options['oc_comic_id']." start -->\n";
                $end = "\n<!-- OnlineComics.net ".$mp_options['oc_comic_id']." end -->\n";
            }
            /*
             * If TWC.com update code is enabled...
             */
            if ($mp_options['twc_code_insert'])
                $twc_code = "\n<!--Last Update: ".date('d/m/Y', strtotime($post->post_date))."-->\n";

            if ($mp_options['generate_comic_page']) {
                $content = $twc_code.$start.$ptitle.$nav.  get_the_post_thumbnail( $post->ID, 'comic-page' ) .$end;
            } else {
                $content = $twc_code.$start.$ptitle.$nav.$post->post_content.$end;
            }

            $content = apply_filters('latest_comic_page', $content);

            wp_reset_query();
        }
        
        return $content;
    }
    /**
     * comic_insert_navigation()
     *
     * Inserts comic navigation at the beginning of The Loop. Hooked to loop_start
     *
     * @since 2.5
     *
     * @global object $post Wordpress post object.
     * @global int $id Post ID. Not used.
     * @global int $cat Category ID. Not used.
     * @global array $mp_options Array containing Manga+Press options.
     */
    function insert_comic_navigation() {
        global $post, $id, $cat, $mp_options, $wp;

        if ( is_comic() && (!is_category() && !is_front_page() && !is_archive()) ) {

            $comics = $this->comics->get_all_comics($post->ID);
            echo $this->comics->comic_navigation($post->ID, $comics);

        }

    }

    /**
     * comic_insert_banner()
     *
     * Inserts comic banner at the start of The Loop on the home page.
     * Hooked to loop_start.
     *
     * @since 2.5
     */
    function insert_banner() {
        
        if ( is_home() || is_front_page() ){
            get_latest_comic_banner(true);
        }
    }
    /**
     * filter_comic_archivepage()
     *
     * Makes changes to the_content() for Comic Archive Page. Hooked to the_content().
     *
     * @since 2.6
     *
     * @global object $wp Global WordPress query object.
     * @global array $mp_options Array containing Manga+Press options.
     * @param string $content
     * @return string $content
     */
    function filter_comic_archivepage($content){
            global $mp_options, $wp, $paged;
            
            $page = get_page( $mp_options['comic_archive_page'] );
            if ( @$wp->query_vars['pagename'] === $page->post_name ) {
                    $parchives = '';
                    if ($mp_options['twc_code_insert']) {
                            //var_dump($this->comics->get_last_comic());
                            $recent_post_date = get_post_field('post_date', $this->comics->last_comic);
                            //$recent_post = get_post( $this->comics->last_comic );
                            //setup_postdata( $recent_post );
                            $parchives = "\n<!--Last Update: ".date('d/m/Y', strtotime($recent_post_date))."-->\n";
                    }
                    /*
                     * Grab all available comic posts...
                     * Yes, this is sort of a "mini Loop"
                     */
                    $args = array( 
                            'showposts' => $mp_options['comic_post_count'],
                            'post_type' => 'comic',
                            'orderby'   => 'post_date',
                            'paged'     => $paged
                        );
                    
                    $posts = get_posts( $args );
                    
                    if ( !empty( $posts ) ) :

                            $parchives .= "<ul class=\"comic-archive-list\">\n";

                            $c = 0;
                            foreach( $posts as $post) :	setup_postdata( $post );

                                    $c++;
                                    $parchives .= "\t<li class=\"list-item-$c\">".date('m-d-Y', strtotime( $post->post_date ) )." <a href=\"".get_permalink( $post->ID )."\">$post->post_title</a></li>\n";

                            endforeach;

                            $parchives .= "</ul>\n";

                    else:

                            $parchives = __("No comics found", 'mangapress');

                    endif;                    

                    $content = $parchives;                    
            }
            

            return $content;

    }
    /**
     * comic_insert_twc_update_code()
     *
     * Inserts a Last Update html comment at the start of The Loop on the either
     * the home page, the main comic page or the archive page. Hooked to loop_start.
     *
     * @since 2.5
     * @version 1.0
     */
    function insert_twc_update_code() {

            if ( is_home() || is_front_page() ){

                    $latest = $this->comics->last_comic;
                    $post_latest = get_post( $latest );
                    echo "\n<!--Last Update: ".date('d/m/Y', strtotime($post_latest->post_date))."-->\n";

            }
    }
}

?>