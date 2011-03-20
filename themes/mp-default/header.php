<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage MangaPress_Bundled_Theme_Header
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php bloginfo('title') ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="wrapper">
        <div id="header">
            <h1><?php bloginfo('title') ?><span class="description"><?php bloginfo('description') ?></span></h1>
        </div>
        <?php wp_nav_menu(array('menu' => 'main', 'container_class' => 'menu')); ?>