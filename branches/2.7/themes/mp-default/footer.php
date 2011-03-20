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
 */
?>
        <div id="footer">
            <ul>
                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer') ) : ?>

                <?php endif;?>
            </ul>
            
        </div>
        <span class="clearfix"></span>
    </div>
    <?php wp_footer();?>
</body>
</html>