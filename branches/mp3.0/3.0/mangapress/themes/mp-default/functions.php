<?php
/**
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
global $mp_theme;

add_theme_support( 'nav-menus' );

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

add_theme_support( 'custom-header' );

add_theme_support( 'custom-background' );

add_action( 'init', create_function('$mp_theme', '$mp_theme = new MP_Bundled_Theme_Functions();') );

class MP_Bundled_Theme_Functions {

    public function  __construct() {
        
        register_sidebar(
                array(
                    'name' => 'Sidebar',
                    'id'   => 'sidebar',
                )
        );

        register_sidebar(
                array(
                    'name' => 'Footer',
                    'id'   => 'footer',
                )
        );

       // This theme allows users to set a custom background
        add_custom_background();

        // Your changeable header business starts here
        define( 'HEADER_TEXTCOLOR', '' );
        // No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
        define( 'HEADER_IMAGE', '' );

        // The height and width of your custom header. You can hook into the theme's own filters to change these values.
        // Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
        define( 'HEADER_IMAGE_WIDTH', apply_filters( 'mp_default_header_image_width', 960 ) );
        define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'mp_default_header_image_height', 200 ) );

        // We'll be using post thumbnails for custom header images on posts and pages.
        // We want them to be 940 pixels wide by 198 pixels tall.
        // Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
        set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

        // Don't support text inside the header image.
        define( 'NO_HEADER_TEXT', false );

        // Add a way for the custom header to be styled in the admin panel that controls
        // custom headers. See twentyten_admin_header_style(), below.
        add_custom_image_header(
                array(&$this, 'header_style'),
                array(&$this, 'admin_header_style')
        );

        // ... and thus ends the changeable header business.

        // Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
        //register_default_headers($headers);


    }
 
    public function header_style() {
    ?>
        <style type="text/css">
            h1#header {
                width: <?php echo HEADER_IMAGE_WIDTH; ?>!important;
                height: <?php echo HEADER_IMAGE_HEIGHT ?>!important;
                text-indent: -9999px;
            }
        </style>        
    <?php
    }

    public function admin_header_style() {
        
    }
    
}
?>
