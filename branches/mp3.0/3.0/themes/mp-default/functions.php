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

    }
    
}
?>
