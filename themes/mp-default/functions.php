<?php
/**
 * @package Manga_Press_Templates
 * @subpackage Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
add_action('admin_init', 'disable_options_init');

function disable_options_init() {
    add_action('mangapress_option_fields', 'disable_options');
}

function disable_options($options)
{
    // we're specifically looking for navigation...
    if (isset($options['nav']['insert_nav'])) {        
        unset($options['nav']['insert_nav']);
    }
    
    return $options;
    
}

add_action('wp_enqueue_scripts', 'mangapress_theme_load_twentyeleven_css');
/**
 * Load the stylesheet from the TwentyEleven Theme
 * @return void
 */
function mangapress_theme_load_twentyeleven_css()
{
    $src = WP_CONTENT_URL . '/themes/twentyeleven/style.css';
    wp_register_style('twentyeleven', $src, null, MP_VERSION);
    
    wp_enqueue_style('twentyeleven');
}