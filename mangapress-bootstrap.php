<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bootstrap
 * @version 2.7
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
add_action('setup_theme', array('Manga_Press_Loader', 'load_theme_dir'));
add_action('init', array('Manga_Press_Loader', 'init'));

class Manga_Press_Loader
{
    public static function init()
    {
        global $mp;

        $mp = new Manga_Press();

    }
    
    public static function load_theme_dir()
    {
        register_theme_directory('plugins/' . basename(dirname(__FILE__)) . '/themes');
    }
}
