<?php

class Manga_Press_Setup {
    
    /**
     * Current Manga+Press version #
     *
     * @var string
     */
    public $version;

    /**
     * PHP5 Constructor function. Initializes the class.
     *
     * @global string $wp_version WordPress version
     */
    public function  __construct() {
        global $wp_version;

        if ( version_compare ($wp_version, '3.0', '<=')) {
            wp_die(
                  'Sorry, only WordPress 3.0 and later are supported.'
                . ' Please upgrade to WordPress 3.0', 'Wrong Version'
            );
        }
        
        $this->version = strval( get_option('mangapress_ver') );

        // version_compare will still evaluate against an empty string
        // so we have to tell it not to.
        if (version_compare($this->version, MP_VERSION, '<')
                && !($this->version == '')) {

            add_option( 'mangapress_upgrade', 'yes', '', 'no');
            
        } elseif ($this->version == '') {
            add_option( 'mangapress_new', 'yes', '', 'no');
        }

    }
    /**
     * mangapress_activate()
     *
     * @since 0.1b
     *
     * Manga+Press activation hook. Was originally webcomicplugin_activate()
     *
     * @return void
     *
     */
    function activate() {
        global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite, $installed_ver;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            // Check for capability
            if ( !current_user_can('activate_plugins') )
                wp_die( __('Sorry, you do not have suffient permissions to activate this plugin.', 'mangapress') );

            // Get the capabilities for the administrator
            $role = get_role('administrator');

            // Must have admin privileges in order to activate.
            if ( empty($role) )
                wp_die( __('Sorry, you must be an Administrator in order to use Manga+Press', 'mangapress') );

            
            if (get_option('mangapress_new') == 'yes')
                $this->set_default_options();

            $wp_rewrite->flush_rules();

    }

    /**
     * set_default_options()
     *
     * @since 2.6
     *
     * Sets default options if activation wasn't an upgrade or
     * copies old options over to new options if it is an upgrade.
     * Using the version number from the database, this function decides
     * what to do based on that version number.
     *
     * @return void
     *
     */
    function set_default_options() {

        // This handles new installs, which is usually the case if $installed_ver is empty
        // add comic options to database
        $mp_options['nav_css']              =	'default_css';
        $mp_options['order_by']             =	'post_date';
        $mp_options['insert_nav']           =	false;
        $mp_options['group_comics']         =	false; // New options in 3.0
        $mp_options['latestcomic_page']     =	0;
        $mp_options['comic_archive_page']	=	0;
        $mp_options['make_thumb']           =	false;
        $mp_options['insert_banner']        =	false;
        $mp_options['banner_width']         =	0;
        $mp_options['banner_height']        =	0;
        $mp_options['twc_code_insert']      =	false;
        $mp_options['oc_code_insert']       =	false;
        $mp_options['oc_comic_id']          =	0;
        $mp_options['comic_post_count']     =   10; // New option in 3.0
        $mp_options['comic_post_count']     =   10; // New option in 3.0
        $mp_options['generate_comic_page']  =   false; // New option in 3.0
        $mp_options['comic_width']          =   ''; // New option in 3.0
        $mp_options['comic_height']         =   ''; // New option in 3.0

        add_option('mangapress_ver',	MP_VERSION,	'', 'no');

        add_option( 'mangapress_options', serialize( $mp_options ), '', 'no' );

        delete_option('mangapress_new');
    }
    /**
     * mangapress_upgrade()
     *
     * @since 2.0 beta
     *
     * Handles the process of upgrading from previous versions by
     * deleting old options, and making any required changes to database schema.
     *
     * @return void
     */
    function do_upgrade() {
        global $mp_options, $wpdb, $wp_rewrite;

        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        check_admin_referer('mangapress-upgrade');

        $installed_ver = substr($this->version, 0, 3);

        $msg =   "Updgrading from Manga+Press version " . $installed_ver 
               . " to Manga+Press version " . MP_VERSION . "<br />";

        $wpdb->mpcomicseries	= $wpdb->prefix . 'comics_series';
        $wpdb->mpcomics         = $wpdb->prefix . 'comics';

        $msg .= __("Upgrading Manga+Press...<br />", 'mangapress');

        //
        // Remove the series table. This isn't used by Manga+Press 2.6 and newer
        if ($wpdb->get_var("show tables like '" . $wpdb->mpcomicseries . "'")
                == $wpdb->mpcomicseries) {

            $sql = $wpdb->prepare( "DROP TABLE " . $wpdb->mpcomicseries . ";" );
            $wpdb->query($sql);
            
            $msg .=  __("$wpdb->mpcomicseries has been removed.", 'mangapress')."<br />";
            $wpdb->flush();
            
        }
       
        //
        // Make changes to databases...
        // Manga+Press 2.7 eliminates the need for $wpdb->mpcomics        
        if( ($wpdb->get_var("show tables like '".$wpdb->mpcomics."'")
                == $wpdb->mpcomics) ) {
            
            $msg .=  __("Upgrading database...", 'mangapress')."<br />";
            $msg .=  __("Getting comic posts IDs from $wpdb->mpcomics...", 'mangapress')."<br />";

            $sql = 'SELECT post_id FROM ' . $wpdb->mpcomics;
            $ids = $wpdb->get_results($sql);

            $records = count($ids);

            $sqlLine = 'UPDATE ' . $wpdb->posts ." SET post_type='comics' WHERE ID='%s'";
            foreach($ids as $record) {
                $likeId[] = sprintf($sqlLine, $record->post_id);
            }
            
            $sql = implode(";\n", $likeId);
            var_dump($sql); die();
            //$wpdb->query($sql);

            $msg .=  __("Updating comic posts to new post-type...", 'mangapress')."<br />";

            //
            // now drop $wpdb->mpcomics, we don't need it anymore...
            $sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomics .";" );
            $wpdb->query($sql);

            $wpdb->flush(); // because we just did a lot of work with the database, lets flush the results cache...
            $msg .=  __("$wpdb->mpcomics has been removed.", 'mangapress')."<br />";
        }

        delete_option('mangapress_upgrade');
        update_option( 'mangapress_ver', MP_VERSION );

        $wp_rewrite->flush_rules();

        $msg .= __('Old options have been deleted from the database.', 'mangapress').'<br/>';
        $msg .= __("Manga+Press has been upgraded to ", 'mangapress').MP_VERSION."<br />";
        
        return $msg;
    }
    
    /**
     * mangapress_deactivate()
     *
     * @since 2.6
     *
     * Manga+Press deactivation hook. Does the clean-up after uninstall has run.
     *
     * @return void
     *
     */
    function deactivate(){
        global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $wp_rewrite->flush_rules();
            
    }

}

?>
