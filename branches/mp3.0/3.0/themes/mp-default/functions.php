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
        //define( 'HEADER_TEXTCOLOR', '' );
        // No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
        define( 'HEADER_IMAGE', '' );

        // The height and width of your custom header. You can hook into the theme's own filters to change these values.
        // Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
        define( 'HEADER_IMAGE_WIDTH', apply_filters( 'mp_default_header_image_width', 960 ) );
        define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'mp_default_header_image_height', 200 ) );

        // Add a way for the custom header to be styled in the admin panel that controls
        // custom headers. See twentyten_admin_header_style(), below.
        add_custom_image_header(array(&$this, 'header_style'), array(&$this, 'admin_header_style') );

        // array value => description
        $this->fonts = array(
            'times'     => '"Times New Roman", Georgia, serif',
            'georgia'   => 'Georgia, "Times New Roman", serif',
            'palatino'  => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
            'book'      => '"Book Antiqua", "Palatino Linotype", Palatino, serif',
            'arial'     => 'Arial, Tahoma, Helvetica, sans-serif',
            'verdana'   => 'Verdana, Tahoma, sans-serif',
            'tahoma'    => 'Tahoma, Arial, Helvectica, sans-serif',
            'trebuchet' => '"Trebuchet MS", Arial, sans-serif',
            'black'     => 'Arial Black, Impact, Gadget, sans-serif',
            'impact'    => 'Impact, Charcoal, sans-serif',
            'user'      => '',
        );

        add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    public function header_style() {
        $hidetext = get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR );
    ?>
    <style type="text/css">
        h1#header {
            width: <?php echo HEADER_IMAGE_WIDTH ?>!important;
            height: <?php echo HEADER_IMAGE_HEIGHT ?>!important;
            <?php if ($hidetext == 'blank'): ?>
            text-indent: -9999px;
            <?php else: ?>
            color: #<?php echo $hidetext; ?>;
            <?php endif;?>
        }
    </style>
    <?php
    }

    public function admin_header_style() {
        
    }

    public function admin_menu() {

        $fonts_page
            = add_theme_page(
                    'M+P Theme Options',
                    'Manga+Press Theme Options',
                    'administrator',
                    'mangapress-theme',
                    array(&$this, 'page_fonts')
            );

    }
    public function page_fonts() {
        include_once('admin/page-fonts.php');
    }
}
?>
