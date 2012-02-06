<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage MangaPress_Bundled_Theme_Sidebar
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
?>
<div id="sidebar">
    <ul>

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar') ) : ?>

        <li id="categories">
            <h3>Categories:</h3>
            <ul>
                <?php wp_list_categories(array('title_li'=>'')); ?>
            </ul>
        </li>
        
        <?php endif; ?>

    </ul>
</div> <!-- #sidebar -->