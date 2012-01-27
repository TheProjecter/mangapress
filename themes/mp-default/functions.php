<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage MangaPress_Bundled_Theme_Functions
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 *
 * @todo Add Int8 support
 */
global $mp_theme, $theme_dir;

$theme_dir = basename(dirname(__FILE__));

add_theme_support( 'nav-menus' );

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

add_theme_support( 'custom-header' );

add_theme_support( 'custom-background' );

add_action( 'init', create_function('$mp_theme', '$mp_theme = new MP_Bundled_Theme_Functions();') );

/**
 * Handles output of alternate post thumbnail when none exists.
 * 
 * @param int $id WordPress post ID.
 * 
 * @global object $post
 * @global array $_wp_additional_image_sizes
 * 
 * @return string
 */
function the_comic_thumbnail($id = 0)
{
    if (!$id) {
        global $post;
        $id = $post->ID;
    }

    $args = array(
        'post_parent' => $id,
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
    );

    $attachment = new WP_Query($args);

    global $_wp_additional_image_sizes;

    if (!empty($_wp_additional_image_sizes['comic-page'])) {
        $size = array($_wp_additional_image_sizes['comic-page']['width'], $_wp_additional_image_sizes['comic-page']['height']);
        // for "Just In Time" filtering of all of wp_get_attachment_image()'s filters
        do_action( 'begin_fetch_post_thumbnail_html', $id , $attachment->post->ID, $size );
        $html = wp_get_attachment_image( $attachment->post->ID, $size, false, $attr );
        do_action( 'end_fetch_post_thumbnail_html', $id , $attachment->post->ID, $size );

        echo apply_filters( 'post_thumbnail_html', $html, $id , $attachment->post->ID, $size, $attr );
    } else {
        $img = wp_get_attachment_image_src($attachment->post->ID, 'full');

        $ratio = round($img[2] / $img[1], 3);
        $column_width = 600; // since our content-area is 640 pixels
        $height       = $column_width * $ratio;
        $size = array($column_width, $height, false);

        do_action( 'begin_fetch_post_thumbnail_html', $id , $attachment->post->ID, $size );
        $html = wp_get_attachment_image( $attachment->post->ID, $size, false, $attr );
        do_action( 'end_fetch_post_thumbnail_html', $id , $attachment->post->ID, $size );

        echo apply_filters( 'post_thumbnail_html', $html, $id, $attachment->post->ID, $size, $attr );

    }
}

/**
 * @package MangaPress_Bundled_Theme
 * @subpackage MangaPress_Bundled_Theme_Functions
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
class MP_Bundled_Theme_Functions
{

    /**
     * Theme options array.
     *
     * @var array
     */
    private $_theme_options = array();

    /**
     * Available fonts array
     *
     * @var array
     */
    public $fonts = array();

    /**
     * PHP5 Constructor method. Initializes the class
     *
     * @return void
     */
    public function  __construct() {
        
        load_plugin_textdomain($theme_dir, false, $theme_dir . '/lang');
        
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

        add_action('admin_menu', array(&$this, 'admin_menu'), 5);

	$theme = get_option( 'stylesheet' );
        
    }

    /**
     * Prints out the <link> reference to the header-style.css file.
     * @return void
     */
    public function header_style()
    {
        wp_print_styles(array('header-style'));
    }

    /**
     * Needed for add_custom_image_header()
     *
     * @return void
     */
    public function admin_header_style()
    {

    }

    /**
     * Adds the menu page for Theme Options, and sets up the enqueuing of
     * scripts and styles for the Theme Options page.
     *
     * @return void
     */
    public function admin_menu() {

        $options_page
            = add_theme_page(
                    'M+P Theme Options',
                    'Manga+Press Theme Options',
                    'administrator',
                    'mangapress-theme',
                    array(&$this, 'page_fonts')
            );
        
        add_action("admin_print_scripts-{$options_page}", array(&$this, 'print_scripts'));
        add_action("admin_print_styles-{$options_page}", array(&$this, 'print_styles'));
        
        add_action("admin_init", array(&$this, 'admin_init'));

    }
    
    public function admin_init()
    {
        
        // Adding new options...
        register_setting(
            "theme_mods_mp-default",
            'theme_mods_mp-default',
            array(&$this, 'update_options')
        );
        
        /* 
         * Settings Section...
         * First, fonts...
         */        
 	add_settings_section(
            'theme_mods_mp-default_body',
            'Manga+Press Theme Body/Header Fonts and Colors',
            array(&$this, 'settings_section_cb'),
            'mangapress-theme'
        );
        
        // Now, colors...
 	add_settings_section(
            'theme_mods_mp-default_link_colors',
            'Manga+Press Theme Link Colors',
            array(&$this, 'settings_section_cb'),
            'mangapress-theme'
        );
        
        $this->output_settings_fields();
        
    }
    
    public function settings_section_cb($section)
    {
        $sections = $this->options_sections();
        
        echo "<p>{$sections[$section['id']]['description']}</p>";
    }

    
    public function output_settings_fields()
    {
        
        $field_sections = $this->options_fields();
        $sections       = $this->options_sections();
                
        foreach ($sections as $section => $data) {
            
            foreach ($field_sections[$section] as $field_name => $field) {

                add_settings_field(
                    "{$section}-options-{$field['id']}",
                    (isset($field['title']) ? $field['title'] : " "),
                    $field['callback'],
                    'mangapress-theme',
                    $section,
                    array_merge(array('name' => $field_name, 'section' => $section), $field)
                );

            }
        }
    }
    
    public function options_fields($options = array())
    {
        $options = array(
            'theme_mods_mp-default_body' => array(
                'header_font' => array(
                    'id'    => 'header-font',
                    'title' => __('Header Font', $theme_dir),
                    'type'  => 'select',
                    'valid' => $this->fonts,
                    'default' => 'times',
                    'callback' => array(&$this, 'settings_field_cb'),                    
                ),
                'header_color' => array(
                    'id'    => 'header-color',
                    'title' => __('Header Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#000000',
                    'callback' => array(&$this, 'settings_field_cb')
                ),
                'body_font'  => array(
                    'id'    => 'body-font',
                    'title' => __('Body Font', $theme_dir),
                    'type'  => 'select',
                    'valid' => $this->fonts,
                    'default' => 'arial',
                    'callback' => array(&$this, 'settings_field_cb'),                    
                ),
                'body_color' => array(
                    'id'    => 'body-color',
                    'title' => __('Body Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#000000',
                    'callback' => array(&$this, 'settings_field_cb')                    
                ),
            ),
            'theme_mods_mp-default_link_colors' => array(
                'link_color' => array(
                    'id'    => 'link-color',
                    'title' => __('Link Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#FF0000',
                    'callback' => array(&$this, 'settings_field_cb')                    
                ),
                'vlink_color' => array(
                    'id'    => 'vlink-color',
                    'title' => __('Visited Link Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#FFCC99',
                    'callback' => array(&$this, 'settings_field_cb')                    
                ),
                'hlink_color' => array(
                    'id'    => 'hlink-color',
                    'title' => __('Hover Link Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#AA6699',
                    'callback' => array(&$this, 'settings_field_cb')                    
                ),
                'alink_color' => array(
                    'id'    => 'alink-color',
                    'title' => __('Active Link Color', $theme_dir),
                    'type'  => 'text',
                    'valid' => '/^#?([0-9a-f]{1,2}){3}$/i',
                    'default' => '#AAAAFF',
                    'callback' => array(&$this, 'settings_field_cb')                    
                ),
            )
        );
        
        return $options;
    }

    public function options_sections($sections = array())
    {
        $sections = array(
            'theme_mods_mp-default_body' => array(
                'title'       => 'Theme Body Styles',
                'description' => __('Set font styles and colors for body text and header text.', $theme_dir),
            ),
            
            'theme_mods_mp-default_link_colors' => array(
                'title'       => 'Theme Link Colors',
                'description' => __('Set theme link colors.', $theme_dir),
            ),
        );
        
        return $sections;
    }

    public function settings_field_cb($option)
    {
        //global $mp_options;
        $options = $this->get_theme_options();
        $field_type  = isset($option['type']) ? $option['type'] : '';

        if ($field_type == 'text') {
        ?>
        
        <input type="text" 
               name="theme_mods_mp-default[<?php echo $option['section']; ?>][<?php echo $option['name']; ?>]" 
               id="<?php echo $option['id'] ?>" 
               value="<?php echo $options[$option['section']][$option['name']] ?>" />
        
        <?php
        } elseif ($field_type == 'checkbox') {                                       
            if (isset($option['description'])) {
                echo "<label for=\"{$option['id']}\">";
            } ?>
                           
        <input type="checkbox" 
               name="theme_mods_mp-default[<?php echo $option['section']; ?>][<?php echo $option['name']; ?>]" 
               id="<?php echo $option['id'] ?>" 
               value="1"
               <?php checked('1', 1, true) ?> />
        
        <?php
            if (isset($option['description'])) {
                echo $option['description'] . '</label>';
            }
            
        } elseif ($field_type == 'radio') {
            // should be an array of radio buttons
        } elseif ($field_type == 'select') {
        ?>
            <select name="theme_mods_mp-default[<?php echo $option['section']; ?>][<?php echo $option['name']; ?>]" id="<?php echo $option['id'] ?>">
            <?php foreach($option['valid'] as $value => $text) { 
                $selected = selected($value, $options[$option['section']][$option['name']], false);
            ?>
                <option value="<?php echo $value; ?>"<?php echo $selected ?>><?php echo $text; ?></option>
            <?php } ?>
            </select>        

        <?php
            if (isset($option['description'])) {
                echo "<span class=\"description\">{$option['description']}</span>";
            }
        } elseif ($field_type == 'textarea') {
            
        }
    }
    
    /**
     * Enqeues CSS files for the Theme Options page.
     *
     * @return void
     */
    function print_styles()
    {
        wp_enqueue_style(
            'page-fonts',
            get_template_directory_uri() . '/css/page-fonts.css',
            false,
            MP_VERSION,
            'screen'
        );

        wp_enqueue_style(
            'farbtastic12',
            get_template_directory_uri() . '/css/farbtastic12.css',
            false,
            '1.2',
            'screen'
        );

    }

    /**
     * Enqueues JS files for the Theme Options page.
     *
     * @return void
     */
    function print_scripts()
    {

        wp_enqueue_script('farbtastic12', get_bloginfo('template_url').'/js/farbtastic.js', false, '1.2');

    }

    /**
     * Loads the Theme Options page.
     *
     * @return void
     */
    public function page_fonts()
    {
        include_once('admin/theme-options.php');
    }

    /**
     * Handles validation and sanitization of theme options before adding to
     * the DB.
     *
     * @param array $options The theme options being passed.
     * @return array|bool Array containing new values, if successful. False if not.
     */
    public function update_options($options)
    {

        // let's validate before we stuff them into the DB
        $body_font = strval($options['theme_mods_mp-default_body']['body_font']);
        $new_opts['body_font']
                = array_key_exists($body_font, $this->fonts) ? $body_font : 'arial';

        $body_color = strval($options['theme_mods_mp-default_body']['body_color']);
        $new_opts['body_color']
            = $this->_validate_color_val($body_color) ? $body_color : '#000000';

        $header_font = $options['theme_mods_mp-default_body']['header_font'];
        $new_opts['header_font']
            = array_key_exists($header_font, $this->fonts) ? $header_font : 'book';

        $header_color = $options['theme_mods_mp-default_body']['header_color'];
        $new_opts['header_color']
            = $this->_validate_color_val($header_color) ? $header_color : '#000000';

        $link_color = $options['theme_mods_mp-default_link_colors']['link_color'];
        $new_opts['link_color'] 
            = $this->_validate_color_val($link_color) ? $link_color : '#0000FF';

        $vlink_color = $options['theme_mods_mp-default_link_colors']['vlink_color'];
        $new_opts['vlink_color']
            = $vlink_color = $this->_validate_color_val($vlink_color) ? $vlink_color : '#00CCFF';

        $hlink_color = $options['theme_mods_mp-default_link_colors']['hlink_color'];
        $new_opts['hlink_color']
            = $hlink_color = $this->_validate_color_val($hlink_color) ? $hlink_color : '#FF0000';

        $alink_color = $options['theme_mods_mp-default_link_colors']['alink_color'];
        $new_opts['alink_color']
            = $alink_color = $this->_validate_color_val($alink_color) ? $alink_color : '#FFFFFF';

        $this->_theme_options = $new_opts;

        return $this->_theme_options;

    }

    /**
     * Returns current theme options.
     *
     * @return array
     */
    public function get_theme_options()
    {

        $options['theme_mods_mp-default_body']['body_font']    = get_theme_mod('body_font', 'arial');
        $options['theme_mods_mp-default_body']['header_font']  = get_theme_mod('header_font', 'book');
        $options['theme_mods_mp-default_body']['body_color']   = get_theme_mod('body_color', '#000000');
        $options['theme_mods_mp-default_body']['header_color'] = get_theme_mod('header_color', '#000000');
        $options['theme_mods_mp-default_link_colors']['link_color']   = get_theme_mod('link_color', '#0000FF');
        $options['theme_mods_mp-default_link_colors']['vlink_color']  = get_theme_mod('vlink_color', '#00CCFF');
        $options['theme_mods_mp-default_link_colors']['hlink_color']  = get_theme_mod('hlink_color', '#FF0000');
        $options['theme_mods_mp-default_link_colors']['alink_color']  = get_theme_mod('alink_color', '#FFFFFF');

        return $options;
    }

    /**
     * Validates color values.
     *
     * @param string $value The color value being validated.
     * @return bool
     */
    private function _validate_color_val($value)
    {
        return (bool)preg_match('/^#?([0-9a-f]{1,2}){3}$/i', $value);
    }
}
