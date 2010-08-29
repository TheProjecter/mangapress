<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Constants
 * @since 2.5
*/
$plugin_folder = str_replace('/includes','', plugin_basename( dirname(__FILE__) ) ); // remove /includes from the path structure

if (!defined('MP_VERSION')) define('MP_VERSION',	'3.0-beta');
if (!defined('MP_FOLDER')) define('MP_FOLDER', $plugin_folder );
if (!defined('MP_DOMAIN')) define('MP_DOMAIN', 'mangapress' );
if (!defined('MP_ABSPATH')) define('MP_ABSPATH', WP_CONTENT_DIR.'/plugins/'.$plugin_folder.'/' );
if (!defined('MP_URLPATH')) define('MP_URLPATH', WP_CONTENT_URL.'/plugins/'.$plugin_folder.'/' );

?>