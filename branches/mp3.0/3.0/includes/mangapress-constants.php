<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Constants
 * @since 2.5
*/
$plugin_folder = str_replace('/includes','', plugin_basename( dirname(__FILE__) ) ); // remove /includes from the path structure

if (!defined('MP_VERSION')) define('MP_VERSION',	'3.0-beta');
if (!defined('MP_FOLDER')) define('MP_FOLDER', $plugin_folder );
if (!defined('MP_ABSPATH')) define('MP_ABSPATH', WP_CONTENT_DIR.'/plugins/'.$plugin_folder.'/' );
if (!defined('MP_URLPATH')) define('MP_URLPATH', WP_CONTENT_URL.'/plugins/'.$plugin_folder.'/' );
if (!defined('MP_CACHE_DIR')) define( 'MP_CACHE_DIR', WP_CONTENT_DIR.'/cache' );
if (!defined('MP_CACHE_URL')) define( 'MP_CACHE_URL', WP_CONTENT_URL.'/cache' );
if (!defined('CACHE_SIZE') ) define( 'CACHE_SIZE', 6 );		// number of files to store before clearing cache
if (!defined('CACHE_CLEAR') ) define( 'CACHE_CLEAR', 5 );		// maximum number of files to delete on each cache clear

?>