<?php
/**
 * @package Manga_Press
 * @subpackage Includes
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/**
 * Display Option Tabs section
 * Some plugin writers prefer to use echo statements to output the code for their options tab,
 * I prefer to create seperate files and use include statements. Is much neater that way!
 *
 * @package Includes
 * @subpackage Display_Option_Tabs
 * @since 0.5
 * @author Jess Green <jgreen@psy-dreamer.com>
 */ 
/**
 * Displays the M+P Options tab, which is located under Settings
 *
 * @global array $mp_options
 * @return void;
 */
function mangapress_options_page()
{
    global $mp_options;

    include(MP_ABSPATH . 'pages/options.php');
}

/**
 * Wrapper function to display upgrade page.
 * 
 * @global array $mp_options
 * @return void
 */
function upgrade_mangapress()
{
    global $mp_options;

    include(MP_ABSPATH . 'pages/upgrade.php');

}