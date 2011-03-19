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
 *
 * @todo Update phpDocumentor function/class headers
 */
global $mp_theme;

add_theme_support( 'nav-menus' );

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

add_theme_support( 'custom-header' );

add_theme_support( 'custom-background' );

add_action( 'init', create_function('$mp_theme', '$mp_theme = new MP_Bundled_Theme_Functions();') );


class MP_Bundled_Theme_Functions {

    private $_theme_options;

    public $fonts = array();
    
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

        register_nav_menu( 'main', 'Manga+Press Main Navigation' );

        $src = get_template_directory_uri() . '/css/header-style.css';
        wp_register_style('header-style', $src, null, MP_VERSION, 'screen');
        
        // This theme allows users to set a custom background
        add_custom_background();

        $this->_theme_options = $this->get_theme_options();

        // Your changeable header business starts here
        define( 'HEADER_TEXTCOLOR', '' );

        // No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
        define( 'HEADER_IMAGE', '' );

        // The height and width of your custom header. You can hook into the theme's own filters to change these values.
        // Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
        define( 'HEADER_IMAGE_WIDTH', apply_filters( 'mp_default_header_image_width', 960 ) );
        define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'mp_default_header_image_height', 200 ) );

        // Add a way for the custom header to be styled in the admin panel that controls
        // custom headers. See twentyten_admin_header_style(), below.
        add_custom_image_header(array(&$this, 'header_style'), array(&$this, 'admin_header_style') );
//        register_default_headers(
//            array(
//                'noimage' => array(
//                    'url'           => '',
//                    'thumbnail_url' => '',
//                    'description'   => 'No Image',
//                ),
//            )
//        );
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
        );

        define( 'HEADER_FONT', $this->fonts[$this->_theme_options['header-font']] );
        define( 'HEADER_COLOR', $this->_theme_options['header-color'] );
        define( 'BODY_FONT', $this->fonts[$this->_theme_options['body-font']] );
        define( 'BODY_COLOR', $this->_theme_options['body-color'] );
        define( 'LINK_COLOR', $this->_theme_options['link-color'] );
        define( 'VLINK_COLOR', $this->_theme_options['vlink-color'] );
        define( 'HLINK_COLOR', $this->_theme_options['hlink-color'] );
        define( 'ALINK_COLOR', $this->_theme_options['alink-color'] );

        add_action('admin_menu', array(&$this, 'admin_menu'));

	$theme = get_option( 'stylesheet' );        
        add_action("update_option_theme_mods_$theme", array(&$this, 'update_css_files'), 100);
    }

    public function header_style()
    {
        wp_print_styles(array('header-style'));
    }

    public function admin_header_style()
    {
        
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
        add_action("admin_print_scripts-$fonts_page", array(&$this, 'fonts_page_print_scripts'));
        add_action("admin_print_styles-$fonts_page", array(&$this, 'fonts_page_print_styles'));

    }
    
    function fonts_page_print_styles()
    {

        wp_enqueue_style('farbtastic12', get_template_directory_uri().'/css/farbtastic12.css', false, '1.2', 'screen');
        
    }

    function fonts_page_print_scripts()
    {

        wp_enqueue_script('farbtastic12', get_bloginfo('template_url').'/js/farbtastic.js', false, '1.2');

    }

    public function page_fonts()
    {
        include_once('admin/page-fonts.php');
    }

    public function set_theme_options($options)
    {
        
        if (wp_verify_nonce($options['_wp_nonce'], 'mangapress-theme-options')) {
            // let's validate before we stuff them into the DB
            $body_font = strval($options['mp_theme_opts']['body-font']);
            $new_opts['body-font'] = $body_font = array_key_exists($body_font, $this->fonts) ?
                                                $body_font : 'arial';

            $body_color = strval($options['mp_theme_opts']['body-color']);
            $new_opts['body-color'] = $body_color = $this->_validate_color_val($body_color) ?
                                $body_color : '#000000';

            $header_font = $options['mp_theme_opts']['header-font'];
            
            $new_opts['header-font'] = $header_font = array_key_exists($header_font, $this->fonts) ?
                                        $header_font : 'book';

            $header_color = $options['mp_theme_opts']['header-color'];
            $new_opts['header-color'] = $header_color = $this->_validate_color_val($header_color) ?
                                $header_color : '#000000';

            $link_color = $options['mp_theme_opts']['link-color'];
            $new_opts['link-color'] = $link_color = $this->_validate_color_val($link_color) ?
                                        $link_color : '#0000FF';

            $vlink_color = $options['mp_theme_opts']['vlink-color'];
            $new_opts['vlink-color'] = $vlink_color = $this->_validate_color_val($vlink_color) ?
                                        $vlink_color : '#00CCFF';

            $hlink_color = $options['mp_theme_opts']['hlink-color'];
            $new_opts['hlink-color'] = $hlink_color = $this->_validate_color_val($hlink_color) ?
                                        $hlink_color : '#FF0000';

            $alink_color = $options['mp_theme_opts']['alink-color'];
            $new_opts['alink-color'] = $alink_color = $this->_validate_color_val($alink_color) ?
                                        $alink_color : '#FFFFFF';

            // Now we can stuff them into the DB
            set_theme_mod('body_font', $body_font);
            set_theme_mod('body_color', $body_color);
            set_theme_mod('header_font', $header_font);
            set_theme_mod('header_color', $header_color);
            set_theme_mod('link_color', $link_color);
            set_theme_mod('vlink_color', $vlink_color);
            set_theme_mod('hlink_color', $hlink_color);
            set_theme_mod('alink_color', $alink_color);

            return $new_opts;
        } else {
            return false;
        }
    }

    public function get_theme_options()
    {

        $options['body-font'] = get_theme_mod('body_font', 'arial');
        $options['header-font'] = get_theme_mod('header_font', 'book');
        $options['body-color'] = get_theme_mod('body_color', '#000000');
        $options['header-color'] = get_theme_mod('header_color', '#000000');
        $options['link-color'] = get_theme_mod('link_color', '#0000FF');
        $options['vlink-color'] = get_theme_mod('vlink_color', '#00CCFF');
        $options['hlink-color'] = get_theme_mod('hlink_color', '#FF0000');
        $options['alink-color'] = get_theme_mod('alink_color', '#FFFFFF');

        return $options;
    }

    public function update_css_files()
    {
        
        $args = get_theme_mods();
        
        $image_width  = HEADER_IMAGE_WIDTH . 'px';
        $image_height = HEADER_IMAGE_HEIGHT . 'px';

        if (!empty($args['header_image'])) {
            $header = 'background: url("' . $args['header_image'] . '") no-repeat;';
        } else {
            $header = '';
        }
        $generated_on = date('c');
$css = <<<CSS
/*
    Generated By : Manga+Press Default Theme
    On           : $generated_on
*/
div#header {
    width: $image_width;
    height: $image_height;
    $header
CSS;

if ($args['header_textcolor'] == 'blank' || $args['header_textcolor'] == '') {
$css .=  <<<CSS

    text-indent: -9999px;
    overflow: hidden;
}

CSS;
} else {
$css .= <<<CSS
}

div#header h1 {
    color: #{$args['header_textcolor']};
    font-size: 1.5em;
}
CSS;
}

$css .= <<<CSS

h1, h2, h3, h4, h5, h6 {
    font-family: {$this->fonts[$header_font]};
    color: {$args['header_color']};
}

body {
    font: 16px {$this->fonts[$args['body_font']]};
    color: {$args['body_color']};
    $background
}
a { color: {$args['link_color']};}
a:visited { color: {$args['vlink_color']};}
a:hover { color: {$args['hlink_color']};}
a:active { color: {$args['alink_color']};}
CSS;
        $header_style_src = get_template_directory() . '/css/header-style.css';
        
        file_put_contents($header_style_src, $css);
        
    }

    /**
     *
     * @param <type> $value
     * @return <type>
     */
    private function _validate_color_val($value)
    {
        return (bool)preg_match('/^#?([0-9a-f]{1,2}){3}$/i', $value);
    }
}
