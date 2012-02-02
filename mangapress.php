<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.jes.gs/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.jes.gs/">Manga+Press</a> for more info.
 Version: 2.7-beta
 Author: Jessica Green
 Author URI: http://www.jes.gs
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

global $mp;

add_action('init', array('MangaPress_Bootstrap', 'init'));

class MangaPress_Bootstrap
{
    /**
     * Static function used to initialize Bootstrap
     * 
     * @return void 
     */
    public static function init()
    {
        global $mp;
        
        register_activation_hook(__FILE__, array('MangaPress_Bootstrap', 'activate'));
        register_deactivation_hook(__FILE__, array('MangaPress_Bootstrap', 'deactivate'));
        register_theme_directory('plugins/' . basename(dirname(__FILE__)) . '/themes');
        
        $mp = new MangaPress_Bootstrap();
    }
    
    /**
     * Static function for plugin activation.
     * 
     * @return void 
     */
    public static function activate()
    {
        
    }
    
    /**
     * Static function for plugin deactivation.
     * 
     * @return void 
     */
    public static function deactivate()
    {
        
    }
    /**
     * 
     * @return void 
     */
    public function __construct()
    {
        
    }
    
}